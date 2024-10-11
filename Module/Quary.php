<?php

namespace Module;

use PDO;
use PDOStatement;

class Quary
{
    private static $db_directory = 'database';
    private static $db_name = 'database';
    private $tb_name;
    private $values = [];
    private $statement;
    private $condition;
    private PDOStatement $connection;

    private function __construct(string $tb_name)
    {
        $this->tb_name = $tb_name;
    }

    private function add_statement(string $sql)
    {
        if (!empty($this->statement)) {
            err('Quary Builder', 'Multiple statement are not allowed');
        }
        $this->statement = $sql;
    }

    private function add_condition($operator, $condition)
    {
        if (empty($operator)) $operator = "WHERE";
        $this->condition .= "$operator $condition ?";
    }

    private function add_value($value)
    {
        array_push($this->values, $value);
    }


    public function select(...$column)
    {
        $join_column = join(',', $column);
        $this->add_statement("SELECT $join_column FROM {$this->tb_name}");
        return $this;
    }

    public function delete()
    {
        if (empty($condition)) err('Quary Buildr', 'Condition required for delete');
        $this->add_statement("DELETE FROM {$this->tb_name}");
        return $this;
    }

    public function where($column, $condition, $value)
    {
        $this->add_condition('AND', "$column $condition");
        $this->add_value($value);
        return $this;
    }
    public function orwhere($column, $condition, $value)
    {
        $this->add_condition('AND', "$column $condition");
        $this->add_value($value);
        return $this;
    }

    public function get()
    {
        if (!$this->condition) $this->exec();
        return $this->condition->fetch();
    }

    public function remain()
    {
        if (!$this->condition) $this->exec();
        return $this->condition->fetchAll();
    }

    public function exec()
    {
        $path = path_real(self::$db_directory . '/' . self::$db_name);
        $conn = new PDO("sqlite:$path.db");

        $sql = "{$this->statement} {$this->condition}";
        $stmt = $conn->prepare($sql);

        $stmt->execute($this->values);
        $this->connection = $stmt;
        return $this;
    }

    static function table($tb_name)
    {
        return new Quary($tb_name);
    }

    static function use($db_name)
    {
        self::$db_name = $db_name;
        return self::class;
    }
}
