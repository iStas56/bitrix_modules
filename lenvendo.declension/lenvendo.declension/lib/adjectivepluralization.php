<?php

namespace Lenvendo\Declension;

use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\RussianCases;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;
use Lenvendo\Declension\Abstracts\BasePluralization;
use RuntimeException;

class AdjectivePluralization extends BasePluralization implements Cases
{
    use RussianLanguage, CasesHelper;

    /**
     * @param string $adjective
     * @param int  $count
     * @param bool $animateness
     * @param string|null $case
     *
     * @return string|void
     * @throws \Exception
     */
    public static function pluralize($adjective, $count = 2, $animateness = false, $case = null)
    {
        // меняем местами аргументы, если они переданы в старом формате
        // @phpstan-ignore-next-line
        if (is_string($count) && is_numeric($adjective)) {
            list($count, $adjective) = [$adjective, $count];
        }

        if ($case !== null)
            $case = static::canonizeCase($case);

        if ($case === null) {
            switch (NounPluralization::getNumeralForm($count)) {
                case NounPluralization::ONE:
                    return $adjective;
                case NounPluralization::TWO_FOUR:
//                    return AdjectiveDeclension::getCase($adjective, RussianCases::RODIT, $animateness);
                case NounPluralization::FIVE_OTHER:
                    return AdjectivePluralization::getCase($adjective, RussianCases::RODIT, $animateness);
            }
        }

        if (NounPluralization::getNumeralForm($count) == NounPluralization::ONE)
            return AdjectiveDeclension::getCase($adjective, $case, $animateness);
        else
            return AdjectivePluralization::getCase($adjective, $case, $animateness);
    }

    /**
     * @param string $adjective
     * @param string $case
     * @param bool $animateness
     *
     * @return string
     * @throws \Exception
     */
    public static function getCase($adjective, $case, $animateness = false)
    {
        $case = static::canonizeCase($case);
        $forms = static::getCases($adjective, $animateness);
        return $forms[$case];
    }

    /**
     * @param string $adjective
     * @param bool $animateness
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($adjective, $animateness = false)
    {
        $type = AdjectiveDeclension::getAdjectiveBaseType($adjective);
        $adjective = MultibyteString::slice($adjective, 0, -2);
        switch ($type)
        {
            case AdjectiveDeclension::HARD_BASE:
                $cases = [
                    RussianCases::IMENIT => $adjective.'ые',
                    RussianCases::RODIT => $adjective.'ых',
                    RussianCases::DAT => $adjective.'ым',
                ];

                $cases[RussianCases::VINIT] = static::getVinitCaseByAnimateness($cases, $animateness);

                $cases[RussianCases::TVORIT] = $adjective.'ыми';
                $cases[RussianCases::PREDLOJ] = $adjective.'ых';

                return $cases;

            case AdjectiveDeclension::SOFT_BASE:
            case AdjectiveDeclension::MIXED_BASE:
                $cases = [
                    RussianCases::IMENIT => $adjective.'ие',
                    RussianCases::RODIT => $adjective.'их',
                    RussianCases::DAT => $adjective.'им',
                ];

                $cases[RussianCases::VINIT] = static::getVinitCaseByAnimateness($cases, $animateness);

                $cases[RussianCases::TVORIT] = $adjective.'ими';
                $cases[RussianCases::PREDLOJ] = $adjective.'их';

                return $cases;
        }

        throw new RuntimeException('Unreachable');
    }
}