<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Componenttest\Callaction;

final class CallactionTest extends ProjectTestCase
{
    public function testRenderCreatesAnchorWithCallactionClassAndText(): void
    {
        $callaction = new Callaction([
            'text' => 'Kontakt',
        ]);
        $callaction->setAttribute('class', 'callaction');

        self::assertSame('<a class="callaction">Kontakt</a>', $callaction->render(false));
    }
}
