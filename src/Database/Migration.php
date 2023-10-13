<?php

namespace LaravelLiberu\Migrator\Database;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Facades\DB;
use LaravelLiberu\Migrator\Services\Menus;
use LaravelLiberu\Migrator\Services\Permissions;

abstract class Migration extends LaravelMigration
{
    protected array $permissions = [];
    protected array $menu = [];
    protected ?string $parentMenu = null;

    public function up()
    {
        DB::transaction(function () {
            (new Permissions($this->permissions))->up();
            (new Menus($this->menu, $this->parentMenu))->up();
        });
    }

    public function down()
    {
        DB::transaction(function () {
            (new Menus($this->menu, $this->parentMenu))->down();
            (new Permissions($this->permissions))->down();
        });
    }
}
