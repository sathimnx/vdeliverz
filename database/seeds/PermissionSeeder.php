<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Permission;
use \Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed the default permissions
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $perms) {
            Permission::firstOrCreate(['name' => $perms]);
        }

        $this->command->info('Default Permissions added.');
        // add admin role
        $role = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'vendor']);
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'delivery-boy']);

        // assign permission to admin
        $role->syncPermissions(Permission::all());
        $this->command->info('Admin granted all the permissions');

        // create admin user
        $this->createUser($role);
        $this->command->info('Admin Role Created Successfully!');
    }
    /**
     * Create a user with given role
     *
     * @param $role
     */
     private function createUser($role)
    {
        $user = User::create(['name' => 'Vmart Admin', 'email' => 'vmartadmin@admin.com', 'mobile' => '9876543210', 'password' => Hash::make('admin@vmart')]);
        $user->assignRole($role->name);

        if( $role->name == 'admin' ) {
            $this->command->info('Here is your admin details to login:');
            $this->command->warn($user->email);
            $this->command->warn('Password is "admin@vmart"');
        }
    }
}
