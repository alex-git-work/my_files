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

    public const FETCH_ONE = 'one';
    public const FETCH_ALL = 'all';

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
     * @param string $sql
     * @param array $params
     * @param string $fetch
     * @return array
     */
    public function createCommand(string $sql, array $params = [], string $fetch = self::FETCH_ONE): array
    {
        $stmt = $this->pdo->prepare($sql);

        if ($params) {
            foreach ($params as $param => $value) {
                $type = $this->getPdoType($value);
                $stmt->bindValue($param, $value, $type);
            }
        }

        $stmt->execute();
        $data = $fetch === self::FETCH_ONE ? $stmt->fetch() : $stmt->fetchAll();

        return $data ?: [];
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

    /**
     * @param $data
     * @return int
     */
    private function getPdoType($data): int
    {
        static $typeMap = [
            // php type => PDO type
            'boolean' => PDO::PARAM_BOOL,
            'integer' => PDO::PARAM_INT,
            'string' => PDO::PARAM_STR,
            'resource' => PDO::PARAM_LOB,
            'NULL' => PDO::PARAM_NULL,
        ];
        $type = gettype($data);

        return $typeMap[$type] ?? PDO::PARAM_STR;
    }
}
