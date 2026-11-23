<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/PlayerDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';

class PlayerService extends BaseService
{
    protected string $entityName = 'Player';
    protected array $fields = ['first_name', 'last_name', 'birth_date', 'position', 'jersey_number', 'team_id'];

    public const POSITIONS = ['Goalkeeper', 'Left wing', 'Right wing', 'Left back', 'Right back', 'Centre back', 'Pivot'];

    /** The youngest age at which a player can be registered. */
    public const MIN_AGE = 14;

    private $teamDao;

    public function __construct($dao = null, $teamDao = null)
    {
        parent::__construct($dao ?? new PlayerDao());
        $this->teamDao = $teamDao ?? new TeamDao();
    }

    /** All players with the name and city of their team. */
    public function get_all_with_team(): array
    {
        return $this->dao->get_all_with_team();
    }

    /** The players of one team; 404 when the team does not exist. */
    public function get_by_team($teamId): array
    {
        Validator::integer($teamId, 'team_id', 1);
        if ($this->teamDao->get_by_id((int) $teamId) === null) {
            throw new NotFoundException('Team not found.');
        }

        return $this->dao->get_by_team((int) $teamId);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['first_name', 'last_name', 'position', 'jersey_number', 'team_id']);
        Validator::length($data['first_name'], 'first_name', 2, 100);
        Validator::length($data['last_name'], 'last_name', 2, 100);
        Validator::no_html($data['first_name'], 'first_name');
        Validator::no_html($data['last_name'], 'last_name');
        Validator::one_of($data['position'], 'position', self::POSITIONS);
        Validator::integer($data['jersey_number'], 'jersey_number', 1, 99);
        $team = $this->require_existing($this->teamDao, $data['team_id'], 'team_id', 'team');

        if ($data['birth_date'] !== null) {
            Validator::date($data['birth_date'], 'birth_date');
            $latest = date('Y-m-d', strtotime('-' . self::MIN_AGE . ' years'));
            if ($data['birth_date'] > $latest) {
                throw new ValidationException('A player must be at least ' . self::MIN_AGE . ' years old.');
            }
        }

        // Rule: a jersey number is used only once in a team
        $existing = $this->dao->get_by_team_and_jersey((int) $data['team_id'], (int) $data['jersey_number']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException("Jersey number {$data['jersey_number']} is already taken in '{$team['name']}'.");
        }
    }
}
