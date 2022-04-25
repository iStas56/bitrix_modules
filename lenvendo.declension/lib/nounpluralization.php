<?php

namespace Lenvendo\Declension;

use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Abstracts\BasePluralization;
use Lenvendo\Declension\Interfaces\RussianCases;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;

class NounPluralization extends BasePluralization implements Cases
{
    use RussianLanguage, CasesHelper;

    const ONE = 1;
    const TWO_FOUR = 2;
    const FIVE_OTHER = 3;

    /**
     * @var string[][]
     * @phpstan-var array<string, string[]>
     */
    protected static $abnormalExceptions = [
        'человек' => ['люди', 'человек', 'людям', 'людей', 'людьми', 'людях'],
    ];

    /** @var string[] */
    protected static $neuterExceptions = [
        'поле',
        'море',
    ];

    /**
     * @var string[]
     * @phpstan-var array<string, string>
     */
    protected static $genitiveExceptions = [
        'письмо' => 'писем',
        'пятно' => 'пятен',
        'кресло' => 'кресел',
        'коромысло' => 'коромысел',
        'ядро' => 'ядер',
        'блюдце' => 'блюдец',
        'полотенце' => 'полотенец',
        'гривна' => 'гривен',
        'год' => 'лет',
    ];

    /**
     * Склонение существительного для сочетания с числом (кол-вом предметов).
     *
     * @param string|int $word        Название предмета
     * @param int|float|string $count Количество предметов
     * @param bool       $animateness Признак одушевленности
     * @param string     $case        Род существительного
     *
     * @return string
     * @throws \Exception
     */
    public static function pluralize($word, $count = 2, $animateness = false, $case = null)
    {
        // меняем местами аргументы, если они переданы в старом формате
        if (is_string($count) && is_numeric($word)) {
            list($count, $word) = [$word, $count];
        }

        if ($case !== null)
            $case = static::canonizeCase($case);

        // для адъективных существительных правила склонения проще:
        // только две формы
        if (static::isAdjectiveNoun($word)) {
            if (static::getNumeralForm($count) == static::ONE)
                return $word;
            else
                return NounPluralization::getCase($word,
                    $case !== null
                        ? $case
                        : RussianCases::RODIT, $animateness);
        }

        if ($case === null) {
            switch (static::getNumeralForm($count)) {
                case static::ONE:
                    return $word;
                case static::TWO_FOUR:
                    return NounDeclension::getCase($word, RussianCases::RODIT, $animateness);
                case static::FIVE_OTHER:
                    return NounPluralization::getCase($word, RussianCases::RODIT, $animateness);
            }
        }

        if (static::getNumeralForm($count) == static::ONE)
            return NounDeclension::getCase($word, $case, $animateness);
        else
            return NounPluralization::getCase($word, $case, $animateness);
    }

    /**
     * @param int|float $count
     * @return int
     */
    public static function getNumeralForm($count)
    {
        if ($count > 100) {
            $count %= 100;
        }
        $ending = $count % 10;

        if (($count > 20 && $ending == 1) || $count == 1) {
            return static::ONE;
        } elseif (($count > 20 && in_array($ending, range(2, 4))) || in_array($count, range(2, 4))) {
            return static::TWO_FOUR;
        } else {
            return static::FIVE_OTHER;
        }
    }

    /**
     * @param string $word
     * @param string $case
     * @param bool $animateness
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
     * @param bool $animateness
     * @return string[]
     * @phpstan-return array<string, string>
     */
    public static function getCases($word, $animateness = false)
    {
        $word = MultibyteString::lower($word);

        if (in_array($word, NounDeclension::$immutableWords, true)) {
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
            return array_combine(
                [RussianCases::IMENIT, RussianCases::RODIT, RussianCases::DAT, RussianCases::VINIT, RussianCases::TVORIT, RussianCases::PREDLOJ],
                static::$abnormalExceptions[$word]);
        }

        // Адъективное склонение (Сущ, образованные от прилагательных и причастий)
        // Пример: прохожий, существительное
        if (static::isAdjectiveNoun($word)) {
            return static::declinateAdjective($word, $animateness);
        }

        // Субстантивное склонение (существительные)
        return static::declinateSubstative($word, $animateness);
    }

