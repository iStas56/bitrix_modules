<?php

namespace Lenvendo\Declension;

use \Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Abstracts\BaseInflection;
use Lenvendo\Declension\Interfaces\RussianCases;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;
use RuntimeException;

class NounDeclension extends BaseInflection implements Cases, Gender
{
    use RussianLanguage, CasesHelper;

    const FIRST_DECLENSION = 1;
    const SECOND_DECLENSION = 2;
    const THIRD_DECLENSION = 3;

    /** @var string[] */
    public static $immutableWords = [
        // валюты
        'евро', 'пенни', 'песо', 'сентаво',

        // на а
        'боа', 'бра', 'фейхоа', 'амплуа', 'буржуа',
        // на о
        'манго', 'какао', 'кино', 'трюмо', 'пальто', 'бюро', 'танго', 'вето', 'бунгало', 'сабо', 'авокадо', 'депо', 'панно',
        // на у
        'зебу', 'кенгуру', 'рагу', 'какаду', 'шоу',
        // на е
        'шимпанзе', 'конферансье', 'атташе', 'колье', 'резюме', 'пенсне', 'кашне', 'протеже', 'коммюнике', 'драже', 'суфле', 'пюре', 'купе', 'фойе', 'шоссе', 'крупье',
        // на и
        'такси', 'жалюзи', 'шасси', 'алиби', 'киви', 'иваси', 'регби', 'конфетти', 'колибри', 'жюри', 'пенальти', 'рефери', 'кольраби',
        // на э
        'каноэ', 'алоэ',
        // на ю
        'меню', 'парвеню', 'авеню', 'дежавю', 'инженю', 'барбекю', 'интервью',
    ];

    /**
     * These words has 2 declension type.
     * @var string[]|string[][]
     */
    protected static $abnormalExceptions = [
        'бремя',
        'вымя',
        'темя',
        'пламя',
        'стремя',
        'пламя',
        'время',
        'знамя',
        'имя',
        'племя',
        'семя',
        'путь' => ['путь', 'пути', 'пути', 'путь', 'путем', 'пути'],
        'дитя' => ['дитя', 'дитяти', 'дитяти', 'дитя', 'дитятей', 'дитяти'],
    ];

    /** @var string[]  */
    protected static $masculineWithSoft = [
        'автослесарь',
        'библиотекарь',
        'водитель',
        'воспитатель',
        'врач',
        'выхухоль',
        'гвоздь',
        'делопроизводитель',
        'день',
        'дождь',
        'заместитель',
        'зверь',
        'камень',
        'конь',
        'конь',
        'корень',
        'лось',
        'медведь',
        'модуль',
        'олень',
        'парень',
        'пекарь',
        'пельмень',
        'пень',
        'председатель',
        'представитель',
        'преподаватель',
        'продавец',
        'производитель',
        'путь',
        'рояль',
        'рубль',
        'руководитель',
        'секретарь',
        'слесарь',
        'строитель',
        'табель',
        'токарь',
        'трутень',
        'тюлень',
        'учитель',
        'циркуль',
        'шампунь',
        'шкворень',
        'юань',
        'ячмень',
    ];

    /** @var string[] */
    public static $runawayVowelsExceptions = [
        'глото*к',
        'де*нь',
        'каме*нь',
        'коре*нь',
        'паре*нь',
        'пе*нь',
        'песе*ц',
        'писе*ц',
        'санузе*л',
        'труте*нь',
    ];

    /**
     * Проверка, изменяемое ли слово.
     * @param string $word Слово для проверки
     * @param bool $animateness Признак одушевленности
     * @return bool
     */
    public static function isMutable($word, $animateness = false)
    {
        $word = MultibyteString::lower($word);
        if (in_array(MultibyteString::slice($word, -1), ['у', 'и', 'е', 'о', 'ю'], true) || in_array($word, static::$immutableWords, true)) {
            return false;
        }
        return true;
    }

    /**
     * Определение рода существительного.
     * @param string $word
     * @return string
     */
    public static function detectGender($word)
    {
        $word = MultibyteString::lower($word);
        $last = MultibyteString::slice($word, -1);
        // пытаемся угадать род объекта, хотя бы примерно, чтобы правильно склонять
        if (MultibyteString::slice($word, -2) == 'мя' || in_array($last, ['о', 'е', 'и', 'у'], true))
            return static::NEUTER;

        if (in_array($last, ['а', 'я'], true) ||
            ($last == 'ь' && !in_array($word, static::$masculineWithSoft, true)))
            return static::FEMALE;

        return static::MALE;
    }

