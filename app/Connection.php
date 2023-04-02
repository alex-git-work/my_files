<?php

namespace App;

use App\Base\BaseObject;
use App\Exceptions\DbException;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class Connection
 * @package App
 *
 * @property-read Schema $schema
 * @property-read string $name
 * @property-read string $lastInsertID
 */
final class Connection extends BaseObject
{
    public PDO $pdo;
    public array $queryLog = [];

    private string $dsn;
    private string $username;
    private string $password;
    private array $options;

    private ?Schema $_schema = null;
    private ?PDOStatement $_stmt = null;

    /**
     * @param string $sql
     * @param array $params
     * @return Connection
     */
    public function createCommand(string $sql, array $params = []): Connection
    {
        $stmt = $this->pdo->prepare($sql);

        if ($params) {
            foreach ($params as $param => $value) {
                $type = $this->getPdoType($value);
                $stmt->bindValue($param, $value, $type);
            }
        }

        $stmt->execute();

        $this->_stmt = $stmt;
        $this->queryLog[] = $stmt->queryString;

        return $this;
    }

    /**
     * @return mixed
     */
    public function query(): mixed
    {
        return $this->_stmt->fetch();
    }

    /**
     * @return bool|array
     */
    public function queryAll(): bool|array
    {
        return $this->_stmt->fetchAll();
    }

    /**
     * @return mixed
     */
    public function queryColumn(): mixed
    {
        return $this->_stmt->fetchColumn();
    }

    /**
     * @param array $params
     * @param array $config
     * @throws DbException
     */
    public function __construct(array $params, array $config = [])
    {
        $this->dsn = $params['dsn'];
        $this->username = $params['db_user'];
        $this->password = $params['db_password'];
        $this->options = $params['options'];

        $this->open();

        parent::__construct($config);
    }

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        if ($this->_schema === null) {
            $this->_schema = new Schema($this);
        }

        return $this->_schema;
    }

    /**
     * @param string $name
     * @return string|false
     */
    public function getLastInsertID(string $name = ''): string|false
    {
        return $this->pdo->lastInsertId($name === '' ? null : $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return params('db_name');
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
