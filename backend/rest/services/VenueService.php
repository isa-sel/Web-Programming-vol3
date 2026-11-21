<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/VenueDao.php';
require_once __DIR__ . '/../dao/MatchDao.php';

class VenueService extends BaseService
{
    protected string $entityName = 'Venue';
    protected array $fields = ['name', 'city', 'address', 'capacity'];

    public const MAX_CAPACITY = 100000;

    private $matchDao;

    public function __construct($dao = null, $matchDao = null)
    {
        parent::__construct($dao ?? new VenueDao());
        $this->matchDao = $matchDao ?? new MatchDao();
    }

    /** Rule: a venue that has matches cannot be deleted. */
    public function delete($id): void
    {
        $venue = $this->get_by_id($id);
        $matches = $this->matchDao->count_by_venue((int) $id);
        if ($matches > 0) {
            throw new ValidationException("'{$venue['name']}' hosts $matches match(es). Move or delete them first.");
        }
        $this->dao->delete((int) $id);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['name', 'city', 'address', 'capacity']);
        Validator::length($data['name'], 'name', 2, 150);
        Validator::length($data['city'], 'city', 2, 100);
        Validator::length($data['address'], 'address', 5, 255);
        foreach (['name', 'city', 'address'] as $field) {
            Validator::no_html($data[$field], $field);
        }
        Validator::integer($data['capacity'], 'capacity', 1, self::MAX_CAPACITY);

        // Rule: venue names are unique within a city
        $existing = $this->dao->get_by_name_and_city($data['name'], $data['city']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException("A venue named '{$data['name']}' already exists in {$data['city']}.");
        }
    }
}
