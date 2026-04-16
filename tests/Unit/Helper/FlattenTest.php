<?php

declare(strict_types=1);

namespace Tests\Unit\Helper;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Helper\Arrays\Flatten;

final class FlattenTest extends ProjectTestCase
{
    public function testFlattenConvertsNestedArraysToDotNotation(): void
    {
        $result = Flatten::flatten([
            'card' => [
                'title' => 'Hallo',
                'meta' => [
                    'variant' => 'primary',
                ],
            ],
        ]);

        self::assertSame([
            'card.title' => 'Hallo',
            'card.meta.variant' => 'primary',
        ], $result);
    }

    public function testDeflattenRebuildsNestedArrays(): void
    {
        $result = Flatten::deflatten([
            'card.title' => 'Hallo',
            'card.meta.variant' => 'primary',
        ]);

        self::assertSame([
            'card' => [
                'title' => 'Hallo',
                'meta' => [
                    'variant' => 'primary',
                ],
            ],
        ], $result);
    }

    public function testFlattenAndDeflattenRoundTrip(): void
    {
        $data = [
            'content' => [
                ['title' => 'A'],
                ['title' => 'B'],
            ],
        ];

        self::assertSame($data, Flatten::deflatten(Flatten::flatten($data)));
    }
}
