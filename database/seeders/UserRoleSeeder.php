<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $userRoles = [
            [
                'label' => 'Administrateur',
                'name' => 'administrator',
                'description' => 'Administrateur',
                'guard_name' => 'api',
            ],
        ];

        foreach ($userRoles as $userRole) {
            UserRole::withTrashed()->updateOrCreate(['name' => $userRole['name']], $userRole);
        }
    }
}
