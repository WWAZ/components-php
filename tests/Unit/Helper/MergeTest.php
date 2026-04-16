<?php

declare(strict_types=1);

namespace Tests\Unit\Helper;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Helper\Arrays\Merge;

final class MergeTest extends ProjectTestCase
{
    public function testMergeRecursivelyOverwritesValuesFromSecondArray(): void
    {
        $left = [
            'wrapComponent' => [
                'wrap' => true,
                'tag' => 'div',
            ],
        ];
        $right = [
            'wrapComponent' => [
                'tag' => 'section',
                'class' => 'component',
            ],
        ];

        $result = Merge::merge($left, $right);

        self::assertSame([
            'wrapComponent' => [
                'wrap' => true,
                'tag' => 'section',
                'class' => 'component',
            ],
        ], $result);
    }
}
