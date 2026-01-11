<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"\\u0645\\u0648\\u0638\\u0641","guard_name":"web","permissions":["ViewAny:Attribute","View:Attribute","Create:Attribute","Update:Attribute","Delete:Attribute","Replicate:Attribute","Reorder:Attribute","ViewAny:Brand","View:Brand","Create:Brand","Update:Brand","Delete:Brand","Replicate:Brand","Reorder:Brand","ViewAny:Category","Delete:Category"]}]';
        $directPermissions = '{"15":{"name":"View:Category","guard_name":"web"},"16":{"name":"Create:Category","guard_name":"web"},"17":{"name":"Update:Category","guard_name":"web"},"19":{"name":"Replicate:Category","guard_name":"web"},"20":{"name":"Reorder:Category","guard_name":"web"},"21":{"name":"ViewAny:Product","guard_name":"web"},"22":{"name":"View:Product","guard_name":"web"},"23":{"name":"Create:Product","guard_name":"web"},"24":{"name":"Update:Product","guard_name":"web"},"25":{"name":"Delete:Product","guard_name":"web"},"26":{"name":"Replicate:Product","guard_name":"web"},"27":{"name":"Reorder:Product","guard_name":"web"},"28":{"name":"ViewAny:Cart","guard_name":"web"},"29":{"name":"View:Cart","guard_name":"web"},"30":{"name":"Create:Cart","guard_name":"web"},"31":{"name":"Update:Cart","guard_name":"web"},"32":{"name":"Delete:Cart","guard_name":"web"},"33":{"name":"Replicate:Cart","guard_name":"web"},"34":{"name":"Reorder:Cart","guard_name":"web"},"35":{"name":"ViewAny:Review","guard_name":"web"},"36":{"name":"View:Review","guard_name":"web"},"37":{"name":"Create:Review","guard_name":"web"},"38":{"name":"Update:Review","guard_name":"web"},"39":{"name":"Delete:Review","guard_name":"web"},"40":{"name":"Replicate:Review","guard_name":"web"},"41":{"name":"Reorder:Review","guard_name":"web"},"42":{"name":"ViewAny:UserAddress","guard_name":"web"},"43":{"name":"View:UserAddress","guard_name":"web"},"44":{"name":"Create:UserAddress","guard_name":"web"},"45":{"name":"Update:UserAddress","guard_name":"web"},"46":{"name":"Delete:UserAddress","guard_name":"web"},"47":{"name":"Replicate:UserAddress","guard_name":"web"},"48":{"name":"Reorder:UserAddress","guard_name":"web"},"49":{"name":"ViewAny:Wishlist","guard_name":"web"},"50":{"name":"View:Wishlist","guard_name":"web"},"51":{"name":"Create:Wishlist","guard_name":"web"},"52":{"name":"Update:Wishlist","guard_name":"web"},"53":{"name":"Delete:Wishlist","guard_name":"web"},"54":{"name":"Replicate:Wishlist","guard_name":"web"},"55":{"name":"Reorder:Wishlist","guard_name":"web"},"56":{"name":"ViewAny:ProductVariant","guard_name":"web"},"57":{"name":"View:ProductVariant","guard_name":"web"},"58":{"name":"Create:ProductVariant","guard_name":"web"},"59":{"name":"Update:ProductVariant","guard_name":"web"},"60":{"name":"Delete:ProductVariant","guard_name":"web"},"61":{"name":"Replicate:ProductVariant","guard_name":"web"},"62":{"name":"Reorder:ProductVariant","guard_name":"web"},"63":{"name":"ViewAny:StockMovement","guard_name":"web"},"64":{"name":"View:StockMovement","guard_name":"web"},"65":{"name":"Create:StockMovement","guard_name":"web"},"66":{"name":"Update:StockMovement","guard_name":"web"},"67":{"name":"Delete:StockMovement","guard_name":"web"},"68":{"name":"Replicate:StockMovement","guard_name":"web"},"69":{"name":"Reorder:StockMovement","guard_name":"web"},"70":{"name":"ViewAny:Order","guard_name":"web"},"71":{"name":"View:Order","guard_name":"web"},"72":{"name":"Create:Order","guard_name":"web"},"73":{"name":"Update:Order","guard_name":"web"},"74":{"name":"Delete:Order","guard_name":"web"},"75":{"name":"Replicate:Order","guard_name":"web"},"76":{"name":"Reorder:Order","guard_name":"web"},"77":{"name":"ViewAny:ReturnOrder","guard_name":"web"},"78":{"name":"View:ReturnOrder","guard_name":"web"},"79":{"name":"Create:ReturnOrder","guard_name":"web"},"80":{"name":"Update:ReturnOrder","guard_name":"web"},"81":{"name":"Delete:ReturnOrder","guard_name":"web"},"82":{"name":"Replicate:ReturnOrder","guard_name":"web"},"83":{"name":"Reorder:ReturnOrder","guard_name":"web"},"84":{"name":"ViewAny:Role","guard_name":"web"},"85":{"name":"View:Role","guard_name":"web"},"86":{"name":"Create:Role","guard_name":"web"},"87":{"name":"Update:Role","guard_name":"web"},"88":{"name":"Delete:Role","guard_name":"web"},"89":{"name":"Replicate:Role","guard_name":"web"},"90":{"name":"Reorder:Role","guard_name":"web"},"91":{"name":"ViewAny:User","guard_name":"web"},"92":{"name":"View:User","guard_name":"web"},"93":{"name":"Create:User","guard_name":"web"},"94":{"name":"Update:User","guard_name":"web"},"95":{"name":"Delete:User","guard_name":"web"},"96":{"name":"Replicate:User","guard_name":"web"},"97":{"name":"Reorder:User","guard_name":"web"},"98":{"name":"Restore:User","guard_name":"web"},"99":{"name":"ForceDelete:User","guard_name":"web"},"100":{"name":"View:ContactSettings","guard_name":"web"},"101":{"name":"View:GeneralSettings","guard_name":"web"},"102":{"name":"View:SocialMediaSettings","guard_name":"web"}}';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
