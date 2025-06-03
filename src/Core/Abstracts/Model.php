<?php

namespace Lms\Core\Abstracts;
use \Lms\Core\QueryBuilder;

abstract class Model
{
    protected static string $table;
    protected array $attributes = [];

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __call(string $method, array $arguments)
    {
        $realMethod = $method . 'Query';
        if (method_exists($this, $realMethod)) {
            return $this->{$realMethod}(...$arguments);
        }

        throw new \BadMethodCallException(
            "Method " . static::class . "::{$method}() does not exist"
        );
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $realMethod = $method . 'Query';
        if (method_exists(static::class, $realMethod)) {
            return call_user_func_array([static::class, $realMethod], $arguments);
        }

        $queryBuilder = static::query();
        if (method_exists($queryBuilder, $method)) {
            return $queryBuilder->{$method}(...$arguments);
        }

        throw new \BadMethodCallException(
            "Method " . static::class . "::{$method}() does not exist"
        );
    }

    // Returns an instance of the QueryBuilder for the current model's table
    protected static function query(): QueryBuilder
    {
        return new QueryBuilder(static::$table, static::class);
    }

    // Retrieve all records from the database
    protected static function allQuery(array $only = [],array $except = [],?int $offset = null,?int $limit = null): array
    {
        return self::query()->only($only)->except($except)->offset($offset)->limit($limit)->get();
    }

    // Find a record by ID
    protected static function findQuery(int $id): ?self
    {
        $row = self::query()->where('id', $id)->get();
        if (empty($row)) {
            return null;
        } else return $row[0];
    }


    // Create a new record
    protected static function createQuery(array $data): ?self
    {
        if (isset($data['id'])) {
            if (static::findQuery($data['id'])) {
                return null;//specific id row is exist
            }
        }

        $insert_id = self::query()->insert($data);
        if ($insert_id != -1) {
            return self::findQuery($insert_id);
        } else return null;
    }

    // Retrieve the first record from the database
    protected static function firstQuery(): ?self
    {
        $row = self::query()->select()->limit(1)->get();
        return $row ? self::hydrateQuery($row) : null;
    }

    // Hydrate a model instance with an array of data
    public static function hydrateQuery(array $data): self
    {
        $model = new static();
        $model->attributes = $data;
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }


    // Update an existing record
    protected function updateQuery(array $data): bool
    {
        if (empty($this->attributes['id'])) {
            return false; // model not exist in db
        }
        unset($data['id']);
        if (self::query()->where('id', $this->attributes['id'])->update($data)) {
            $this->attributes = $data;
            return true;
        }
        return false;
    }

    // Delete the current record from the database
    protected function deleteQuery(): bool
    {
        if (empty($this->attributes['id'])) {
            return false; // model not exist in db
        }
        return self::query()->where('id', $this->attributes['id'])->delete();
    }

    public function to_array(): array
    {
        return $this->attributes;
    }

    public function except(array $columns): self
    {
        foreach ($columns as $column) {
            $this->{$column} = null;
            unset($this->attributes[$column]);
        }
        return $this;
    }
}
