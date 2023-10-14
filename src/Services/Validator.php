<?php

namespace LaravelLiberu\Migrator\Services;

use Illuminate\Support\Collection;
use LaravelLiberu\Migrator\Exceptions\LiberuStructure;

class Validator
{
    public static function run(array $required, $attributes, string $element)
    {
        if (! is_array($attributes)) {
            throw LiberuStructure::invalidElement($element);
        }

        $diff = Collection::wrap($required)
            ->diff(Collection::wrap($attributes)->keys());

        if ($diff->isNotEmpty()) {
            throw LiberuStructure::missingAttributes($diff, $element);
        }
    }
}
