<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Fixtures\InspectableBaseComponent;
use Tests\Fixtures\InspectableChildBaseComponent;
use Tests\Fixtures\SimpleNestedComponent;
use Tests\Support\ProjectTestCase;

final class BaseComponentTest extends ProjectTestCase
{
    public function testSetAttributeGetAttributeAndGetAttributes(): void
    {
        $component = new InspectableBaseComponent([]);
        $component->setAttribute('id', 'hero');

        self::assertSame('hero', $component->getAttribute('id'));
        self::assertSame(['id' => 'hero'], $component->getAttributes());
    }

    public function testAddClassHasClassAndPrependClass(): void
    {
        $component = new InspectableBaseComponent([]);
        $component->addClass('card');
        $component->prependClass('featured');

        self::assertTrue($component->hasClass('card'));
        self::assertTrue($component->hasClass('featured'));
        self::assertSame(['featured', 'card'], $component->getAttribute('class'));
    }

    public function testHtmlAttributesBuildsMarkup(): void
    {
        $component = new InspectableBaseComponent([]);
        $component->setAttribute('id', 'hero');
        $component->setAttribute('class', ['featured', 'card']);
        $component->setAttribute('href', '/docs');

        self::assertSame(' id="hero" class="featured card" href="/docs"', $component->htmlAttributesPublic());
    }

    public function testAddContentAppendsNewEntries(): void
    {
        $component = new InspectableBaseComponent([]);
        $component->addContent('Titel');
        $component->addContent(['Text']);

        self::assertSame(['Titel', 'Text'], $component->getContent());
    }

    public function testIsComponentObjectDetectsBaseComponentSubclasses(): void
    {
        $component = new InspectableBaseComponent([]);

        self::assertTrue($component->isComponentObjectPublic(new SimpleNestedComponent(['text' => 'Hallo'])));
        self::assertFalse($component->isComponentObjectPublic(new \stdClass()));
    }

    public function testGetPropertiesMergesInheritanceChain(): void
    {
        $component = new InspectableChildBaseComponent([]);
        $properties = $component->getProperties();

        self::assertArrayHasKey('attributes', $properties);
        self::assertArrayHasKey('data', $properties);
        self::assertSame('isString', $properties['attributes']['id']);
        self::assertSame('isString', $properties['attributes']['data-role']);
        self::assertSame('isString', $properties['data']['size']);
    }
}
