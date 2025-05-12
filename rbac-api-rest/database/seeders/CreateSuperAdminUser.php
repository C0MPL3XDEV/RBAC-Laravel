<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class CreateSuperAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => "superadmin@example.com"],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('Superadmin123!'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super-admin');
    }
}
