<?php

namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class requiredException extends ValidationException
{
    protected $defaultTemplates = [
      self::MODE_DEFAULT => [
        self::STANDARD => '{{name}} is required',
      ],
      self::MODE_NEGATIVE => [
        self::STANDARD => '{{name}} is not required',
      ],
    ];
}
