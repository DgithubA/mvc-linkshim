<?php

namespace Lms\Core;

class QueryBuilder
{
    protected string $table;
    protected string $modelClass;
    protected \PDO $pdo;
    protected string $selects = '*';
    protected array $excepts = [];
    protected array $conditions = [];
    protected array $bindings = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $orderByClauses = [];
    protected array $groupByColumns = [];
    protected array $having = [];

    public function __construct(string $table, string $modelClass)
    {
        $this->table = $table;
        $this->modelClass = $modelClass;
        $this->pdo = Database::getInstance();
    }

    public function select(...$columns): self
    {
        if (isset($columns[0])) $columns = $columns[0];
        $this->selects = empty($columns) ? '*' : implode(',', $columns);
        return $this;
    }

    public function only(array $columns): self
    {
        return $this->select(...$columns);
    }

    public function except(array $columns): self
    {
        $this->excepts = $columns;
        return $this;
    }

    public function where(string $column, mixed $operator = null, mixed $value = null): self
    {
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }

        $this->conditions[] = ['column' => $column, 'operator' => $operator, 'value' => $value];

        return $this;
    }

    public function groupBy(...$columns): self
    {
        $this->groupByColumns = $columns;
        return $this;
    }

    public function having(string $column, string $operator, mixed $value): self
    {
        $this->having[] = ['column' => $column, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderByClauses[] = "$column $direction";
        return $this;
    }

    public function limit(?int $limit = null): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(?int $offset = null): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(array $only = [], array $except = []): array
    {
        if(!empty($only)) $this->select(...$only);

        $query = "SELECT $this->selects FROM $this->table";

        if ($this->conditions) {
            $where_query = "";
            foreach ($this->conditions as $a) {
                $where_query .= "$a[column] $a[operator] ? AND ";
                $this->bindings[] = $a['value'];
            }
            $where_query = substr($where_query, 0, -4);
            $query .= " WHERE $where_query";
        }

        if ($this->groupByColumns) {
            $query .= " GROUP BY " . implode(", ", $this->groupByColumns);
        }

        if ($this->having) {
            $having_query = "";
            foreach ($this->having as $h) {
                $having_query .= "$h[column] $h[operator] ?";
                $this->bindings[] = $h['value'];
            }
            $query .= " HAVING $having_query";
        }

        if ($this->orderByClauses) {
            $query .= " ORDER BY " . implode(", ", $this->orderByClauses);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT $this->limit";
        }

        if ($this->offset !== null) {
            $query .= " OFFSET $this->offset";
        }


        $stmt = $this->pdo->prepare($query);
        $stmt->execute($this->bindings);


        $return = [];
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $return[] = $this->modelClass::hydrateQuery(array_diff_key($row, array_flip($this->excepts)));
        }
        return $return;
    }

    public function first()
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    public function count(): int
    {
        $query = "SELECT COUNT(*) FROM $this->table";

        if ($this->conditions) {
            $where_query = "";
            foreach ($this->conditions as $a) {
                $where_query .= "$a[column] $a[operator] ?";
                $this->bindings[] = $a['value'];
            }
            $query .= " WHERE $where_query";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($this->bindings);

        return (int)$stmt->fetchColumn();
    }

    private function aggregate($function, $column)
    {
        $query = "SELECT $function($column) FROM $this->table";

        if ($this->conditions) {
            $where_query = "";
            foreach ($this->conditions as $a) {
                $where_query .= "$a[column] $a[operator] ?";
                $this->bindings[] = $a['value'];
            }
            $query .= " WHERE $where_query";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($this->bindings);

        return $stmt->fetchColumn();
    }

    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    public function insert(array $data): int
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), '?'));

        $query = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array_values($data));

        return (int)$this->pdo->lastInsertId() ?? -1;
    }

    public function update(array $data): bool
    {
        $setQuery = "";
        foreach ($data as $column => $value) {
            $setQuery .= "$column = ?, ";
            $this->bindings[] = $value;
        }
        $setQuery = rtrim($setQuery, ", ");

        $query = "UPDATE $this->table SET $setQuery";

        if ($this->conditions) {
            $where_query = "";
            foreach ($this->conditions as $a) {
                $where_query .= "$a[column] $a[operator] ?";
                $this->bindings[] = $a['value'];
            }
            $query .= " WHERE $where_query";
        }
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($this->bindings);
    }

    public function delete(): bool
    {
        $query = "DELETE FROM $this->table";

        if ($this->conditions) {
            $where_query = "";
            foreach ($this->conditions as $a) {
                $where_query .= "$a[column] $a[operator] ?";
                $this->bindings[] = $a['value'];
            }
            $query .= " WHERE $where_query";
        }

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($this->bindings);
    }
}


