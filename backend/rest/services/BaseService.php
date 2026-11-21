<?php

declare(strict_types=1);

require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';

/**
 * Common business logic for every entity.
 *
 * Broken rules are signalled with exceptions from utils/exceptions.php that carry
 * an HTTP status code. Child services list their $fields, override validate() with
 * their own rules and override a CRUD method when an entity needs special behaviour.
 * Services are the ONLY layer that calls the DAOs.
 */
abstract class BaseService
{
    protected $dao;

    /** Used in messages, e.g. "Team not found." */
    protected string $entityName = 'Entity';

    /** The fields a client may send. Every other key in the request is ignored. */
    protected array $fields = [];

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    public function get_all(): array
    {
        return $this->dao->get_all();
    }

    public function get_by_id($id): array
    {
        Validator::integer($id, 'id', 1);
        $entity = $this->dao->get_by_id((int) $id);
        if ($entity === null) {
            throw new NotFoundException("{$this->entityName} not found.");
        }

        return $entity;
    }

    public function add(array $data): array
    {
        $data = $this->prepare($data);
        $this->validate($data);
        $id = $this->dao->add($data);

        return $this->get_by_id($id);
    }

    /** PUT replaces the whole entity, so every required field must be sent again. */
    public function update($id, array $data): array
    {
        $this->get_by_id($id);
        $data = $this->prepare($data);
        $this->validate($data, (int) $id);
        $this->dao->update((int) $id, $data);

        return $this->get_by_id($id);
    }

    public function delete($id): void
    {
        $this->get_by_id($id);
        $this->dao->delete((int) $id);
    }

    /**
     * Throw a ValidationException when $data breaks a rule.
     * $id is null when an entity is added and its id when it is updated.
     */
    protected function validate(array $data, ?int $id = null): void
    {
    }

    /**
     * Keep only the known fields (missing ones become null), turn every value
     * into trimmed text and an empty string from a form into null.
     */
    protected function prepare(array $data): array
    {
        $prepared = [];
        foreach ($this->fields as $field) {
            $value = $data[$field] ?? null;
            if (is_array($value) || is_object($value)) {
                throw new ValidationException("Field '$field' must be a single value.");
            }
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            if (is_int($value) || is_float($value)) {
                // JSON numbers become text, so every value is checked the same way as form input
                $value = (string) $value;
            }
            if (is_string($value)) {
                $value = trim($value);
                if ($value === '') {
                    $value = null;
                }
            }
            $prepared[$field] = $value;
        }

        return $prepared;
    }

    /** Check that a related entity exists, e.g. the team of a player. */
    protected function require_existing($dao, $id, string $field, string $label): array
    {
        Validator::integer($id, $field, 1);
        $entity = $dao->get_by_id((int) $id);
        if ($entity === null) {
            throw new ValidationException("The selected $label does not exist.");
        }

        return $entity;
    }
}
