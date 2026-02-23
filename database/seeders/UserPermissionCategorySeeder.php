<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\UserPermissionCategory;

class UserPermissionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userPermissionCategories = [
            [
                'label' => 'Gérer les catégories de permission utilisateur',
                'name' => 'manage-user-permission-category',
                'description' => 'Gérer les catégories de permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Gérer les permissions utilisateur',
                'name' => 'manage-user-permission',
                'description' => 'Gérer les permissions utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Gérer les rôles utilisateur',
                'name' => 'manage-user-role',
                'description' => 'Gérer les rôles utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Gérer les utilisateurs',
                'name' => 'manage-user',
                'description' => 'Gérer les utilisateurs',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Gérer les journaux d\'activité',
                'name' => 'manage-activity-log',
                'description' => 'Gérer les journaux d\'activité',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Gérer les pays',
                'name' => 'manage-country',
                'description' => 'Gérer les pays',
                'guard_name' => 'api',
            ],
        ];

        foreach ($userPermissionCategories as $userPermissionCategory) {
            UserPermissionCategory::withTrashed()->updateOrCreate(['name' => $userPermissionCategory['name']], $userPermissionCategory);
        }
    }
}
