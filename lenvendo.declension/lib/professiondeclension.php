<?php

namespace Lenvendo\Declension;

use \Lenvendo\Declension\Interfaces\Cases;
use \Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;

class ProfessionDeclension implements Cases
{
    use RussianLanguage, CasesHelper;

    /**
     * @var string[]
     * слова которые не нужно склонять, например офис-менеджер, Арт-директор, Тренинг-менеджер
     */
    protected static array $excludedWords = [
        'офис', 'контент', 'продакт', 'бренд', 'клиент', 'пресс', 'арт', 'тренинг', 'строений', 'сооружений', 'кризис',
        'поручений', 'офис', 'интернет', 'бизнес', 'аккаунт', 'решений', 'фитнесс', 'дата', 'блокчейн', 'шев', 'ивент',
        'SMM', 'гейм', 'саунд', 'онлайн', 'тайм'
    ];

    public static function getExcludedWord() {
        return self::$excludedWords;
    }

    public static function getRole($arrRole, $case): string
    {
        $result = '';

        foreach ($arrRole as $role) {
            // Случай если в должности есть разделитель
            if (strpos($role, '-') != false) {

                $withDefis = '';
                foreach (explode('-', $role) as $part) {

                    if (!in_array(MultibyteString::lower($part), self::$excludedWords))
                        $withDefis .= self::getCases($part, $case) . '-';
                    else
                        $withDefis .= $part . '-';
                }
                $result .= mb_substr($withDefis, 0, -1) . ' ';
            } else {
                $result .= self::getCases($role, $case) . ' ';
            }
        }
        return trim($result);
    }

    protected function getCases($word, $case) {

        $char = mb_substr($word, mb_strlen($word)-1, 1);

        switch ($char) {
            case 'й':
                if (mb_substr($word, mb_strlen($word)-2, 2) == 'ой' && $word != 'лицевой') {
                    $resultWord = $word;
                    break;
                }
                if (mb_strlen($word) > 3 && !in_array(MultibyteString::lower($word), self::$excludedWords)) {
                    $resultWord = self::declensionAjective($word)[$case];
                }
                else {
                    $resultWord = $word;
                }
                break;
            case 'ь':
                $resultWord = self::softEnding($word)[$case];
                break;
            case in_array($char, ['р', 'т', 'к', 'г', 'ф', 'д', 'з', 'н']):
                $resultWord = self::consonantEnding($word)[$case];
                break;
            case 'ц':
                if (mb_substr($word, mb_strlen($word)-3, 3))
                    $resultWord = self::softEnding($word)[$case];
                break;
            default:
                $resultWord = $word;
                break;
        }

        return $resultWord;
    }

    protected function declensionAjective($word) {

        $ending = mb_substr($word, mb_strlen($word)-3, 3);
        $prefix = mb_substr($word, 0, -2);

        if (in_array($ending, ['щий', 'ший', 'чий'])) {
            return [
                Cases::NOMINATIVE => $prefix.'ий',
                Cases::GENITIVE => $prefix.'его',
                Cases::DATIVE => $prefix.'ему',
                Cases::ACCUSATIVE => $prefix.'ий',
                Cases::ABLATIVE => $prefix.'им',
                Cases::PREPOSITIONAL => $prefix.'ем',
            ];
        } else {
            return [
                Cases::NOMINATIVE => $prefix.'ый',
                Cases::GENITIVE => $prefix.'oго',
                Cases::DATIVE => $prefix.'ому',
                Cases::ACCUSATIVE => $prefix.'ый',
                Cases::ABLATIVE => $prefix.'ым',
                Cases::PREPOSITIONAL => $prefix.'ом',
            ];
        }
    }

    protected function softEnding($word){
        $prefix = mb_substr($word, 0, -1);

        return [
            Cases::NOMINATIVE => $prefix.'ь',
            Cases::GENITIVE => $prefix.'я',
            Cases::DATIVE => $prefix.'ю',
            Cases::ACCUSATIVE => $prefix.'я',
            Cases::ABLATIVE => $prefix.'ем',
            Cases::PREPOSITIONAL => $prefix.'е',
        ];
    }

    protected function consonantEnding($word) {

        return [
            Cases::NOMINATIVE => $word,
            Cases::GENITIVE => $word.'а',
            Cases::DATIVE => $word.'у',
            Cases::ACCUSATIVE => $word.'а',
            Cases::ABLATIVE => $word.'ом',
            Cases::PREPOSITIONAL => $word.'е',
        ];
    }
}