    /**
     * Определение склонения (по школьной программе) существительного.
     * @param string $word
     * @param bool $animateness
     * @return int
     */
    public static function getDeclension($word, $animateness = false)
    {
        $word = MultibyteString::lower($word);
        $last = MultibyteString::slice($word, -1);
        if (isset(static::$abnormalExceptions[$word]) || in_array($word, static::$abnormalExceptions, true)) {
            return 2;
        }

        if (in_array($last, ['а', 'я'], true) && MultibyteString::slice($word, -2) != 'мя') {
            return 1;
        } elseif (static::isConsonant($last) || in_array($last, ['о', 'е', 'ё'], true)
            || ($last == 'ь' && static::isConsonant(MultibyteString::slice($word, -2, -1)) && !static::isHissingConsonant(MultibyteString::slice($word, -2, -1))
                && (in_array($word, static::$masculineWithSoft, true)) /*|| in_array($word, static::$masculineWithSoftAndRunAwayVowels, true)*/)) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * Получение слова во всех 6 падежах.
     * @param string $word
     * @param bool $animateness Признак одушевлённости
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($word, $animateness = false)
    {
        $word = MultibyteString::lower($word);

        // Адъективное склонение (Сущ, образованные от прилагательных и причастий) - прохожий, существительное
        if (static::isAdjectiveNoun($word)) {
            return static::declinateAdjective($word, $animateness);
        }

        // Субстантивное склонение (существительные)
        if (in_array($word, static::$immutableWords, true)) {
            return [
                RussianCases::IMENIT => $word,
                RussianCases::RODIT => $word,
                RussianCases::DAT => $word,
                RussianCases::VINIT => $word,
                RussianCases::TVORIT => $word,
                RussianCases::PREDLOJ => $word,
            ];
        }

        if (isset(static::$abnormalExceptions[$word])) {
            return array_combine([RussianCases::IMENIT, RussianCases::RODIT, RussianCases::DAT, RussianCases::VINIT, RussianCases::TVORIT, RussianCases::PREDLOJ], static::$abnormalExceptions[$word]);
        }

        if (in_array($word, static::$abnormalExceptions, true)) {
            $prefix = MultibyteString::slice($word, 0, -1);
            return [
                RussianCases::IMENIT => $word,
                RussianCases::RODIT => $prefix.'ени',
                RussianCases::DAT => $prefix.'ени',
                RussianCases::VINIT => $word,
                RussianCases::TVORIT => $prefix.'енем',
                RussianCases::PREDLOJ => $prefix.'ени',
            ];
        }

        switch (static::getDeclension($word)) {
            case static::FIRST_DECLENSION:
                return static::declinateFirstDeclension($word);
            case static::SECOND_DECLENSION:
                return static::declinateSecondDeclension($word, $animateness);
            case static::THIRD_DECLENSION:
                return static::declinateThirdDeclension($word);

            default: throw new RuntimeException('Unreachable');
        }
    }

    /**
     * Получение всех форм слова первого склонения.
     * @param string $word
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function declinateFirstDeclension($word)
    {
        $word = MultibyteString::lower($word);
        $prefix = MultibyteString::slice($word, 0, -1);
        $last = MultibyteString::slice($word, -1);
        $soft_last = static::checkLastConsonantSoftness($word);
        $forms =  [
            RussianCases::IMENIT => $word,
        ];

        // RODIT
        $forms[RussianCases::RODIT] = static::chooseVowelAfterConsonant($last, $soft_last || (in_array(MultibyteString::slice($word, -2, -1), ['г', 'к', 'х'], true)), $prefix.'и', $prefix.'ы');

        // DAT
        $forms[RussianCases::DAT] = static::getPredCaseOf12Declensions($word, $last, $prefix);

        // VINIT
        $forms[RussianCases::VINIT] = static::chooseVowelAfterConsonant($last, $soft_last && MultibyteString::slice($word, -2, -1) !== 'ч', $prefix.'ю', $prefix.'у');

        // TVORIT
        if ($last === 'ь') {
            $forms[RussianCases::TVORIT] = $prefix.'ой';
        } else {
            $forms[RussianCases::TVORIT] = static::chooseVowelAfterConsonant($last, $soft_last, $prefix.'ей', $prefix.'ой');
        }

        // 	if ($last == 'й' || (static::isConsonant($last) && !static::isHissingConsonant($last)) || static::checkLastConsonantSoftness($word))
        // 	$forms[RussianCases::TVORIT] = $prefix.'ей';
        // else
        // 	$forms[RussianCases::TVORIT] = $prefix.'ой'; # http://morpher.ru/Russian/Spelling.aspx#sibilant

        // PREDLOJ the same as DAT
        $forms[RussianCases::PREDLOJ] = $forms[RussianCases::DAT];
        return $forms;
    }

    /**
     * Получение всех форм слова второго склонения.
     * @param string $word
     * @param bool $animateness
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function declinateSecondDeclension($word, $animateness = false)
    {
        $word = MultibyteString::lower($word);
        $last = MultibyteString::slice($word, -1);
        $soft_last = $last === 'й'
            || (
                in_array($last, ['ь', 'е', 'ё', 'ю', 'я'], true)
                && (
                    (
                        static::isConsonant(MultibyteString::slice($word, -2, -1))
                        && !static::isHissingConsonant(MultibyteString::slice($word, -2, -1))
                    )
                    || MultibyteString::slice($word, -2, -1) === 'и')
            );
        $prefix = static::getPrefixOfSecondDeclension($word, $last);
        $forms =  [
            RussianCases::IMENIT => $word,
        ];

        // RODIT
        $forms[RussianCases::RODIT] = static::chooseVowelAfterConsonant($last, $soft_last, $prefix.'я', $prefix.'а');

        // DAT
        $forms[RussianCases::DAT] = static::chooseVowelAfterConsonant($last, $soft_last, $prefix.'ю', $prefix.'у');

        // VINIT
        if (in_array($last, ['о', 'е', 'ё'], true)) {
            $forms[RussianCases::VINIT] = $word;
        } else {
            $forms[RussianCases::VINIT] = static::getVinitCaseByAnimateness($forms, $animateness);
        }

        // TVORIT
        // if ($last == 'ь')
        // 	$forms[RussianCases::TVORIT] = $prefix.'ом';
        // else if ($last == 'й' || (static::isConsonant($last) && !static::isHissingConsonant($last)))
        // 	$forms[RussianCases::TVORIT] = $prefix.'ем';
        // else
        // 	$forms[RussianCases::TVORIT] = $prefix.'ом'; # http://morpher.ru/Russian/Spelling.aspx#sibilant
        if ((static::isHissingConsonant($last) && $last !== 'ш')
            || (in_array($last, ['ь', 'е', 'ё', 'ю', 'я'], true) && static::isHissingConsonant(MultibyteString::slice($word, -2, -1)))
            || ($last === 'ц' && MultibyteString::slice($word, -2) !== 'ец')) {
            $forms[RussianCases::TVORIT] = $prefix.'ем';
        } elseif (in_array($last, ['й'/*, 'ч', 'щ'*/], true) || $soft_last) {
            $forms[RussianCases::TVORIT] = $prefix.'ем';
        } else {
            $forms[RussianCases::TVORIT] = $prefix.'ом';
        }

        // PREDLOJ
        $forms[RussianCases::PREDLOJ] = static::getPredCaseOf12Declensions($word, $last, $prefix);

        return $forms;
    }

    /**
     * Получение всех форм слова третьего склонения.
     * @param string $word
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function declinateThirdDeclension($word)
    {
        $word = MultibyteString::lower($word);
        $prefix = MultibyteString::slice($word, 0, -1);
        return [
            RussianCases::IMENIT => $word,
            RussianCases::RODIT => $prefix.'и',
            RussianCases::DAT => $prefix.'и',
            RussianCases::VINIT => $word,
            RussianCases::TVORIT => $prefix.'ью',
            RussianCases::PREDLOJ => $prefix.'и',
        ];
    }

    /**
     * Склонение существительных, образованных от прилагательных и причастий.
     * Rules are from http://rusgram.narod.ru/1216-1231.html
     * @param string $word
     * @param bool $animateness
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function declinateAdjective($word, $animateness)
    {
        $prefix = MultibyteString::slice($word, 0, -2);

        switch (MultibyteString::slice($word, -2)) {
            // Male adjectives
            case 'ой':
            case 'ый':
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'ого',
                    RussianCases::DAT => $prefix.'ому',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => $prefix.'ым',
                    RussianCases::PREDLOJ => $prefix.'ом',
                ];

            case 'ий':
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'его',
                    RussianCases::DAT => $prefix.'ему',
                    RussianCases::VINIT => $prefix.'его',
                    RussianCases::TVORIT => $prefix.'им',
                    RussianCases::PREDLOJ => $prefix.'ем',
                ];

            // Neuter adjectives
            case 'ое':
            case 'ее':
                $prefix = MultibyteString::slice($word, 0, -1);
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.'го',
                    RussianCases::DAT => $prefix.'му',
                    RussianCases::VINIT => $word,
                    RussianCases::TVORIT => MultibyteString::slice($word, 0, -2).(MultibyteString::slice($word, -2, -1) == 'о' ? 'ы' : 'и').'м',
                    RussianCases::PREDLOJ => $prefix.'м',
                ];

            // Female adjectives
            case 'ая':
                $ending = static::isHissingConsonant(MultibyteString::slice($prefix, -1)) ? 'ей' : 'ой';
                return [
                    RussianCases::IMENIT => $word,
                    RussianCases::RODIT => $prefix.$ending,
                    RussianCases::DAT => $prefix.$ending,
                    RussianCases::VINIT => $prefix.'ую',
                    RussianCases::TVORIT => $prefix.$ending,
                    RussianCases::PREDLOJ => $prefix.$ending,
                ];

            default: throw new RuntimeException('Unreachable');
        }
    }

    /**
     * Получение одной формы слова (падежа).
     * @param string $word Слово
     * @param string $case Падеж
     * @param bool $animateness Признак одушевленности
     * @return string
     * @throws \Exception
     */
    public static function getCase($word, $case, $animateness = false)
    {
        $case = static::canonizeCase($case);
        $forms = static::getCases($word, $animateness);
        return $forms[$case];
    }

    /**
     * @param string $word
     * @param string $last
     * @return string
     */
    public static function getPrefixOfSecondDeclension($word, $last)
    {
        // слова с бегающей гласной в корне
        $runaway_vowels_list = static::getRunAwayVowelsList();
        if (isset($runaway_vowels_list[$word])) {
            $vowel_offset = $runaway_vowels_list[$word];
            $word = MultibyteString::slice($word, 0, $vowel_offset) . MultibyteString::slice($word, $vowel_offset + 1);
        }

        if (in_array($last, ['о', 'е', 'ё', 'ь', 'й'], true)) {
            $prefix = MultibyteString::slice($word, 0, -1);
        }
        // уменьшительные формы слов (котенок) и слова с суффиксом ок
        elseif (MultibyteString::slice($word, -2) === 'ок' && MultibyteString::length($word) > 3) {
            $prefix = MultibyteString::slice($word, 0, -2) . 'к';
        }
        // слова с суффиксом бец
        elseif (MultibyteString::slice($word, -3) === 'бец' && MultibyteString::length($word) > 4) {
            $prefix = MultibyteString::slice($word, 0, -3).'бц';
        } else {
            $prefix = $word;
        }
        return $prefix;
    }

    /**
     * @param string $word
     * @param string $last
     * @param string $prefix
     * @return string
     */
    public static function getPredCaseOf12Declensions($word, $last, $prefix)
    {
        if (in_array(MultibyteString::slice($word, -2), ['ий', 'ие'], true)) {
            if ($last == 'ё') {
                return $prefix.'е';
            } else {
                return $prefix.'и';
            }
        } else {
            return $prefix.'е';
        }
    }

    /**
     * @return int[]|false[]
     */
    public static function getRunAwayVowelsList()
    {
        $runawayVowelsNormalized = [];
        foreach (NounDeclension::$runawayVowelsExceptions as $word) {
            $runawayVowelsNormalized[str_replace('*', '', $word)] = MultibyteString::indexOf($word, '*') - 1;
        }
        return $runawayVowelsNormalized;
    }
}