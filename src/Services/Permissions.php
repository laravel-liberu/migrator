<?php

namespace LaravelLiberu\Migrator\Services;

use Illuminate\Support\Collection;
use LaravelLiberu\Permissions\Models\Permission;
use LaravelLiberu\Roles\Models\Role;

class Permissions
{
    private const Attributes = ['name', 'description', 'is_default'];

    private readonly Collection $permissions;
    private Collection $roleIds;
    private ?int $defaultRoleId = null;

    public function __construct(?array $permissions)
    {
        $this->permissions = new Collection($permissions);
    }

    public function up()
    {
        if ($this->permissions->isEmpty()) {
            return;
        }

        $this->validate()
            ->roleIds()
            ->defaultRoleId()
            ->permissions();
    }

    public function down()
    {
        if ($this->permissions->isNotEmpty()) {
            $this->validate()
                ->destroy();
        }
    }

    private function permissions(): void
    {
        $this->permissions->each(fn ($permission) => $this->create($permission));
    }

    private function create($permission): void
    {
        Permission::create($permission)
            ->roles()->attach($this->roles($permission));
    }

    private function destroy(): void
    {
        Permission::whereIn('name', $this->permissions->pluck('name'))->delete();
    }

    private function roles($permission)
    {
        return $permission['is_default']
            ? $this->roleIds
            : $this->defaultRoleId;
    }

    private function roleIds(): self
    {
        $this->roleIds = Role::pluck('id');

        return $this;
    }

    private function defaultRoleId(): self
    {
        $role = Role::whereName(config('enso.config.defaultRole'))->first();

        $this->defaultRoleId = $role?->id;

        return $this;
    }

    private function validate(): self
    {
        $this->permissions->each(
            fn ($permission) => Validator::run(self::Attributes, $permission, 'permissions')
        );

        return $this;
    }
}
