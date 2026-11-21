<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MatchDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

class MatchService extends BaseService
{
    protected string $entityName = 'Match';
    protected array $fields = ['home_team_id', 'away_team_id', 'venue_id', 'match_date', 'home_score', 'away_score', 'status'];

    public const STATUSES = ['scheduled', 'completed', 'postponed', 'cancelled'];
    public const MAX_SCORE = 99;

    private $teamDao;
    private $venueDao;

    public function __construct($dao = null, $teamDao = null, $venueDao = null)
    {
        parent::__construct($dao ?? new MatchDao());
        $this->teamDao = $teamDao ?? new TeamDao();
        $this->venueDao = $venueDao ?? new VenueDao();
    }

    /** The matches of one team, home and away; 404 when the team does not exist. */
    public function get_by_team($teamId): array
    {
        Validator::integer($teamId, 'team_id', 1);
        if ($this->teamDao->get_by_id((int) $teamId) === null) {
            throw new NotFoundException('Team not found.');
        }

        return $this->dao->get_by_team((int) $teamId);
    }

    public function add(array $data): array
    {
        $data = $this->prepare($data);
        $this->validate($data);

        // Rule: a newly scheduled match must be in the future (an old match can still be edited)
        if ($data['status'] === 'scheduled') {
            Validator::in_future($data['match_date'], 'match_date');
        }

        $id = $this->dao->add($data);

        return $this->get_by_id($id);
    }

    protected function prepare(array $data): array
    {
        $data = parent::prepare($data);
        $data['status'] = $data['status'] ?? 'scheduled';

        // Accept the HTML input format 2026-11-20T18:00 and store 2026-11-20 18:00:00
        if (is_string($data['match_date'])) {
            $date = str_replace('T', ' ', $data['match_date']);
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $date)) {
                $date .= ':00';
            }
            $data['match_date'] = $date;
        }

        return $data;
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['home_team_id', 'away_team_id', 'venue_id', 'match_date']);
        $this->require_existing($this->teamDao, $data['home_team_id'], 'home_team_id', 'home team');
        $this->require_existing($this->teamDao, $data['away_team_id'], 'away_team_id', 'away team');
        $this->require_existing($this->venueDao, $data['venue_id'], 'venue_id', 'venue');
        Validator::datetime($data['match_date'], 'match_date');
        Validator::one_of($data['status'], 'status', self::STATUSES);

        // Rule: a team cannot play against itself
        if ((int) $data['home_team_id'] === (int) $data['away_team_id']) {
            throw new ValidationException('The home team and the away team must be different.');
        }

        // Rule: only a completed match has a result, and it needs both scores
        if ($data['status'] === 'completed') {
            Validator::required($data, ['home_score', 'away_score']);
            Validator::integer($data['home_score'], 'home_score', 0, self::MAX_SCORE);
            Validator::integer($data['away_score'], 'away_score', 0, self::MAX_SCORE);
        } elseif ($data['home_score'] !== null || $data['away_score'] !== null) {
            throw new ValidationException('Scores can only be entered for a completed match.');
        }
    }
}
