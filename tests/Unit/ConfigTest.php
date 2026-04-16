<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Config;

final class ConfigTest extends ProjectTestCase
{
    public function testSetAndGetSimpleValues(): void
    {
        Config::set('Vendor\\Package', [
            'feature' => [
                'enabled' => true,
            ],
        ]);

        self::assertTrue(Config::get('Vendor\\Package', 'feature.enabled'));
        self::assertSame(['enabled' => true], Config::get('Vendor\\Package', 'feature'));
    }

    public function testGlobalNamespaceIsMergedIntoOtherNamespaces(): void
    {
        Config::set('Vendor\\Package', [
            'wrapComponent' => [
                'tag' => 'section',
            ],
        ]);

        self::assertTrue(Config::get('Vendor\\Package', 'wrapComponent.wrap'));
        self::assertSame('section', Config::get('Vendor\\Package', 'wrapComponent.tag'));
        self::assertSame('cdp', Config::get('Vendor\\Package', 'docking.class'));
    }

    public function testMatchNamespaceReturnsExactLongestOrGlobalMatch(): void
    {
        Config::set('Vendor', ['name' => 'vendor']);
        Config::set('Vendor\\Package', ['name' => 'package']);

        self::assertSame('Vendor\\Package', Config::matchNamespace('Vendor\\Package'));
        self::assertSame('Vendor\\Package', Config::matchNamespace('Vendor\\Package\\Card'));
        self::assertSame('global', Config::matchNamespace('Unknown\\Package\\Card'));
    }
}
