<?php

namespace Lenvendo\Declension;

use Lenvendo\Declension\Abstracts\NamesInflection;
use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Interfaces\RussianCases;
use \Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;


/**
 * Rules are from http://gramma.ru/SPR/?id=2.8
 */
class LastNamesInflection  extends NamesInflection implements Cases
{
    use RussianLanguage, CasesHelper;

    /** @var string[] */
    protected static $womenPostfixes = ['ва', 'на', 'ая', 'яя'];
    /** @var string[] */
    protected static $menPostfixes = ['ов', 'ев' ,'ин' ,'ын', 'ой', 'ий'];

    /**
     * @param string $name
     * @param string|null $gender
     * @return bool
     */
    public static function isMutable($name, $gender = null)
    {
        $name = MultibyteString::lower($name);
        if ($gender === null) {
            $gender = static::detectGender($name);
        }
        // составная фамилия - разбить на части и проверить по отдельности
        if (strpos($name, '-') !== false) {
            foreach (explode('-', $name) as $part) {
                if (static::isMutable($part, $gender))
                    return true;
            }
            return false;
        }

        if (in_array(MultibyteString::slice($name, -1), ['а', 'я'], true)) {
            return true;
        }

        // Несклоняемые фамилии независимо от пола (Токаревских)
        if (in_array(MultibyteString::slice($name, -2), ['их'], true))
            return false;

        if ($gender == Gender::MALE) {
            // Несклоняемые фамилии (Фоминых, Седых / Стецко, Писаренко)
            if (in_array(MultibyteString::slice($name, -2), ['ых', 'ко'], true))
                return false;

            // Несклоняемые, образованные из родительного падежа личного или прозвищного имени главы семьи
            // суффиксы: ово, аго
            if (in_array(MultibyteString::slice($name, -3), ['ово', 'аго'], true))
                return false;

            // Типичные суффикс мужских фамилий
            if (in_array(MultibyteString::slice($name, -2), ['ов', 'ев', 'ин', 'ын', 'ий', 'ой'], true)) {
                return true;
            }

            // Согласная на конце
            if (static::isConsonant(MultibyteString::slice($name, -1))) {
                return true;
            }

            // Мягкий знак на конце
            if (MultibyteString::slice($name, -1) == 'ь') {
                return true;
            }

        } else {
            // Типичные суффиксы женских фамилий
            if (in_array(MultibyteString::slice($name, -2), ['ва', 'на', 'ая'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public static function detectGender($name)
    {
        $name = MultibyteString::lower($name);
        if (in_array(MultibyteString::slice($name, -2), static::$menPostfixes, true)) {
            return Gender::MALE;
        }
        if (in_array(MultibyteString::slice($name, -2), Gender::$womenPostfixes, true)) {
            return static::FEMALE;
        }

        return null;
    }

    /**
     * @param string $name
     * @param null|string $gender
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($name, $gender = null)
    {
        $name = MultibyteString::lower($name);
        if ($gender === null) {
            $gender = static::detectGender($name);
        }

        // составная фамилия - разбить на части и склонять по отдельности
        if (strpos($name, '-') !== false) {
            $parts = explode('-', $name);
            $cases = [];
            foreach ($parts as $i => $part) {
                $parts[$i] = static::getCases($part, $gender);
            }

            return static::composeCasesFromWords($parts, '-');
        }

        if (static::isMutable($name, $gender)) {
            if ($gender == Gender::MALE) {
                if (in_array(MultibyteString::slice($name, -2), ['ов', 'ев', 'ин', 'ын'], true)) {
                    $prefix = MultibyteString::name($name);
                    return [
                        RussianCases::IMENIT => $prefix,
                        RussianCases::RODIT => $prefix.'а',
                        RussianCases::DAT => $prefix.'у',
                        RussianCases::VINIT => $prefix.'а',
                        RussianCases::TVORIT => $prefix.'ым',
                        RussianCases::PREDLOJ => $prefix.'е'
                    ];
                } elseif (in_array(MultibyteString::slice($name, -4), ['ский', 'ской', 'цкий', 'цкой'], true)) {
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2));
                    return [
                        RussianCases::IMENIT => MultibyteString::name($name),
                        RussianCases::RODIT => $prefix.'ого',
                        RussianCases::DAT => $prefix.'ому',
                        RussianCases::VINIT => $prefix.'ого',
                        RussianCases::TVORIT => $prefix.'им',
                        RussianCases::PREDLOJ => $prefix.'ом'
                    ];
                    // Верхний / Убогий / Толстой
                    // Верхнего / Убогого / Толстого
                    // Верхнему / Убогому / Толстому
                    // Верхним / Убогим / Толстым
                    // О Верхнем / Об Убогом / О Толстом
                } else if (in_array(MultibyteString::slice($name, -2), ['ой', 'ый', 'ий'], true)) {
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2));
                    return [
                        RussianCases::IMENIT => MultibyteString::name($name),
                        RussianCases::RODIT => $prefix.'ого',
                        RussianCases::DAT => $prefix.'ому',
                        RussianCases::VINIT => $prefix.'ого',
                        RussianCases::TVORIT => $prefix.'ым',
                        RussianCases::PREDLOJ => $prefix.'ом'
                    ];
                }

            } else {
                if (in_array(MultibyteString::slice($name, -3), ['ова', 'ева', 'ина', 'ына'], true)) {
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                    return [
                        RussianCases::IMENIT => MultibyteString::name($name),
                        RussianCases::RODIT => $prefix.'ой',
                        RussianCases::DAT => $prefix.'ой',
                        RussianCases::VINIT => $prefix.'у',
                        RussianCases::TVORIT => $prefix.'ой',
                        RussianCases::PREDLOJ => $prefix.'ой'
                    ];
                }

                if (in_array(MultibyteString::slice($name, -2), ['ая'], true)) {
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2));
                    return [
                        RussianCases::IMENIT => MultibyteString::name($name),
                        RussianCases::RODIT => $prefix.'ой',
                        RussianCases::DAT => $prefix.'ой',
                        RussianCases::VINIT => $prefix.'ую',
                        RussianCases::TVORIT => $prefix.'ой',
                        RussianCases::PREDLOJ => $prefix.'ой'
                    ];
                }

                if (in_array(MultibyteString::slice($name, -2), ['яя'], true)) {
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2));
                    return [
                        RussianCases::IMENIT => MultibyteString::name($name),
                        RussianCases::RODIT => $prefix.'ей',
                        RussianCases::DAT => $prefix.'ей',
                        RussianCases::VINIT => $prefix.'юю',
                        RussianCases::TVORIT => $prefix.'ей',
                        RussianCases::PREDLOJ => $prefix.'ей'
                    ];
                }
            }

            if (MultibyteString::slice($name, -1) == 'я') {
                $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                return [
                    RussianCases::IMENIT => MultibyteString::name($name),
                    RussianCases::RODIT => $prefix.'и',
                    RussianCases::DAT => $prefix.'е',
                    RussianCases::VINIT => $prefix.'ю',
                    RussianCases::TVORIT => $prefix.'ей',
                    RussianCases::PREDLOJ => $prefix.'е'
                ];
            } elseif (MultibyteString::slice($name, -1) == 'а') {
                $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                return [
                    RussianCases::IMENIT => MultibyteString::name($name),
                    RussianCases::RODIT => $prefix.(static::isDeafConsonant(MultibyteString::slice($name, -2, -1)) || MultibyteString::slice($name, -2)
                        == 'га' ? 'и' : 'ы'),
                    RussianCases::DAT => $prefix.'е',
                    RussianCases::VINIT => $prefix.'у',
                    RussianCases::TVORIT => $prefix.'ой',
                    RussianCases::PREDLOJ => $prefix.'е'
                ];
            } elseif (static::isConsonant(MultibyteString::slice($name, -1)) && MultibyteString::slice($name, -2) != 'ых') {
                $prefix = MultibyteString::name($name);
                return [
                    RussianCases::IMENIT => MultibyteString::name($name),
                    RussianCases::RODIT => $prefix.'а',
                    RussianCases::DAT => $prefix.'у',
                    RussianCases::VINIT => $prefix.'а',
                    RussianCases::TVORIT => $prefix.'ом',
                    RussianCases::PREDLOJ => $prefix.'е'
                ];
            } elseif (MultibyteString::slice($name, -1) == 'ь' && $gender == Gender::MALE) {
                $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                return [
                    RussianCases::IMENIT => MultibyteString::name($name),
                    RussianCases::RODIT => $prefix.'я',
                    RussianCases::DAT => $prefix.'ю',
                    RussianCases::VINIT => $prefix.'я',
                    RussianCases::TVORIT => $prefix.'ем',
                    RussianCases::PREDLOJ => $prefix.'е'
                ];
            }
        }

        $name = MultibyteString::name($name);
        return array_fill_keys([RussianCases::IMENIT, RussianCases::RODIT, RussianCases::DAT, RussianCases::VINIT, RussianCases::TVORIT, RussianCases::PREDLOJ], $name);
    }

    /**
     * @param string $name
     * @param string $case
     * @param null $gender
     * @return string
     * @throws \Exception
     */
    public static function getCase($name, $case, $gender = null)
    {
        if (!static::isMutable($name, $gender)) {
            return $name;
        } else {
            $case = static::canonizeCase($case);
            $forms = static::getCases($name, $gender);
            return $forms[$case];
        }
    }
}