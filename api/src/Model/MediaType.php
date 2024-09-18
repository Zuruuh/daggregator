<?php

declare(strict_types=1);

namespace App\Model;

enum MediaType
{
    case Video;
    case Image;
    case TemporaryVideoStoredAsImage;

    public function toString(): string
    {
        return match ($this) {
            self::Video => 'video',
            self::Image => 'image',
            self::TemporaryVideoStoredAsImage => 'temporaryVideoStoredAsImage',
        };
    }

    public function asPath(): string
    {
        return $this->toString() . '/';
    }
}