    /**
     * Склонение обычных существительных.
     * @param string $word
     * @param bool $animateness
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected static function declinateSubstative($word, $animateness)
    {
        $prefix = MultibyteString::slice($word, 0, -1);
        $last = MultibyteString::slice($word, -1);

        if (($declension = NounDeclension::getDeclension($word)) == NounDeclension::SECOND_DECLENSION) {
            $soft_last = $last == 'й' || (in_array($last, ['ь', 'е', 'ё', 'ю', 'я'], true)
                    && ((
                            static::isConsonant(MultibyteString::slice($word, -2, -1)) && !static::isHissingConsonant(MultibyteString::slice($word, -2, -1)))
                        || MultibyteString::slice($word, -2, -1) == 'и'));
            $prefix = NounDeclension::getPrefixOfSecondDeclension($word, $last);
        } elseif ($declension == NounDeclension::FIRST_DECLENSION) {
            $soft_last = static::checkLastConsonantSoftness($word);
        } else {
            $soft_last = in_array(MultibyteString::slice($word, -2), ['чь', 'сь', 'ть', 'нь', 'дь'], true);
        }

        $forms = [];

        if (in_array($last, ['ч', 'г'], true)
            || in_array(MultibyteString::slice($word, -2), ['чь', 'сь', 'ть', 'нь', 'рь', 'дь'], true)
            || (static::isVowel($last) && in_array(MultibyteString::slice($word, -2, -1), ['ч', 'к'], true))) { // before ч, чь, сь, ч+vowel, к+vowel
            $forms[RussianCases::IMENIT] = $prefix.'и';
        } elseif (in_array($last, ['н', 'ц', 'р', 'т'], true)) {
            $forms[RussianCases::IMENIT] = $prefix.'ы';
        } else {
            $forms[RussianCases::IMENIT] = static::chooseVowelAfterConsonant($last, $soft_last, $prefix.'я', $prefix.'а');
        }

        // RODIT
        if (isset(static::$genitiveExceptions[$word])) {
            $forms[RussianCases::RODIT] = static::$genitiveExceptions[$word];
        } elseif (in_array($last, ['о', 'е'], true)) {
            // exceptions
            if (in_array($word, static::$neuterExceptions, true)) {
                $forms[RussianCases::RODIT] = $prefix.'ей';
            } elseif (MultibyteString::slice($word, -2, -1) == 'и') {
                $forms[RussianCases::RODIT] = $prefix.'й';
            } else {
                $forms[RussianCases::RODIT] = $prefix;
            }
        } elseif (MultibyteString::slice($word, -2) == 'ка' && MultibyteString::slice($word, -3, -2) !== 'и') { // words ending with -ка: чашка, вилка, ложка, тарелка, копейка, батарейка, аптека
            if (MultibyteString::slice($word, -3, -2) == 'л') {
                $forms[RussianCases::RODIT] = MultibyteString::slice($word, 0, -2).'ок';
            } elseif (in_array(MultibyteString::slice($word, -3, -2), ['й', 'е'], true)) {
                $forms[RussianCases::RODIT] = MultibyteString::slice($word, 0, -3).'ек';
            } else {
                $forms[RussianCases::RODIT] = MultibyteString::slice($word, 0, -2).'ек';
            }
        } elseif (in_array($last, ['а'], true)) { // обида, ябеда
            $forms[RussianCases::RODIT] = $prefix;
        } elseif (in_array($last, ['я'], true)) { // молния
            $forms[RussianCases::RODIT] = $prefix.'й';
        } elseif (RussianLanguage::isHissingConsonant($last) || ($soft_last && $last != 'й') || in_array(MultibyteString::slice($word, -2), ['чь', 'сь', 'ть', 'нь', 'дь'], true)) {
            $forms[RussianCases::RODIT] = $prefix.'ей';
        } elseif ($last == 'й' || MultibyteString::slice($word, -2) == 'яц') { // месяц
            $forms[RussianCases::RODIT] = $prefix.'ев';
        } else { // (static::isConsonant($last) && !RussianLanguage::isHissingConsonant($last))
            $forms[RussianCases::RODIT] = $prefix.'ов';
        }

        // DAT
        $forms[RussianCases::DAT] = static::chooseVowelAfterConsonant($last, $soft_last && MultibyteString::slice($word, -2, -1) != 'ч', $prefix.'ям', $prefix.'ам');

        // VINIT
        $forms[RussianCases::VINIT] = NounDeclension::getVinitCaseByAnimateness($forms, $animateness);

        // TVORIT
        // my personal rule
        if ($last == 'ь' && $declension == NounDeclension::THIRD_DECLENSION && !in_array(MultibyteString::slice($word, -2), ['чь', 'сь', 'ть', 'нь', 'дь'], true)) {
            $forms[RussianCases::TVORIT] = $prefix.'ми';
        } else {
            $forms[RussianCases::TVORIT] = static::chooseVowelAfterConsonant($last, $soft_last && MultibyteString::slice($word, -2, -1) != 'ч', $prefix.'ями', $prefix.'ами');
        }

        // PREDLOJ
        $forms[RussianCases::PREDLOJ] = static::chooseVowelAfterConsonant($last, $soft_last && MultibyteString::slice($word, -2, -1) != 'ч', $prefix.'ях', $prefix.'ах');
        return $forms;
    }

    /**
     * Склонение существительных, образованных от прилагательных и причастий.
     * Rules are from http://rusgram.narod.ru/1216-1231.html
     * @param string $word
     * @param bool $animateness
     * @return string[]
     * @phpstan-return array<string, string>
     */
    protected static function declinateAdjective($word, $animateness)
    {
        $prefix = MultibyteString::slice($word, 0, -2);
        $vowel = static::isHissingConsonant(MultibyteString::slice($prefix, -1)) ? 'и' : 'ы';
        return [
            RussianCases::IMENIT => $prefix.$vowel.'е',
            RussianCases::RODIT => $prefix.$vowel.'х',
            RussianCases::DAT => $prefix.$vowel.'м',
            RussianCases::VINIT => $prefix.$vowel.($animateness ? 'х' : 'е'),
            RussianCases::TVORIT => $prefix.$vowel.'ми',
            RussianCases::PREDLOJ => $prefix.$vowel.'х',
        ];
    }
}