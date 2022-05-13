<?php

namespace Lenvendo\Declension;

use Lenvendo\Declension\Abstracts\NumeralGenerator;
use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Interfaces\RussianCases;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;
use RuntimeException;

class CardinalNumeralGenerator extends NumeralGenerator implements Cases
{
    use RussianLanguage, CasesHelper;

    /**
     * @var string[]
     * @phpstan-var array<int, string>
     */
    protected static $words = [
        1 => 'один',
        2 => 'два',
        3 => 'три',
        4 => 'четыре',
        5 => 'пять',
        6 => 'шесть',
        7 => 'семь',
        8 => 'восемь',
        9 => 'девять',
        10 => 'десять',
        11 => 'одиннадцать',
        12 => 'двенадцать',
        13 => 'тринадцать',
        14 => 'четырнадцать',
        15 => 'пятнадцать',
        16 => 'шестнадцать',
        17 => 'семнадцать',
        18 => 'восемнадцать',
        19 => 'девятнадцать',
        20 => 'двадцать',
        30 => 'тридцать',
        40 => 'сорок',
        50 => 'пятьдесят',
        60 => 'шестьдесят',
        70 => 'семьдесят',
        80 => 'восемьдесят',
        90 => 'девяносто',
        100 => 'сто',
        200 => 'двести',
        300 => 'триста',
        400 => 'четыреста',
        500 => 'пятьсот',
        600 => 'шестьсот',
        700 => 'семьсот',
        800 => 'восемьсот',
        900 => 'девятьсот',
    ];

    /**
     * @var string[]
     * @phpstan-var array<int, string>
     */
    protected static $exponents = [
        1000 => 'тысяча',
        1000000 => 'миллион',
        1000000000 => 'миллиард',
        1000000000000 => 'триллион',
        1000000000000000 => 'квадриллион',
    ];

    /**
     * @var array
     * @phpstan-var array<string, array<string, array<string, string>|string>>
     */
    protected static $precalculated = [
        'один' => [
            Gender::MALE => [
                RussianCases::IMENIT => 'один',
                RussianCases::RODIT => 'одного',
                RussianCases::DAT => 'одному',
                RussianCases::VINIT => 'один',
                RussianCases::TVORIT => 'одним',
                RussianCases::PREDLOJ => 'одном',
            ],
            Gender::FEMALE => [
                RussianCases::IMENIT => 'одна',
                RussianCases::RODIT => 'одной',
                RussianCases::DAT => 'одной',
                RussianCases::VINIT => 'одну',
                RussianCases::TVORIT => 'одной',
                RussianCases::PREDLOJ => 'одной',
            ],
            Gender::NEUTER => [
                RussianCases::IMENIT => 'одно',
                RussianCases::RODIT => 'одного',
                RussianCases::DAT => 'одному',
                RussianCases::VINIT => 'одно',
                RussianCases::TVORIT => 'одним',
                RussianCases::PREDLOJ => 'одном',
            ],
        ],
        'два' => [
            Gender::MALE => [
                RussianCases::IMENIT => 'два',
                RussianCases::RODIT => 'двух',
                RussianCases::DAT => 'двум',
                RussianCases::VINIT => 'два',
                RussianCases::TVORIT => 'двумя',
                RussianCases::PREDLOJ => 'двух',
            ],
            Gender::FEMALE => [
                RussianCases::IMENIT => 'две',
                RussianCases::RODIT => 'двух',
                RussianCases::DAT => 'двум',
                RussianCases::VINIT => 'две',
                RussianCases::TVORIT => 'двумя',
                RussianCases::PREDLOJ => 'двух',
            ],
            Gender::NEUTER => [
                RussianCases::IMENIT => 'два',
                RussianCases::RODIT => 'двух',
                RussianCases::DAT => 'двум',
                RussianCases::VINIT => 'два',
                RussianCases::TVORIT => 'двумя',
                RussianCases::PREDLOJ => 'двух',
            ],
        ],
        'три' => [
            RussianCases::IMENIT => 'три',
            RussianCases::RODIT => 'трех',
            RussianCases::DAT => 'трем',
            RussianCases::VINIT => 'три',
            RussianCases::TVORIT => 'тремя',
            RussianCases::PREDLOJ => 'трех',
        ],
        'четыре' => [
            RussianCases::IMENIT => 'четыре',
            RussianCases::RODIT => 'четырех',
            RussianCases::DAT => 'четырем',
            RussianCases::VINIT => 'четыре',
            RussianCases::TVORIT => 'четырьмя',
            RussianCases::PREDLOJ => 'четырех',
        ],
        'двести' => [
            RussianCases::IMENIT => 'двести',
            RussianCases::RODIT => 'двухсот',
            RussianCases::DAT => 'двумстам',
            RussianCases::VINIT => 'двести',
            RussianCases::TVORIT => 'двумястами',
            RussianCases::PREDLOJ => 'двухстах',
        ],
        'восемьсот' => [
            RussianCases::IMENIT => 'восемьсот',
            RussianCases::RODIT => 'восьмисот',
            RussianCases::DAT => 'восьмистам',
            RussianCases::VINIT => 'восемьсот',
            RussianCases::TVORIT => 'восьмистами',
            RussianCases::PREDLOJ => 'восьмистах',
        ],
        'тысяча' => [
            RussianCases::IMENIT => 'тысяча',
            RussianCases::RODIT => 'тысяч',
            RussianCases::DAT => 'тысячам',
            RussianCases::VINIT => 'тысяч',
            RussianCases::TVORIT => 'тысячей',
            RussianCases::PREDLOJ => 'тысячах',
        ],
    ];

