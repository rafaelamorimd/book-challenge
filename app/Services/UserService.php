<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function createUser(array $data): Model
    {
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new Exception('Email já cadastrado');
        }

        $data['password'] = bcrypt($data['password']);
        return $this->userRepository->create($data);
    }
}
