<?php

namespace Lenvendo\Declension;

use Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;


class Declension implements Gender
{

    public static function inflectName($fullName, $case = null, $gender = null)
    {
        if (in_array($case, [self::MALE, self::FEMALE, null], true)) {
            return $case === null ? self::getNameCases($fullName) : self::getNameCases($fullName, $case);
        }

        $fullName = self::normalizeFullName($fullName);
        $case = CasesHelper::canonizeCase($case);
        if ($gender === null) $gender = self::detectGender($fullName);

        $name = explode(' ', $fullName);

        switch (count($name)) {
            case 1:
                $name[0] = FirstNamesInflection::getCase($name[0], $case, $gender);
                break;

            case 2:
                $name[0] = LastNamesInflection::getCase($name[0], $case, $gender);
                $name[1] = FirstNamesInflection::getCase($name[1], $case, $gender);
                break;

            case 3:
                $name[0] = LastNamesInflection::getCase($name[0], $case, $gender);
                $name[1] = FirstNamesInflection::getCase($name[1], $case, $gender);
                $name[2] = MiddleNamesInflection::getCase($name[2], $case, $gender);
                break;

            default:
                return false;
        }

        return implode(' ', $name);
    }

    public function getNameCases($fullName, $gender = null)
    {
        $fullName = self::normalizeFullName($fullName);
        if ($gender === null) $gender = self::detectGender($fullName);

        $name = explode(' ', $fullName);

        switch (count($name)) {
            case 1:
                $name[0] = FirstNamesInflection::getCases($name[0], $gender);
                break;

            case 2:
                $name[0] = LastNamesInflection::getCases($name[0], $gender);
                $name[1] = FirstNamesInflection::getCases($name[1], $gender);
                break;

            case 3:
                $name[0] = LastNamesInflection::getCases($name[0], $gender);
                $name[1] = FirstNamesInflection::getCases($name[1], $gender);
                $name[2] = MiddleNamesInflection::getCases($name[2], $gender);
                break;

            default:
                return false;
        }

        return CasesHelper::composeCasesFromWords($name);
    }

    public function normalizeFullName($name)
    {
        $name = preg_replace('~[ ]{2,}~', '', trim($name));
        return $name;
    }

    public static function detectGender($fullName)
    {
        $gender = null;
        $name = explode(' ', MultibyteString::lower($fullName));
        $nameCount = count($name);
        if ($nameCount > 3) {
            return null;
        }

        if ($nameCount === 1)
            return FirstNamesInflection::detectGender($name[0]);
        else if ($nameCount === 2)
            return LastNamesInflection::detectGender($name[0])
                ?: FirstNamesInflection::detectGender($name[1]);
        else
            return MiddleNamesInflection::detectGender($name[2])
                ?: (LastNamesInflection::detectGender($name[0])
                    ?: FirstNamesInflection::detectGender($name[1]));
    }

    public static function pluralize($count, $word, $animateness = false, $case = null)
    {
        // меняем местами аргументы, если они переданы в старом формате
        // @phpstan-ignore-next-line

        if (is_string($count) && is_numeric($word)) {
            list($count, $word) = [$word, $count];
        }

        if (strpos($word, ' ') != false) {
            return '1';
            $words = explode(' ', $word);
            $noun = array_pop($words);

            foreach ($words as $i => $word) {
                if (in_array($word, RussianLanguage::$unions, true))
                    $words[$i] = $word;
                else
                    $words[$i] = AdjectivePluralization::pluralize($word, $count, $animateness, $case);
            }
            return $count.' '.implode(' ', $words).' '.NounPluralization::pluralize($count, $noun, $animateness, $case);
        }
        return $count.' '.NounPluralization::pluralize($word, $count, $animateness, $case);
    }

    public static function declensionOfPosts($role, $case) {

        $arrRole = explode(' ', $role);

        // Если должность состоит из одного слова(существительное)
        if (count($arrRole) == 1 && strpos($arrRole[0], '-') == false && !in_array($arrRole[0], ProfessionDeclension::getExcludedWord())) {
            return mb_convert_case(NounDeclension::getCase($arrRole[0], $case), MB_CASE_TITLE, "UTF-8");
        }

        $res = ProfessionDeclension::getRole($arrRole, $case);

        return trim($res);
    }
}