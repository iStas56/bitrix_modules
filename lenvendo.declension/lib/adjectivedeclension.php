<?php

namespace Lenvendo\Declension;

use \Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Interfaces\RussianCases;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;
use Lenvendo\Declension\Abstracts\BaseInflection;
use RuntimeException;

class AdjectiveDeclension extends BaseInflection implements Cases, Gender
{
    use RussianLanguage, CasesHelper;

    const HARD_BASE = 1;
    const SOFT_BASE = 2;
    const MIXED_BASE = 3;

    /**
     * @param string $adjective
     * @return bool|void
     */
    public static function isMutable($adjective)
    {
        return false;
    }

    /**
     * @param string $adjective
     * @param string $case
     * @param bool   $animateness
     * @param null   $gender
     *
     * @return string
     * @throws \Exception
     */
    public static function getCase($adjective, $case, $animateness = false, $gender = null)
    {
        $case = static::canonizeCase($case);

        if ($gender === null)
            $gender = static::detectGender($adjective);

        $forms = static::getCases($adjective, $animateness, $gender);
        return $forms[$case];
    }

    /**
     * @param string $adjective
     *
     * @param bool $isEmphasized
     *
     * @return string
     */
    public static function detectGender($adjective, &$isEmphasized = false)
    {
        switch (MultibyteString::lower(MultibyteString::slice($adjective, -2)))
        {
            case 'ой':
                $isEmphasized = true;
            case 'ый':
            case 'ий':
                return static::MALE;

            case 'ая':
            case 'яя':
                return static::FEMALE;

            case 'ое':
            case 'ее':
                return static::NEUTER;
        }

        throw new RuntimeException('Unreachable');
    }

    /**
     * @param string $adjective
     * @param bool $animateness
     * @param null|string $gender
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($adjective, $animateness = false, $gender = null)
    {
        if ($gender === null)
            $gender = static::detectGender($adjective, $isEmphasized);

        $last_consonant_vowel = MultibyteString::slice($adjective, -2, -1);
        $type = static::getAdjectiveBaseType($adjective);
        $adjective = MultibyteString::slice($adjective, 0, -2);

        switch ($type)
        {
            case static::HARD_BASE:
                return static::declinateHardAdjective($adjective, $animateness, $gender,
                    $last_consonant_vowel);

            case static::SOFT_BASE:
                return static::declinateSoftAdjective($adjective, $animateness, $gender,
                    $last_consonant_vowel);

            case static::MIXED_BASE:
                return static::declinateMixedAdjective($adjective, $animateness, $gender,
                    $last_consonant_vowel);
        }

        throw new RuntimeException('Unreachable');
    }

    /**
     * @param string $adjective
     *
     * @return int
     */
    public static function getAdjectiveBaseType($adjective)
    {
        $adjective = MultibyteString::lower($adjective);
        $consonants = static::$consonants;

        unset($consonants[array_search('н', $consonants)]);
        unset($consonants[array_search('й', $consonants)]);

        $substring = MultibyteString::findLastPositionForOneOfChars($adjective, $consonants);
        $last_consonant = MultibyteString::slice($substring, 0, 1);

        // г, к, х, ударное ш - признак смешанного прилагательно
        if (in_array($last_consonant, ['г', 'к', 'х'], true) ||
            (
                $last_consonant === 'ш' && in_array(MultibyteString::slice($substring, 1, 2), ['о', 'а'], true)
            )) {
            return static::MIXED_BASE;
        }

        return static::checkBaseLastConsonantSoftness($substring)
        || (MultibyteString::slice($substring, 0, 2) === 'шн')
            ? static::SOFT_BASE
            : static::HARD_BASE;
    }

