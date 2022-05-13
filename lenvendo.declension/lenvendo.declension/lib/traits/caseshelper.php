<?php
namespace Lenvendo\Declension\Traits;

use InvalidArgumentException;
use \Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\MultibyteString;

trait CasesHelper
{
    /**
     * @param string $case
     * @return string
     * @throws InvalidArgumentException If passed case is invalid.
     */
    public static function canonizeCase($case)
    {
        $case = MultibyteString::lower($case);
        switch ($case) {
            case Cases::NOMINATIVE:
            case 'nominativus':
            case 'n':
                return Cases::NOMINATIVE;

            case Cases::GENITIVE:
            case 'genetivus':
            case 'g':
                return Cases::GENITIVE;

            case Cases::DATIVE:
            case 'dativus':
            case 'd':
                return Cases::DATIVE;

            case Cases::ACCUSATIVE:
                return Cases::ACCUSATIVE;

            case Cases::ABLATIVE:
            case 'ablativus':
            case 'a':
                return Cases::ABLATIVE;

            case Cases::PREPOSITIONAL:
            case 'praepositionalis':
            case 'p':
                return Cases::PREPOSITIONAL;

            default:
                throw new InvalidArgumentException('Invalid case: '.$case);
        }
    }

    /**
     * @return string[]
     * @phpstan-return array<Cases::*>
     */
    public static function getAllCases()
    {
        return [
            Cases::NOMINATIVE,
            Cases::GENITIVE,
            Cases::DATIVE,
            Cases::ACCUSATIVE,
            Cases::ABLATIVE,
            Cases::PREPOSITIONAL,
        ];
    }

    /**
     * Составляет один массив с падежами из нескольких массивов падежей разных слов
     * @param array $words Двумерный массив слов и их падежей
     * @phpstan-param array<int, array<string, string>> $words
     * @param string $delimiter Разделитель между падежами слов
     * @return string[] Одномерный массив падежей
     * @phpstan-return array<string, string>
     */
    public static function composeCasesFromWords(array $words, $delimiter = ' ') {
        $cases = [];
        $all_cases = CasesHelper::getAllCases();
        if (count($words[0]) === 7)
            $all_cases[] = \Lenvendo\Declension\Interfaces\RussianCases::LOCATIVE;

        foreach ($all_cases as $case) {
            $composed_case = [];
            foreach ($words as $wordCases) {
                $composed_case[] = $wordCases[$case];
            }
            $cases[$case] = implode($delimiter, $composed_case);
        }
        return $cases;
    }
}
