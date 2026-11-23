<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RoleDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class RoleService extends BaseService
{
    protected string $entityName = 'Role';
    protected array $fields = ['name', 'description'];

    private $userDao;

    /** DAOs can be passed in (for tests); by default the real DAOs are created. */
    public function __construct($dao = null, $userDao = null)
    {
        parent::__construct($dao ?? new RoleDao());
        $this->userDao = $userDao ?? new UserDao();
    }

    /** Rule: a role that is still assigned to users cannot be deleted. */
    public function delete($id): void
    {
        $role = $this->get_by_id($id);
        $users = $this->userDao->count_by_role((int) $id);
        if ($users > 0) {
            throw new ValidationException("The role '{$role['name']}' is assigned to $users user(s). Change their role first.");
        }
        $this->dao->delete((int) $id);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['name']);
        Validator::length($data['name'], 'name', 2, 50);
        if (!preg_match('/^[a-z_]+$/', $data['name'])) {
            throw new ValidationException("Field 'name' may contain only lowercase letters and underscores.");
        }
        if ($data['description'] !== null) {
            Validator::length($data['description'], 'description', 0, 255);
            Validator::no_html($data['description'], 'description');
        }

        // Rule: role names are unique
        $existing = $this->dao->get_by_name($data['name']);
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException("A role named '{$data['name']}' already exists.");
        }
    }
}
