<?php

namespace App\Services\MasterData;

use App\Models\MasterData\MasterUser;
use Illuminate\Support\Facades\Hash;

class MasterUserService
{
    /**
     * Create a new Master User.
     *
     * @param array $data
     * @return MasterUser
     */
    public function create(array $data): MasterUser
    {
        $data['password'] = Hash::make($data['password']);
        return MasterUser::create($data);
    }

    /**
     * Update an existing Master User.
     *
     * @param MasterUser $user
     * @param array $data
     * @return MasterUser
     */
    public function update(MasterUser $user, array $data): MasterUser
    {
        if ($user->akses && $user->akses->nama_akses === 'Superadmin') {
            throw new \DomainException('Superadmin user cannot be edited.');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete a Master User.
     *
     * @param MasterUser $user
     * @return void
     */
    public function delete(MasterUser $user): void
    {
        if ($user->akses && $user->akses->nama_akses === 'Superadmin') {
            throw new \DomainException('Superadmin user cannot be deleted.');
        }

        $user->delete();
    }

    /**
     * Reset password for a Master User.
     *
     * @param MasterUser $user
     * @param string $password
     * @return void
     */
    public function resetPassword(MasterUser $user, string $password): void
    {
        if ($user->akses && $user->akses->nama_akses === 'Superadmin') {
            throw new \DomainException('Superadmin user password cannot be reset.');
        }

        $user->update([
            'password' => Hash::make($password)
        ]);
    }
}
