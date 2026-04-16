<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Render\StrapiRenderer;

final class StrapiRendererTest extends ProjectTestCase
{
    public function testRenderBuildsKnownComponentsAndUnknownFallbacks(): void
    {
        $renderer = new StrapiRenderer([
            [
                '__component' => 'card',
                'title' => 'Titel',
                'text' => 'Text',
            ],
            [
                '__component' => 'unknown-widget',
            ],
        ]);

        $html = $renderer->render();

        self::assertStringContainsString('<h3>Titel</h3>', $html);
        self::assertStringContainsString('unknown component: unknown-widget', $html);
        self::assertStringContainsString('class="unknown-component"', $html);
    }
}
