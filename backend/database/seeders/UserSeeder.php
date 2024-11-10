<?php

namespace Database\Seeders;

use App\Helpers\PasswordHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passwordSalt = PasswordHelper::getPasswordSalt();

        $users = [
            [
                'username' => 'admin',
                'password' => PasswordHelper::getHashPassword('P@ssw0rd', $passwordSalt),
                'password_salt' => $passwordSalt,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
