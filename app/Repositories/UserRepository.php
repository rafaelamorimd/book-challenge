<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository
{
    public function create(array $data): Model
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?Model
    {
        return User::where('email', $email)->first();
    }
}