    /**
     * @param int $number
     * @param string $gender
     * @return string[]
     * @phpstan-return array<string, string>
     * @throws \Exception
     */
    public static function getCases($number, $gender = Gender::MALE)
    {
        // simple numeral
        if (isset(static::$words[$number]) || isset(static::$exponents[$number])) {
            $word = isset(static::$words[$number]) ? static::$words[$number] : static::$exponents[$number];
            if (isset(static::$precalculated[$word])) {
                if (isset(static::$precalculated[$word][static::MALE])) {
                    return static::$precalculated[$word][$gender];
                } else {
                    return static::$precalculated[$word];
                }
            } elseif (($number >= 5 && $number <= 20) || $number == 30) {
                $prefix = MultibyteString::slice($word, 0, -1);
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'и',
                    RussianCases::DAT => $prefix.'и',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => $prefix.'ью',
                    RussianCases::PREDLOJ => $prefix.'и',
                ];
            } elseif (in_array($number, [40, 90, 100])) {
                $prefix = $number == 40 ? $word : MultibyteString::slice($word, 0, -1);
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'а',
                    RussianCases::DAT => $prefix.'а',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => $prefix.'а',
                    RussianCases::PREDLOJ => $prefix.'а',
                ];
            } elseif (($number >= 50 && $number <= 80)) {
                $prefix = MultibyteString::slice($word, 0, -6);
                return [
                    RussianCases::IMENIT => $prefix.'ьдесят',
                    RussianCases::RODIT => $prefix.'идесяти',
                    RussianCases::DAT => $prefix.'идесяти',
                    RussianCases::VINIT => $prefix.'ьдесят',
                    RussianCases::TVORIT => $prefix.'ьюдесятью',
                    RussianCases::PREDLOJ => $prefix.'идесяти',
                ];
            } elseif (in_array($number, [300, 400])) {
                $prefix = MultibyteString::slice($word, 0, -4);
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'ехсот',
                    RussianCases::DAT => $prefix.'емстам',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => $prefix.($number == 300 ? 'е' : 'ь').'мястами',
                    RussianCases::PREDLOJ => $prefix.'ехстах',
                ];
            } elseif ($number >= 500 && $number <= 900) {
                $prefix = MultibyteString::slice($word, 0, -4);
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'исот',
                    RussianCases::DAT => $prefix.'истам',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => $prefix.'ьюстами',
                    RussianCases::PREDLOJ => $prefix.'истах',
                ];
            } elseif (isset(static::$exponents[$number])) {
                return NounDeclension::getCases($word, false);
            }

            throw new RuntimeException('Unreachable');
        }

        if ($number == 0) {
            return [
                RussianCases::IMENIT => 'ноль',
                RussianCases::RODIT => 'ноля',
                RussianCases::DAT => 'нолю',
                RussianCases::VINIT => 'ноль',
                RussianCases::TVORIT => 'нолём',
                RussianCases::PREDLOJ => 'ноле',
            ];
        } // compound numeral

        $parts = [];
        $result = [];

        foreach (array_reverse(static::$exponents, true) as $word_number => $word) {
            if ($number >= $word_number) {
                $count = (int)floor($number / $word_number);
                $parts[] = static::getCases($count, ($word_number == 1000 ? static::FEMALE : static::MALE));

                switch (NounPluralization::getNumeralForm($count)) {
                    case NounPluralization::ONE:
                        $parts[] = NounDeclension::getCases($word, false);
                        break;

                    case NounPluralization::TWO_FOUR:
                        $part = NounPluralization::getCases($word);
                        if ($word_number != 1000) { // get dative case of word for 1000000, 1000000000 and 1000000000000
                            $part[RussianCases::IMENIT] = $part[RussianCases::VINIT] = NounDeclension::getCase($word, RussianCases::RODIT);
                        }
                        $parts[] = $part;
                        break;

                    case NounPluralization::FIVE_OTHER:
                        $part = NounPluralization::getCases($word);
                        $part[RussianCases::IMENIT] = $part[RussianCases::VINIT] = $part[RussianCases::RODIT];
                        $parts[] = $part;
                        break;
                }

                $number = $number % ($count * $word_number);
            }
        }

        foreach (array_reverse(static::$words, true) as $word_number => $word) {
            if ($number >= $word_number) {
                $parts[] = static::getCases($word_number, $gender);
                $number %= $word_number;
            }
        }

        // make one array with cases and delete 'o/об' prepositional from all parts except the last one
        foreach (array(RussianCases::IMENIT, RussianCases::RODIT, RussianCases::DAT, RussianCases::VINIT, RussianCases::TVORIT, RussianCases::PREDLOJ) as $case) {
            $result[$case] = [];
            foreach ($parts as $partN => $part) {
                $result[$case][] = $part[$case];
            }
            $result[$case] = implode(' ', $result[$case]);
        }

        return $result;
    }

    /**
     * @param int $number
     * @param string $case
     * @param string $gender
     *
     * @return string
     * @throws \Exception
     */
    public static function getCase($number, $case, $gender = Gender::MALE)
    {
        $case = static::canonizeCase($case);
        $forms = static::getCases($number, $gender);
        return $forms[$case];
    }
}