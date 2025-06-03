<?php

namespace Lms\app\Models;

use Lms\Core\Abstracts\Model;

class User extends Model
{
    protected static string $table = 'users';

    public int $id;
    public string $name;
    public string $email;
    public string $role;
    private string $password;

    protected static function findByEmailQuery(string $email): ?self
    {
        $row = parent::query()->where('email', $email)->get();
        if (empty($row)) {
            return null;
        } else return $row[0];
    }
}
