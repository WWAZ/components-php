<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Fixtures\InspectableComponent;
use Tests\Support\ProjectTestCase;

final class ComponentTest extends ProjectTestCase
{
    public function testAddComponentClassAndHasComponentClass(): void
    {
        $component = new InspectableComponent([
            'title' => 'Hero',
        ]);

        $component->addComponentClass('highlighted');

        self::assertTrue($component->hasComponentClass('highlighted'));
        self::assertSame(['highlighted'], $component->getComponentClasses());
        self::assertStringContainsString('highlighted', $component->getComponentClassPublic());
    }

    public function testSetWrapTagOverridesConfiguredWrapTag(): void
    {
        $component = new InspectableComponent([
            'title' => 'Hero',
        ]);

        $component->setWrapTag('article');

        self::assertStringStartsWith('<article class="', $component->render());
    }

    public function testRenderWithoutComponentWrapReturnsRawMarkup(): void
    {
        $component = new InspectableComponent([
            'title' => 'Hero',
        ]);

        self::assertSame('<section data-title="Hero">Body</section>', $component->render(false));
    }

    public function testGetComponentClassContainsClassNameAndAdditionalClasses(): void
    {
        $component = new InspectableComponent([
            'title' => 'Hero',
        ]);

        $component->addComponentClass('primary');

        $className = $component->getComponentClassPublic();

        self::assertStringContainsString('inspectablecomponent', $className);
        self::assertStringContainsString('primary', $className);
    }

    public function testGetDataReturnsNullWhenDataKeyIsMissingAndArrayWhenPresent(): void
    {
        $componentWithoutData = new InspectableComponent([
            'title' => 'Hero',
        ]);
        $componentWithData = new InspectableComponent([
            'title' => 'Hero',
            'variant' => 'primary',
        ]);

        self::assertNull($componentWithoutData->getData());
        self::assertSame(['variant' => 'primary'], $componentWithData->getData());
        self::assertSame('primary', $componentWithData->getData('variant'));
    }
}
