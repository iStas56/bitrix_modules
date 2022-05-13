<?php
namespace Lenvendo\Declension;

use \Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Abstracts\NamesInflection;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Interfaces\RussianCases;
use \Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;

/**
 * Rules are from http://surnameonline.ru/patronymic.html
 */
class MiddleNamesInflection extends NamesInflection implements Cases
{
    use RussianLanguage, CasesHelper;

    /**
     * @param string $name
     * @return null|string
     */
    public static function detectGender($name)
    {
        $name = MultibyteString::lower($name);
        if (MultibyteString::slice($name, -2) == 'ич') {
            return Gender::MALE;
        } elseif (MultibyteString::slice($name, -2) == 'на') {
            return Gender::FEMALE;
        }

        return null;
    }

    /**
     * @param string $name
     * @param null $gender
     * @return bool
     */
    public static function isMutable($name, $gender = null)
    {
        $name = MultibyteString::lower($name);
        if (in_array(MultibyteString::slice($name, -2), ['ич', 'на'], true)) {
            return true;
        }

        // it's foreign middle name, inflect it as a first name
        return FirstNamesInflection::isMutable($name, $gender);
    }

    /**
     * @param string $name
     * @param string $case
     * @param string|null $gender
     * @return string
     * @throws \Exception
     */
    public static function getCase($name, $case, $gender = null)
    {
        $case = static::canonizeCase($case);
        $forms = static::getCases($name, $gender);
        return $forms[$case];
    }

    /**
     * @param string $name
     * @param string|null $gender
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($name, $gender = null)
    {
        $name = MultibyteString::lower($name);
        if (MultibyteString::slice($name, -2) == 'ич') {
            // man rules
            $name = MultibyteString::name($name);
            return array(
                RussianCases::IMENIT => $name,
                RussianCases::RODIT => $name.'а',
                RussianCases::DAT => $name.'у',
                RussianCases::VINIT => $name.'а',
                RussianCases::TVORIT => $name.'ем',
                RussianCases::PREDLOJ => $name.'е',
            );
        } elseif (MultibyteString::slice($name, -2) == 'на') {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            return array(
                RussianCases::IMENIT => $prefix.'а',
                RussianCases::RODIT => $prefix.'ы',
                RussianCases::DAT => $prefix.'е',
                RussianCases::VINIT => $prefix.'у',
                RussianCases::TVORIT => $prefix.'ой',
                RussianCases::PREDLOJ => $prefix.'е',
            );
        }

        // inflect other middle names (foreign) as first names
        return FirstNamesInflection::getCases($name, $gender);
    }
}
