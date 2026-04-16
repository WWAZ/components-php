<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Fragment\Html\A;
use wwaz\Components\FragmentFactory;

final class FragmentFactoryTest extends ProjectTestCase
{
    public function testMakeReturnsFragmentInstanceForKnownType(): void
    {
        $fragment = FragmentFactory::make('html.a', [
            'href' => '/docs',
            'target' => 'null',
            'content' => 'Mehr',
        ]);

        self::assertInstanceOf(A::class, $fragment);
    }

    public function testMakeReturnsFalseWithoutDotSeparatedType(): void
    {
        self::assertFalse(FragmentFactory::make('html', []));
    }

    public function testMakeThrowsExceptionForUnknownType(): void
    {
        $this->expectException(\Exception::class);

        FragmentFactory::make('html.unknown', []);
    }
}
