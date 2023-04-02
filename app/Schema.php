<?php

namespace App;

use App\Exceptions\InvalidConfigException;
use Exception;
use PDOException;

/**
 * Class Schema
 * @package App
 */
final class Schema
{
    private Connection $db;
    private array $tableNames = [];
    private array $columns = [];

    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getTableNames(): array
    {
        if (empty($this->tableNames)) {
            $this->tableNames = $this->findTableNames($this->db->name);
        }

        return $this->tableNames;
    }

    /**
     * @param string $table
     * @return array
     * @throws Exception
     */
    public function getColumns(string $table): array
    {
        if (empty($this->columns)) {
            $this->setColumns();
        }

        if (!isset($this->columns[$table])) {
            throw new InvalidConfigException('Table `' . $table . '` does not exists');
        }

        return $this->columns[$table];
    }

    /**
     * @return void
     * @throws Exception
     */
    private function setColumns(): void
    {
        foreach ($this->getTableNames() as $table) {
            $this->columns[$table] = $this->findColumns($table);
        }
    }

    /**
     * @param string $schema
     * @return array
     */
    private function findTableNames(string $schema = ''): array
    {
        $sql = 'SHOW TABLES';

        if ($schema !== '') {
            $sql .= ' FROM ' . $schema;
        }

        $tables = $this->db->createCommand($sql)->queryAll();

        return array_column($tables, 'Tables_in_' . $this->db->name);
    }

    /**
     * @param string $table
     * @return array
     * @throws Exception
     */
    private function findColumns(string $table): array
    {
        $sql = 'SHOW FULL COLUMNS FROM ' . $table;

        try {
            $columns = $this->db->createCommand($sql)->queryAll();
        } catch (Exception $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof PDOException && str_contains($previous->getMessage(), 'SQLSTATE[42S02')) {
                #table does not exist
                return [];
            }
            throw $e;
        }

        return array_column($columns, 'Field');
    }
}
