<?php

namespace App;

use App\Exceptions\DbException;
use PDO;
use PDOException;

/**
 * Class Connection
 * @package App
 */
final class Connection
{
    private string $dsn;
    private string $username;
    private string $password;
    private array $options;

    private PDO $pdo;

    /**
     * @param array $params
     * @throws DbException
     */
    public function __construct(array $params)
    {
        $this->dsn = $params['dsn'];
        $this->username = $params['db_user'];
        $this->password = $params['db_password'];
        $this->options = $params['options'];

        $this->open();
    }

    /**
     * @return void
     * @throws DbException
     */
    private function open(): void
    {
        try {
            $this->pdo = $this->createPdoInstance();
        } catch (PDOException $e) {
            throw new DbException($e->getMessage(), $e->errorInfo, (int)$e->getCode(), $e);
        }
    }

    /**
     * @return PDO
     */
    private function createPdoInstance(): PDO
    {
        return new PDO($this->dsn, $this->username, $this->password, $this->options);
    }
}
