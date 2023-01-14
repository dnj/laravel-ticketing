<?php

namespace dnj\Ticket\Casts;

use dnj\Filesystem\Contracts\IFile;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class File implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return unserialize($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value instanceof IFile) {
            throw new \InvalidArgumentException('The given value is not an File instance.');
        }

        return serialize($value);
    }
}
