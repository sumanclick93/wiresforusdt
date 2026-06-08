<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class DropdownOption
{
    public ?int $id = null;
    public string $dropdown_key;
    public string $option_value;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Map database row object to DropdownOption instance.
     */
    public static function map($row): ?self
    {
        if (!$row) return null;

        $option = new self();
        $option->id = (int)$row->id;
        $option->dropdown_key = $row->dropdown_key;
        $option->option_value = $row->option_value;
        $option->created_at = $row->created_at;
        $option->updated_at = $row->updated_at;

        return $option;
    }

    /**
     * Find a dropdown option by ID.
     */
    public static function find(int $id): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM dropdown_options WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? self::map($row) : null;
    }

    /**
     * Fetch all options under a specific dropdown key.
     *
     * @return self[]
     */
    public static function findByKey(string $key): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM dropdown_options WHERE dropdown_key = :key ORDER BY option_value ASC");
        $stmt->execute([':key' => $key]);
        $rows = $stmt->fetchAll();

        $options = [];
        foreach ($rows as $row) {
            $options[] = self::map($row);
        }
        return $options;
    }

    /**
     * Get unique option values under a specific dropdown key as plain array of strings.
     *
     * @return string[]
     */
    public static function getValuesByKey(string $key): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT option_value FROM dropdown_options WHERE dropdown_key = :key ORDER BY option_value ASC");
        $stmt->execute([':key' => $key]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Fetch all dropdown options in the system.
     *
     * @return self[]
     */
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM dropdown_options ORDER BY dropdown_key ASC, option_value ASC");
        $rows = $stmt->fetchAll();

        $options = [];
        foreach ($rows as $row) {
            $options[] = self::map($row);
        }
        return $options;
    }

    /**
     * Save the dropdown option record.
     */
    public function save(): bool
    {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $this->updated_at = $now;

        if ($this->id === null) {
            // INSERT
            $this->created_at = $now;
            $stmt = $db->prepare("
                INSERT INTO dropdown_options (dropdown_key, option_value, created_at, updated_at)
                VALUES (:dropdown_key, :option_value, :created_at, :updated_at)
            ");

            $result = $stmt->execute([
                ':dropdown_key' => $this->dropdown_key,
                ':option_value' => $this->option_value,
                ':created_at' => $this->created_at,
                ':updated_at' => $this->updated_at
            ]);

            if ($result) {
                $this->id = (int)$db->lastInsertId();
            }
            return $result;
        } else {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE dropdown_options SET
                    dropdown_key = :dropdown_key,
                    option_value = :option_value,
                    updated_at = :updated_at
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $this->id,
                ':dropdown_key' => $this->dropdown_key,
                ':option_value' => $this->option_value,
                ':updated_at' => $this->updated_at
            ]);
        }
    }

    /**
     * Delete the dropdown option record.
     */
    public function delete(): bool
    {
        if ($this->id === null) return false;

        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM dropdown_options WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }
}
