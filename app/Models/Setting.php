<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Setting extends Model {
    private static array $cache = [];

    public function get(string $key, mixed $default = null): mixed {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $row = $this->db->fetch("SELECT setting_value FROM store_settings WHERE setting_key = ? LIMIT 1", [$key]);
        if ($row) {
            self::$cache[$key] = $row['setting_value'];
            return $row['setting_value'];
        }

        return $default;
    }

    public function getAllGrouped(): array {
        $rows = $this->db->fetchAll("SELECT * FROM store_settings ORDER BY setting_group ASC, id ASC");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['setting_group']][] = $row;
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        return $grouped;
    }

    public function set(string $key, string $value): bool {
        self::$cache[$key] = $value;
        $exists = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM store_settings WHERE setting_key = ?", [$key]);

        if ($exists > 0) {
            return $this->db->execute("UPDATE store_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
        } else {
            return $this->db->execute("INSERT INTO store_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
        }
    }

    public function updateBatch(array $settings): void {
        foreach ($settings as $key => $value) {
            $this->set((string)$key, (string)$value);
        }
    }
}
