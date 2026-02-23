<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountrySeeder::class);
        $this->call(UserPermissionCategorySeeder::class);
        $this->call(UserPermissionCategorySeeder::class);
        $this->call(UserPermissionSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(UserProfileSeeder::class);
        $this->call(UserSeeder::class);
    }
}
