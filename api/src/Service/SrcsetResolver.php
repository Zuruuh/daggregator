<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\String\UnicodeString;

use function Symfony\Component\String\u;

final readonly class SrcsetResolver
{
    public function getBestQualityUrl(string $srcset): string
    {
        $srcset = array_map(
            static fn (UnicodeString $s) => array_values(array_filter(
                $s->trim()->split(' '),
                static fn (UnicodeString $s) => !$s->isEmpty()
            )),
            u($srcset)->trim()->split(','),
        );

        /** @var list<array{url:string,quality:string}> */
        $srcset = array_map(
            static fn (array $src) => [
                'url' => $src[0]->toString(),
                'quality' => (int) $src[1]->trim('w')->toString(),
            ],
            $srcset
        );

        usort($srcset, static fn (array $a, array $b) => $b['quality'] <=> $a['quality']);

        return $srcset[0]['url'];
    }
}
