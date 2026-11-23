<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/StandingDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';

class StandingService extends BaseService
{
    protected string $entityName = 'Standing';

    /** played and points are not here: the service calculates them. */
    protected array $fields = ['team_id', 'season', 'wins', 'draws', 'losses', 'goals_for', 'goals_against', 'position'];

    /** Handball: 2 points for a win, 1 for a draw. */
    public const POINTS_FOR_WIN = 2;
    public const POINTS_FOR_DRAW = 1;

    private $teamDao;

    public function __construct($dao = null, $teamDao = null)
    {
        parent::__construct($dao ?? new StandingDao());
        $this->teamDao = $teamDao ?? new TeamDao();
    }

    /** The table of one season, ordered by position. */
    public function get_by_season($season): array
    {
        $season = trim((string) $season);
        $this->validate_season($season);

        return $this->dao->get_by_season($season);
    }

    protected function prepare(array $data): array
    {
        $data = parent::prepare($data);

        // Rule: played and points are always calculated, never taken from the request
        if (is_numeric($data['wins']) && is_numeric($data['draws']) && is_numeric($data['losses'])) {
            $data['played'] = (int) $data['wins'] + (int) $data['draws'] + (int) $data['losses'];
            $data['points'] = (int) $data['wins'] * self::POINTS_FOR_WIN + (int) $data['draws'] * self::POINTS_FOR_DRAW;
        }

        return $data;
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['team_id', 'season', 'wins', 'draws', 'losses', 'goals_for', 'goals_against', 'position']);
        $team = $this->require_existing($this->teamDao, $data['team_id'], 'team_id', 'team');
        $this->validate_season($data['season']);
        foreach (['wins', 'draws', 'losses', 'goals_for', 'goals_against'] as $field) {
            Validator::integer($data[$field], $field, 0);
        }
        Validator::integer($data['position'], 'position', 1);

        // Rule: one standings row per team and season
        $existing = $this->dao->get_by_team_and_season((int) $data['team_id'], $data['season']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException("'{$team['name']}' already has standings for the {$data['season']} season.");
        }

        // Rule: two teams cannot share a position in the same season
        $samePosition = $this->dao->get_by_season_and_position($data['season'], (int) $data['position']);
        if ($samePosition !== null && (int) $samePosition['id'] !== $id) {
            throw new ValidationException("Position {$data['position']} in the {$data['season']} season is already taken.");
        }
    }

    /** A season is written as two following years, e.g. 2026-2027. */
    private function validate_season(string $season): void
    {
        if (!preg_match('/^(\d{4})-(\d{4})$/', $season, $years) || (int) $years[2] !== (int) $years[1] + 1) {
            throw new ValidationException("Field 'season' must be two following years, e.g. 2026-2027.");
        }
    }
}
