<?php
declare(strict_types=1);

namespace Core;

abstract class Model {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected static function getDb(): Database {
        return Database::getInstance();
    }
}
