<?php

declare(strict_types=1);

namespace Tests\Unit;

use ReflectionMethod;
use Tests\Fixtures\InspectableComponent;
use Tests\Support\ProjectTestCase;
use wwaz\Components\Componenttest\Card;
use wwaz\Components\Config;
use wwaz\Components\Factory;

final class ApiContractTest extends ProjectTestCase
{
    public function testFactoryPublicMethodSignaturesStayStable(): void
    {
        self::assertSame(1, (new ReflectionMethod(Factory::class, 'addNamespace'))->getNumberOfParameters());
        self::assertSame(1, (new ReflectionMethod(Factory::class, 'setThrowErrors'))->getNumberOfParameters());
        self::assertSame(2, (new ReflectionMethod(Factory::class, 'make'))->getNumberOfParameters());
        self::assertSame(1, (new ReflectionMethod(Factory::class, 'exists'))->getNumberOfParameters());
        self::assertSame(1, (new ReflectionMethod(Factory::class, 'keyIsComponentName'))->getNumberOfParameters());
    }

    public function testFactoryContractForKnownAndUnknownComponents(): void
    {
        self::assertSame(Card::class, Factory::exists('Card'));
        self::assertFalse(Factory::exists('MissingComponent'));
        self::assertSame('callaction', Factory::keyIsComponentName('callaction'));
    }

    public function testComponentRenderContractIsStable(): void
    {
        $component = new InspectableComponent([
            'title' => 'Hero',
        ]);

        self::assertSame('<section data-title="Hero">Body</section>', $component->render(false));
        self::assertIsString($component->render());

        $component->setWrapTag('article');
        self::assertStringStartsWith('<article class="', $component->render());
    }

    public function testConfigSetGetAndNamespaceMatchingContractIsStable(): void
    {
        Config::set('Vendor\\Package', [
            'feature' => [
                'enabled' => true,
            ],
        ]);

        self::assertTrue(Config::get('Vendor\\Package', 'feature.enabled'));
        self::assertSame(['enabled' => true], Config::get('Vendor\\Package', 'feature'));
        self::assertSame('Vendor\\Package', Config::matchNamespace('Vendor\\Package\\Card'));
        self::assertSame('global', Config::matchNamespace('Unknown\\Package\\Card'));
    }
}
