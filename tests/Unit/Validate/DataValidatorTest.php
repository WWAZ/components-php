<?php

declare(strict_types=1);

namespace Tests\Unit\Validate;

use Tests\Support\ProjectTestCase;
use wwaz\Components\Validate\DataValidator;

final class DataValidatorTest extends ProjectTestCase
{
    public function testMissingRequiredFieldProducesErrors(): void
    {
        $validator = new DataValidator([
            'title' => 'required|isString',
        ], []);

        $result = $validator->validate();

        self::assertNotEmpty($result['errors']);
    }

    public function testDefaultValueIsAppliedWhenFieldIsMissing(): void
    {
        $validator = new DataValidator([
            'status' => 'default:active',
        ], []);

        $result = $validator->validate();

        self::assertSame('active', $result['data']['status']);
    }

    public function testContentStringIsWrappedIntoArray(): void
    {
        $validator = new DataValidator([
            'content' => '*',
        ], [
            'content' => 'Hallo',
        ]);

        $result = $validator->validate();

        self::assertSame(['Hallo'], $result['data']['content']);
    }

    public function testValidDataReturnsNoErrors(): void
    {
        $validator = new DataValidator([
            'title' => 'isString',
            'amount' => 'isNumber',
        ], [
            'title' => 'Hallo',
            'amount' => 3,
        ]);

        $result = $validator->validate();

        self::assertSame([], $result['errors']);
        self::assertSame('Hallo', $result['data']['title']);
        self::assertSame(3, $result['data']['amount']);
    }
}
