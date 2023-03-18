<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Permission;
use \Spatie\Permission\Models\Role;

class DemandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed the default permissions
        $permissions = Permission::demandPermissions();

        foreach ($permissions as $perms) {
            Permission::firstOrCreate(['name' => $perms]);
        }

        $this->command->info('Demand Permissions added.');
        // add admin role
        $role = Role::where('name', 'admin')->first();
        Role::firstOrCreate(['name' => 'provider']);

        // assign permission to admin
        $role->syncPermissions(Permission::all());
        $this->command->info('Admin granted all the permissions');

        // Create Demand Categories
        $categories = config('constants.demand_categories');
        foreach ($categories as $key => $category) {
            \App\Service::firstOrCreate(['name' => $category, 'order' => $key + 1]);
        }

    }
}