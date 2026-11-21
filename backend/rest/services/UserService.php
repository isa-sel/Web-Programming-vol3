<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/RoleDao.php';

class UserService extends BaseService
{
    protected string $entityName = 'User';

    /** The client sends 'password'; only its bcrypt hash is ever stored. */
    protected array $fields = ['first_name', 'last_name', 'email', 'password', 'role_id', 'is_active'];

    /** The role of a new user when none is given. */
    public const DEFAULT_ROLE = 'user';

    private $roleDao;

    public function __construct($dao = null, $roleDao = null)
    {
        parent::__construct($dao ?? new UserDao());
        $this->roleDao = $roleDao ?? new RoleDao();
    }

    public function add(array $data): array
    {
        $data = $this->prepare($data);
        if ($data['role_id'] === null) {
            $defaultRole = $this->roleDao->get_by_name(self::DEFAULT_ROLE);
            $data['role_id'] = $defaultRole['id'] ?? null;
        }
        $data['is_active'] = $data['is_active'] ?? 1;
        $this->validate($data);

        // The password is checked BEFORE it is hashed: every bcrypt hash is 60 characters long.
        Validator::required($data, ['password']);
        Validator::password($data['password']);

        $id = $this->dao->add($this->to_columns($data));

        return $this->get_by_id($id);
    }

    /** The password is optional on update: when it is sent, it is replaced. */
    public function update($id, array $data): array
    {
        $this->get_by_id($id);
        $data = $this->prepare($data);
        $data['is_active'] = $data['is_active'] ?? 1;
        $this->validate($data, (int) $id);
        if ($data['password'] !== null) {
            Validator::password($data['password']);
        }

        $this->dao->update((int) $id, $this->to_columns($data));

        return $this->get_by_id($id);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['first_name', 'last_name', 'email', 'role_id']);
        Validator::length($data['first_name'], 'first_name', 2, 100);
        Validator::length($data['last_name'], 'last_name', 2, 100);
        Validator::no_html($data['first_name'], 'first_name');
        Validator::no_html($data['last_name'], 'last_name');
        Validator::email($data['email']);
        Validator::length($data['email'], 'email', 5, 150);
        Validator::integer($data['is_active'], 'is_active', 0, 1);
        $this->require_existing($this->roleDao, $data['role_id'], 'role_id', 'role');

        // Rule: one account per e-mail address (compared in lowercase)
        $existing = $this->dao->get_by_email(strtolower($data['email']));
        if ($existing !== null && (int) $existing['id'] !== $id) {
            throw new ValidationException('An account with this e-mail address already exists.');
        }
    }

    /** Turn the validated request into database columns: hash the password, lowercase the e-mail. */
    private function to_columns(array $data): array
    {
        $columns = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => strtolower($data['email']),
            'role_id' => (int) $data['role_id'],
            'is_active' => (int) $data['is_active'],
        ];
        if ($data['password'] !== null) {
            $columns['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        return $columns;
    }
}
