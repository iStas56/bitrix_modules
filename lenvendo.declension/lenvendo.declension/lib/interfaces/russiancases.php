<?php

namespace Lenvendo\Declension\Interfaces;

interface RussianCases extends Cases
{
    const IMENIT_1 = self::NOMINATIVE;
    const RODIT_2 = self::GENITIVE;
    const DAT_3 = self::DATIVE;
    const VINIT_4 = self::ACCUSATIVE;
    const TVORIT_5 = self::ABLATIVE;
    const PREDLOJ_6 = self::PREPOSITIONAL;

    const IMENIT = self::NOMINATIVE;
    const RODIT = self::GENITIVE;
    const DAT = self::DATIVE;
    const VINIT = self::ACCUSATIVE;
    const TVORIT = self::ABLATIVE;
    const PREDLOJ = self::PREPOSITIONAL;
    const LOCATIVE = 'locative';

}