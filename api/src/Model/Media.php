<?php

declare(strict_types=1);

namespace App\Model;

interface Media
{
    public const INDEX_NAME = 'medias';

    public function getId(): string;

    public function getPlatform(): Platform;

    public function getType(): ?MediaType;

    /**
     * @return list<string>
     */
    public function getUrls(): array;
}
