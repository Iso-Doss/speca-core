<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'label' => 'Inspector',
                'name' => 'inspector',
                'description' => 'Inspector profil',
            ],
            [
                'label' => '',
                'name' => 'administrator',
                'description' => 'Administrator profil',
            ],
        ];

        foreach ($profiles as $profile) {
            UserProfile::withTrashed()->updateOrCreate(['name' => $profile['name']], $profile);
        }
    }
}
