<?php

declare(strict_types=1);

namespace App\Model;

enum Platform
{
    case Twitter;
    case Reddit;

    public function toString(): string
    {
        return match ($this) {
            self::Twitter => 'Twitter',
            self::Reddit => 'Reddit',
        };
    }

    public function asPath(): string
    {
        return strtolower($this->toString()) . '/';
    }

    public function asId(string $id): string
    {
        return strtolower($this->toString()) . "-$id";
    }
}
