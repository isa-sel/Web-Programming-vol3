<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/PlayerDao.php';
require_once __DIR__ . '/../dao/MatchDao.php';
require_once __DIR__ . '/../dao/StandingDao.php';

class TeamService extends BaseService
{
    protected string $entityName = 'Team';
    protected array $fields = ['name', 'city', 'founded_year', 'category'];

    public const CATEGORIES = ['Senior Men', 'Senior Women', 'Junior Men', 'Junior Women'];

    private $playerDao;
    private $matchDao;
    private $standingDao;

    public function __construct($dao = null, $playerDao = null, $matchDao = null, $standingDao = null)
    {
        parent::__construct($dao ?? new TeamDao());
        $this->playerDao = $playerDao ?? new PlayerDao();
        $this->matchDao = $matchDao ?? new MatchDao();
        $this->standingDao = $standingDao ?? new StandingDao();
    }

    /** Rule: a team that still has players, matches or standings cannot be deleted. */
    public function delete($id): void
    {
        $team = $this->get_by_id($id);
        $players = $this->playerDao->count_by_team((int) $id);
        $matches = $this->matchDao->count_by_team((int) $id);
        $standings = $this->standingDao->count_by_team((int) $id);
        if ($players > 0 || $matches > 0 || $standings > 0) {
            throw new ValidationException(
                "'{$team['name']}' still has $players player(s), $matches match(es) and $standings standings row(s). Remove them first."
            );
        }
        $this->dao->delete((int) $id);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['name', 'city', 'category']);
        Validator::length($data['name'], 'name', 2, 150);
        Validator::length($data['city'], 'city', 2, 100);
        Validator::no_html($data['name'], 'name');
        Validator::no_html($data['city'], 'city');
        Validator::one_of($data['category'], 'category', self::CATEGORIES);
        if ($data['founded_year'] !== null) {
            Validator::integer($data['founded_year'], 'founded_year', 1800, (int) date('Y'));
        }

        // Rule: there is only one team with the same name in the same city
        $existing = $this->dao->get_by_name_and_city($data['name'], $data['city']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException("A team named '{$data['name']}' already exists in {$data['city']}.");
        }
    }
}
