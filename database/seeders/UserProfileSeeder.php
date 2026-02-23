<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            'Marchand',
            'Filiale d\'un marchand',
            'Collaborateur d\'un marchand',
            'Sous marchand d\'un marchand',
            'Collaborateur interne',
            'Service interne',
            'Client',
            'Payeur',
            'Partenaire',
            'Administrateur',
            'Police',
            'Régulateur',
        ];

        foreach ($profiles as $profile) {
            UserProfile::withTrashed()->updateOrCreate(['name' => $profile], [
                'name' => $profile,
                'description' => 'Profil ' . $profile,
            ]);
        }
    }
}
