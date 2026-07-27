<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'engins', 'chauffeurs', 'affectations', 'clients', 'contrats',
            'pointages', 'maintenances', 'factures', 'paiements', 'charges',
            'entreprise', 'utilisateurs',
        ];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        // Journal d'audit : lecture seule, pas de create/update/delete (l'historique ne se modifie pas).
        Permission::firstOrCreate(['name' => 'audit.view', 'guard_name' => 'web']);

        $forModules = fn (array $modules) => collect($modules)
            ->crossJoin($actions)
            ->map(fn ($pair) => "{$pair[0]}.{$pair[1]}")
            ->all();

        $viewOnly = fn (array $modules) => collect($modules)
            ->map(fn ($module) => "{$module}.view")
            ->all();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'Gestionnaire de parc', 'guard_name' => 'web'])
            ->syncPermissions($forModules(['engins', 'chauffeurs', 'affectations', 'maintenances']));

        Role::firstOrCreate(['name' => 'Commercial', 'guard_name' => 'web'])
            ->syncPermissions($forModules(['clients', 'contrats']));

        Role::firstOrCreate(['name' => 'Superviseur de chantier', 'guard_name' => 'web'])
            ->syncPermissions([
                'pointages.view', 'pointages.create', 'pointages.update',
                'contrats.view', 'engins.view',
            ]);

        Role::firstOrCreate(['name' => 'Comptable', 'guard_name' => 'web'])
            ->syncPermissions($forModules(['factures', 'paiements', 'charges']));

        Role::firstOrCreate(['name' => 'Direction', 'guard_name' => 'web'])
            ->syncPermissions([...$viewOnly($modules), 'audit.view']);
    }
}
