<?php

namespace App\Base;

use App\App;
use App\Helpers\StringHelper;

/**
 * Class Query
 * @package App\Base
 */
class Query extends BaseObject
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return StringHelper::camel2id(StringHelper::basename(get_called_class()), '_') . 's';
    }

    /**
     * $condition: @see queryParams()
     *
     * @param int|string|array $condition
     * @return static|null
     */
    public static function findOne(int|string|array $condition = []): ?static
    {
        if (is_array($condition)) {
            $data = self::queryParams($condition);
            $sql = $data['sql'];
            $params = $data['params'];
        } else {
            $sql = 'SELECT * FROM ' . self::tableName() . ' WHERE id = :id';
            $params = [':id' => $condition];
        }

        $attributes = App::$db->createCommand($sql, $params)->query();

        if (empty($attributes)) {
            return null;
        }

        return new static($attributes);
    }

    /**
     * $condition: @see queryParams()
     *
     * @param array $condition
     * @return array
     */
    public static function findAll(array $condition = []): array
    {
        $data = self::queryParams($condition);
        $rows = App::$db->createCommand($data['sql'], $data['params'])->queryAll();

        return self::createModels($rows);
    }

    /**
     * $condition:
     * ['column' => 'value']
     * ['column1' => 'value', 'column2' => 'value']
     * [['column1', 'operator', value], ['column2', 'operator', value]]
     *
     * note that 'value' can be array
     *
     * operator: =, !=, >, >=, <, <=, IS, IS NOT, IN, NOT IN
     *
     * examples:
     * ['is_active' => 1] <--> WHERE is_active = 1
     *
     * [['last_login', 'is not', null]] <--> WHERE last_login IS NOT NULL
     *
     * ['id' => [1, 2, 3]] <--> WHERE id IN (1, 2, 3)
     *
     * [['role' '!=', 1], ['status', 'not in', [2, 4, 7]]] <--> WHERE role != 1 AND status NOT IN (2, 4, 7)
     *
     * @param array $condition
     * @return array
     */
    protected static function queryParams(array $condition = []): array
    {
        $where = '';
        $params = [];

        if ($condition) {
            $where .= ' WHERE ';

            foreach ($condition as $key => $value) {
                if (is_int($key)) {
                    if (count($value) === 1) {
                        foreach ($value as $c => $v) {
                            $where .= $c . ' = :' . $c . ' AND ';
                            $params[':' . $c] = $v;
                        }
                    } else {
                        list($c, $o, $v) = $value;
                        if (is_array($v)) {
                            $placeHolders = implode(', ', array_fill(0, count($v), '?'));
                            $where .= $c . ' ' . strtoupper($o) . ' (' . $placeHolders . ') AND ';
                            $params = array_combine(array_map(fn($key) => $key + 1, array_keys($v)), $v);
                        } else {
                            if ($v === null) {
                                $v = 'NULL';
                            }
                            $where .= $c . ' ' . strtoupper($o) . ' ' . $v . ' AND ';
                        }
                    }
                } else {
                    if (is_array($value)) {
                        $placeHolders = implode(', ', array_fill(0, count($value), '?'));
                        $where .= $key . ' IN (' . $placeHolders . ') AND ';
                        $params = array_combine(array_map(fn($key) => $key + 1, array_keys($value)), $value);
                    } else {
                        $where .= $key . ' = :' . $key . ' AND ';
                        $params[':' . $key] = $value;
                    }
                }
            }

            $where = substr($where, 0, -5);

            $sql = 'SELECT * FROM ' . self::tableName() . $where;
        } else {
            $sql = 'SELECT * FROM ' . self::tableName();
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * @param $rows
     * @return array
     */
    protected static function createModels($rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $models = [];

        foreach ($rows as $attribute) {
            $model = new static($attribute);
            $models[] = $model;
        }

        return $models;
    }
}
