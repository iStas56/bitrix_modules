<?php

namespace Lenvendo\Declension\Abstracts;

use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use RuntimeException;

abstract class NumeralGenerator implements Cases, Gender
{
    /**
     * @abstract
     * @param int $number
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($number) {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @abstract
     * @param int $number
     * @param string $case
     *
     * @return string
     */
    public static function getCase($number, $case) {
        throw new RuntimeException('Not implemented');
    }
}