    /**
     * @param string $adjective
     * @param bool   $animateness
     * @param string $gender
     * @param string $afterConsonantVowel
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected static function declinateHardAdjective($adjective, $animateness, $gender,
                                                     $afterConsonantVowel)
    {
        switch ($gender)
        {
            case static::MALE:
                $postfix = $afterConsonantVowel.'й';
                break;

            case static::FEMALE:
                $postfix = $afterConsonantVowel.'я';
                break;

            case static::NEUTER:
                $postfix = $afterConsonantVowel.'е';
                break;

            default: throw new RuntimeException('Unreachable');
        }

        $cases = [
            RussianCases::IMENIT => $adjective.$postfix,
            RussianCases::RODIT => $adjective.'о'.($gender !== static::FEMALE ? 'го' : 'й'),
            RussianCases::DAT => $adjective.'о'.($gender !== static::FEMALE ? 'му' : 'й'),
        ];

        if ($gender !== static::FEMALE)
            $cases[RussianCases::VINIT] = static::getVinitCaseByAnimateness($cases, $animateness);
        else
            $cases[RussianCases::VINIT] = $adjective.'ую';

        $cases[RussianCases::TVORIT] = $adjective.($gender !== static::FEMALE ? 'ым' : 'ой');
        $cases[RussianCases::PREDLOJ] = $adjective.($gender !== static::FEMALE ? 'ом' : 'ой');

        return $cases;
    }

    /**
     * @param string $adjective
     * @param bool   $animateness
     * @param string $gender
     * @param string $afterConsonantVowel
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected static function declinateSoftAdjective($adjective, $animateness, $gender, $afterConsonantVowel)
    {
        switch ($gender)
        {
            case static::MALE:
                $postfix = $afterConsonantVowel.'й';
                break;

            case static::FEMALE:
                $postfix = $afterConsonantVowel.'я';
                break;

            case static::NEUTER:
                $postfix = $afterConsonantVowel.'е';
                break;

            default: throw new RuntimeException('Unreachable');
        }

        $cases = [
            RussianCases::IMENIT => $adjective.$postfix,
            RussianCases::RODIT => $adjective.'е'.($gender !== static::FEMALE ? 'го' : 'й'),
            RussianCases::DAT => $adjective.'е'.($gender !== static::FEMALE ? 'му' : 'й'),
        ];

        if ($gender !== static::FEMALE)
            $cases[RussianCases::VINIT] = static::getVinitCaseByAnimateness($cases, $animateness);
        else
            $cases[RussianCases::VINIT] = $adjective.'юю';

        $cases[RussianCases::TVORIT] = $adjective.($gender !== static::FEMALE ? 'им' : 'ей');
        $cases[RussianCases::PREDLOJ] = $adjective.($gender !== static::FEMALE ? 'ем' : 'ей');

        return $cases;
    }

    /**
     * @param string $adjective
     * @param bool   $animateness
     * @param string $gender
     * @param string $afterConsonantVowel
     *
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected static function declinateMixedAdjective($adjective, $animateness, $gender, $afterConsonantVowel)
    {
        switch ($gender)
        {
            case static::MALE:
                $postfix = $afterConsonantVowel.'й';
                break;

            case static::FEMALE:
                $postfix = $afterConsonantVowel.'я';
                break;

            case static::NEUTER:
                $postfix = $afterConsonantVowel.'е';
                break;

            default: throw new RuntimeException('Unreachable');
        }

        $cases = [
            RussianCases::IMENIT => $adjective.$postfix,
            RussianCases::RODIT => $adjective.'о'.($gender !== static::FEMALE ? 'го' : 'й'),
            RussianCases::DAT => $adjective.'о'.($gender !== static::FEMALE ? 'му' : 'й'),
        ];

        if ($gender === static::MALE)
            $cases[RussianCases::VINIT] = static::getVinitCaseByAnimateness($cases, $animateness);
        else if ($gender === static::NEUTER)
            $cases[RussianCases::VINIT] = $adjective.'ое';
        else
            $cases[RussianCases::VINIT] = $adjective.'ую';

        $cases[RussianCases::TVORIT] = $adjective.($gender !== static::FEMALE ? 'им' : 'ой');
        $cases[RussianCases::PREDLOJ] = $adjective.($gender !== static::FEMALE ? 'ом' : 'ой');

        return $cases;
    }
}