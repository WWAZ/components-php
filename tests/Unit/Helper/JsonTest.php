<?php

declare(strict_types=1);

namespace Tests\Unit\Helper;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Helper\Strings\Json;

final class JsonTest extends ProjectTestCase
{
    public function testIsJsonRecognizesValidAndInvalidJsonStrings(): void
    {
        self::assertTrue((bool) Json::isJson('{"title":"Hallo"}'));
        self::assertFalse((bool) Json::isJson('not-json'));
    }

    public function testToArrayDecodesJsonIntoAssociativeArray(): void
    {
        self::assertSame(['title' => 'Hallo'], Json::toArray('{"title":"Hallo"}'));
    }
}
