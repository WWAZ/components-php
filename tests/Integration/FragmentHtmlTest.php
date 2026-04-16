<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\ProjectTestCase;
use wwaz\Components\FragmentFactory;

final class FragmentHtmlTest extends ProjectTestCase
{
    public function testHtmlDivRendersContainerMarkup(): void
    {
        $fragment = FragmentFactory::make('html.div', [
            'content' => 'Hallo',
        ]);

        $html = $fragment->render();

        self::assertStringContainsString('<div>', $html);
        self::assertStringContainsString('Hallo', $html);
        self::assertStringContainsString('</div>', $html);
    }

    public function testHtmlAnchorRendersHrefAndText(): void
    {
        $fragment = FragmentFactory::make('html.a', [
            'href' => '/mehr',
            'content' => 'Mehr',
        ]);

        self::assertSame('<a href="/mehr">Mehr</a>', $fragment->render());
    }
}
