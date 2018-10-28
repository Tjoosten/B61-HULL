<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};
use Illuminate\Database\Eloquent\Collection;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param  Permission   $permissions The ACL permissions storage model. 
     * @param  Role         $role        The ACL roles storage model. 
     * @return void
     */
    public function run(Permission $permissions, Role $roles): void
    {
        // Seed default permissions 
        foreach ($this->defaultPermissions() as $permission)  { 
            $permissions->firsOrCreate(['name' => trim($permission)]);
        }

        $this->command->info('Default permissions added.');

        if ($this->command->confirm('Create roles for user(s), default is admin, leader and user.', true)) { 
            // Confirm the roles needed in the application. 
            $inputRoles = $this->command->ask('Enter roles in comma separated format.', 'admin,user'); // Ask roles from input. 
            
            foreach (explode(',', $inputRoles) as $role) {
                $role = $roles->firstOrCreate(['name' => trim($role)]);
                
                if ($this->isAdmin($role->name)) { // Assign all permissions
                    $role->syncPermissions($permissions->all());
                    $this->command->info('Admin granted all permissions');
                } else { // For others by default only read access
                    $role->syncPermissions($this->getUserPermissions());
                }

                $this->createUser($role); // Create one user for each role.
            }
        } else {
            $roles->firstOrCreate(['name' => 'user']);
            $this->command->info('Added only default user role.');
        }
    }

    /**
     * The array with the application his default permissions. 
     * 
     * @return array
     */
    private function defaultPermissions(): array
    {
        return [];
    }

    /**
     * Get the permissions in the application for the normal users. 
     * 
     * @return Collection
     */
    private function getUserPermissions(): Collection
    {
        return Permission::where('name', 'LIKE', 'view_%')->get();
    }

    /**
     * Conditional to check if the create role is admin 
     * 
     * @param  string $role The name from the create role. 
     * @return bool 
     */
    private function isAdmin(string $role): bool 
    {
        return $role === 'admin'; 
    }

    /**
     * Create an user with the given role. 
     * 
     * @param  Role $role The resource entity from the role. 
     * @return void
     */
    private function createUser(Role $role): void 
    {
        $user = factory(User::class)->create(['password' => 'secret'])->assignRole($role->name);
       
        if ($this->isAdmin($role->name)) {
            $this->command->info('Here are your admin details to login:'); 
            $this->command->warn($user->email); 
            $this->command->warn('Password is "secret"');
        }
    }
}
