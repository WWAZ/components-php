<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Componenttest\Card;
use wwaz\Components\Factory;

final class FactoryTest extends ProjectTestCase
{
    public function testCreatePossibleVariantsHandlesSimpleSnakeCaseAndDotSeparatedNames(): void
    {
        $simpleVariants = $this->invokeMethod(Factory::class, 'createPossibleVariants', ['card']);
        $snakeCaseVariants = $this->invokeMethod(Factory::class, 'createPossibleVariants', ['banner_hero']);
        $dotSeparatedVariants = $this->invokeMethod(Factory::class, 'createPossibleVariants', ['foo.bar']);

        self::assertContains('wwaz\\Components\\Componenttest\\Card', $simpleVariants);
        self::assertContains('wwaz\\Components\\Componenttest\\BannerHero', $snakeCaseVariants);
        self::assertContains('wwaz\\Components\\Componenttest\\Foo\\Bar', $dotSeparatedVariants);
    }

    public function testCamelCaseConvertsSnakeCaseNames(): void
    {
        self::assertSame('BannerHero', $this->invokeMethod(Factory::class, 'camelCase', ['banner_hero']));
    }

    public function testExistsReturnsClassNameForKnownComponent(): void
    {
        self::assertSame(Card::class, Factory::exists('Card'));
    }

    public function testMakeBuildsKnownComponent(): void
    {
        $component = Factory::make('Card', [
            'title' => 'Titel',
            'text' => 'Text',
        ]);

        self::assertInstanceOf(Card::class, $component);
    }

    public function testMakeThrowsExceptionForUnknownComponent(): void
    {
        $this->expectException(\Exception::class);

        Factory::make('MissingComponent', []);
    }

    public function testKeyIsComponentNameReturnsResolvedTypeForKnownNames(): void
    {
        self::assertFalse(Factory::keyIsComponentName('0'));
        self::assertSame('callaction', Factory::keyIsComponentName('callaction'));
    }
}
