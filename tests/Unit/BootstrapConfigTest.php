<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Config;

final class BootstrapConfigTest extends ProjectTestCase
{
    public function testBootstrapLoadsOptionalExternalOverrideConfig(): void
    {
        $this->setStaticProperty(Config::class, 'config', []);

        $overridePath = dirname(__DIR__) . '/Fixtures/bootstrap-config-override.php';
        putenv('WWAZ_COMPONENTS_CONFIG=' . $overridePath);

        try {
            require dirname(__DIR__, 2) . '/bootstrap/config.php';

            self::assertSame([
                'wrap' => false,
                'tag' => 'section',
                'class' => null,
            ], Config::get('global', 'wrapComponent'));

            self::assertSame([
                'wrap' => true,
                'tag' => 'div',
                'class' => 'cdp',
            ], Config::get('global', 'docking'));
        } finally {
            putenv('WWAZ_COMPONENTS_CONFIG');
        }
    }
}
