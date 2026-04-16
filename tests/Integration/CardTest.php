<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Componenttest\Callaction;
use wwaz\Components\Componenttest\Card;

final class CardTest extends ProjectTestCase
{
    public function testRenderContainsMainCardMarkup(): void
    {
        $card = new Card([
            'title' => 'Titel',
            'text' => 'Text',
        ]);

        $html = $card->render();

        self::assertStringContainsString('<h3>Titel</h3>', $html);
        self::assertStringContainsString('<p>Text</p>', $html);
    }

    public function testRenderIncludesOptionalSublineOnlyWhenProvided(): void
    {
        $withoutSubline = new Card([
            'title' => 'Titel',
            'text' => 'Text',
        ]);
        $withSubline = new Card([
            'title' => 'Titel',
            'text' => 'Text',
            'subline' => 'Unterzeile',
        ]);

        self::assertStringNotContainsString('class="subline"', $withoutSubline->render());
        self::assertStringContainsString('class="subline"', $withSubline->render());
        self::assertStringContainsString('Unterzeile', $withSubline->render());
    }

    public function testRenderIncludesCallactionWhenProvided(): void
    {
        $callaction = new Callaction([
            'text' => 'Mehr',
        ]);
        $callaction->setAttribute('class', 'callaction');

        $card = new Card([
            'title' => 'Titel',
            'text' => 'Text',
            'callaction' => $callaction,
        ]);

        self::assertStringContainsString('<a class="callaction">Mehr</a>', $card->render());
    }
}
