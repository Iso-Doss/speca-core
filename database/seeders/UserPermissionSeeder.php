<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\UserPermission;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $userPermissions = [
            // Permission pour la gestion des catégories de permission utilisateur.
            [
                'label' => 'Liste des catégories de permission utilisateur',
                'name' => 'list-user-permission-category',
                'description' => 'Consulter les catégories de permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Consulter une catégorie de permission utilisateur',
                'name' => 'show-user-permission-category',
                'description' => 'Consulter les détails d\'une catégorie de permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Créer une catégorie de permission utilisateur',
                'name' => 'create-user-permission-category',
                'description' => 'Créer une catégorie de permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Modifier une catégorie de permission utilisateur existante',
                'name' => 'update-user-permission-category',
                'description' => 'Modifier une catégorie de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Activer ou désactiver une catégorie de permission utilisateur existante',
                'name' => 'enable-disable-user-permission-category',
                'description' => 'Activer ou désactiver une catégorie de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Action grouper sur une liste de catégorie de permission utilisateur existante',
                'name' => 'group-action-user-permission-category',
                'description' => 'Action grouper sur une liste de catégorie de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Exporter une liste de catégorie de permission utilisateur existante',
                'name' => 'export-user-permission-category',
                'description' => 'Exporter une liste de catégorie de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Archiver une catégorie de permission utilisateur existante',
                'name' => 'delete-user-permission-category',
                'description' => 'Archiver une catégorie de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Restaurer une catégorie de permission utilisateur archivée',
                'name' => 'restore-user-permission-category',
                'description' => 'Restaurer une catégorie de permission utilisateur archivée',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Supprimer une catégorie de permission utilisateur',
                'name' => 'force-delete-user-permission-category',
                'description' => 'Supprimer une catégorie de permission utilisateur',
                'guard_name' => 'api',
            ],

            // Permission pour la gestion des permissions utilisateur.
            [
                'label' => 'Liste des permissions utilisateur',
                'name' => 'list-user-permission',
                'description' => 'Consulter les permissions utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Consulter une permission utilisateur',
                'name' => 'show-user-permission',
                'description' => 'Consulter les détails d\'une permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Créer une permission utilisateur',
                'name' => 'create-user-permission',
                'description' => 'Créer une permission utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Modifier une permission utilisateur existante',
                'name' => 'update-user-permission',
                'description' => 'Modifier une permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Activer ou désactiver une permission utilisateur existante',
                'name' => 'enable-disable-user-permission',
                'description' => 'Activer ou désactiver une permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Action grouper sur une liste de permission utilisateur existante',
                'name' => 'group-action-user-permission',
                'description' => 'Action grouper sur une liste de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Exporter une liste de permission utilisateur existante',
                'name' => 'export-user-permission',
                'description' => 'Exporter une liste de permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Archiver une permission utilisateur existante',
                'name' => 'delete-user-permission',
                'description' => 'Archiver une permission utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Restaurer une permission utilisateur archivée',
                'name' => 'restore-user-permission',
                'description' => 'Restaurer une permission utilisateur archivée',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Supprimer une permission utilisateur',
                'name' => 'force-delete-user-permission',
                'description' => 'Supprimer une permission utilisateur',
                'guard_name' => 'api',
            ],

            // Permission pour la gestion des roles utilisateur.
            [
                'label' => 'Liste des rôles utilisateur',
                'name' => 'list-user-role',
                'description' => 'Consulter les rôles utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Consulter un rôle utilisateur',
                'name' => 'show-user-role',
                'description' => 'Consulter les détails d\'un rôle utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Créer un rôle utilisateur',
                'name' => 'create-user-role',
                'description' => 'Créer un rôle utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Modifier un rôle utilisateur existant',
                'name' => 'update-user-role',
                'description' => 'Modifier un rôle utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Activer ou désactiver un rôle utilisateur existant',
                'name' => 'enable-disable-user-role',
                'description' => 'Activer ou désactiver un rôle utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Action grouper sur une liste de rôle utilisateur existante',
                'name' => 'group-action-user-role',
                'description' => 'Action grouper sur une liste de rôle utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Exporter une liste de rôle utilisateur existante',
                'name' => 'export-user-role',
                'description' => 'Exporter une liste de rôle utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Archiver un rôle utilisateur existant',
                'name' => 'delete-user-role',
                'description' => 'Archiver un rôle utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Restaurer un rôle utilisateur archivé',
                'name' => 'restore-user-role',
                'description' => 'Restaurer un rôle utilisateur archivé',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Supprimer un rôle utilisateur',
                'name' => 'force-delete-user-role',
                'description' => 'Supprimer un rôle utilisateur',
                'guard_name' => 'api',
            ],

            // Permission pour la gestion des utilisateurs.
            [
                'label' => 'Liste des utilisateurs',
                'name' => 'list-user',
                'description' => 'Consulter les utilisateurs',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Consulter un utilisateur',
                'name' => 'show-user',
                'description' => 'Consulter les détails d\'un utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Créer un utilisateur',
                'name' => 'create-user',
                'description' => 'Créer un utilisateur',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Modifier un utilisateur existant',
                'name' => 'update-user',
                'description' => 'Modifier un utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Activer ou désactiver un utilisateur existant',
                'name' => 'enable-disable-user',
                'description' => 'Activer ou désactiver un utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Action grouper sur une liste d\'utilisateur existante',
                'name' => 'group-action-user',
                'description' => 'Action grouper sur une liste d\'utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Exporter une liste de rôle d\'utilisateur existante',
                'name' => 'export-user',
                'description' => 'Exporter une liste de rôle d\'utilisateur existante',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Archiver un utilisateur existant',
                'name' => 'delete-user',
                'description' => 'Archiver un utilisateur existant',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Restaurer un utilisateur archivé',
                'name' => 'restore-user',
                'description' => 'Restaurer un utilisateur archivé',
                'guard_name' => 'api',
            ],
            [
                'label' => 'Supprimer un utilisateur',
                'name' => 'force-delete-user',
                'description' => 'Supprimer un utilisateur',
                'guard_name' => 'api',
            ],
        ];

        foreach ($userPermissions as $userPermission) {
            UserPermission::withTrashed()->updateOrCreate(['name' => $userPermission['name']], $userPermission);
        }
    }
}
