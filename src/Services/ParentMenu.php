<?php

namespace LaravelLiberu\Migrator\Services;

use Illuminate\Support\Collection;
use LaravelLiberu\Menus\Models\Menu;
use LaravelLiberu\Migrator\Exceptions\LiberuStructure;

class ParentMenu
{
    private readonly Collection $segments;

    public function __construct(private readonly string $menu)
    {
        $this->segments = new Collection(explode('.', $menu));
    }

    public function id(): int
    {
        $found = $this->matches()
            ->first(fn ($menu) => $this->found($menu));

        if ($found) {
            return $found->id;
        }

        throw LiberuStructure::invalidParentMenu($this->menu);
    }

    private function found($menu): bool
    {
        return (bool) $this->segments->reverse()
            ->reduce(fn ($match, $segment) => $this->advance($match, $segment), $menu);
    }

    private function advance($match, $segment)
    {
        return $match && $match->parent?->name === $segment
            ? $match->parent
            : false;
    }

    private function matches(): Collection
    {
        return Menu::isParent()
            ->whereName($this->segments->pop())
            ->get();
    }
}
