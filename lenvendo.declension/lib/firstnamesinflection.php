<?php

namespace Lenvendo\Declension;

use \Lenvendo\Declension\Interfaces\Cases;
use Lenvendo\Declension\Abstracts\NamesInflection;
use Lenvendo\Declension\Interfaces\Gender;
use Lenvendo\Declension\Interfaces\RussianCases;
use \Lenvendo\Declension\Traits\CasesHelper;
use Lenvendo\Declension\Traits\RussianLanguage;

class FirstNamesInflection extends NamesInflection implements Cases
{

    use RussianLanguage, CasesHelper;

    /**
     * @var string[][]
     * @phpstan-var array<string, array<string, string>>
     */
    protected static $exceptions = [
        'лев' => [
            RussianCases::IMENIT => 'Лев',
            RussianCases::RODIT => 'Льва',
            RussianCases::DAT => 'Льву',
            RussianCases::VINIT => 'Льва',
            RussianCases::TVORIT => 'Львом',
            RussianCases::PREDLOJ => 'Льве',
        ],
        'павел' => [
            RussianCases::IMENIT => 'Павел',
            RussianCases::RODIT => 'Павла',
            RussianCases::DAT => 'Павлу',
            RussianCases::VINIT => 'Павла',
            RussianCases::TVORIT => 'Павлом',
            RussianCases::PREDLOJ => 'Павле',
        ]
    ];

    /** @var string[]  */
    protected static $menNames = [
        'абрам', 'аверьян', 'авраам', 'агафон', 'адам', 'азар', 'акакий', 'аким', 'аксён', 'александр', 'алексей',
        'альберт', 'анатолий', 'андрей', 'андрон', 'антип', 'антон', 'аполлон', 'аристарх', 'аркадий', 'арнольд',
        'арсений', 'арсентий', 'артем', 'артём', 'артемий', 'артур', 'аскольд', 'афанасий', 'богдан', 'борис',
        'борислав', 'бронислав', 'вадим', 'валентин', 'валерий', 'варлам', 'василий', 'венедикт', 'вениамин',
        'веньямин', 'венцеслав', 'виктор', 'виген', 'вилен', 'виталий', 'владилен', 'владимир', 'владислав', 'владлен',
        'вова', 'всеволод', 'всеслав', 'вячеслав', 'гавриил', 'геннадий', 'георгий', 'герман', 'глеб', 'григорий',
        'давид', 'даниил', 'данил', 'данила', 'демьян', 'денис', 'димитрий', 'дмитрий', 'добрыня', 'евгений', 'евдоким',
        'евсей', 'егор', 'емельян', 'еремей', 'ермолай', 'ерофей', 'ефим', 'захар', 'иван', 'игнат', 'игорь',
        'илларион', 'иларион', 'илья', 'иосиф', 'казимир', 'касьян', 'кирилл', 'кондрат', 'константин', 'кузьма',
        'лавр', 'лаврентий', 'лазарь', 'ларион', 'лев', 'леонард', 'леонид', 'лука', 'максим', 'марат', 'мартын',
        'матвей', 'мефодий', 'мирон', 'михаил', 'моисей', 'назар', 'никита', 'николай', 'олег', 'осип', 'остап',
        'павел', 'панкрат', 'пантелей', 'парамон', 'пётр', 'петр', 'платон', 'потап', 'прохор', 'роберт', 'ростислав',
        'савва', 'савелий', 'семён', 'семен', 'сергей', 'сидор', 'спартак', 'тарас', 'терентий', 'тимофей', 'тимур',
        'тихон', 'ульян', 'фёдор', 'федор', 'федот', 'феликс', 'фирс', 'фома', 'харитон', 'харлам', 'эдуард',
        'эммануил', 'эраст', 'юлиан', 'юлий', 'юрий', 'яков', 'ян', 'ярослав',
    ];

    /** @var string[]  */
    protected static $womenNames = [
        'авдотья', 'аврора', 'агата', 'агния', 'агриппина', 'ада', 'аксинья', 'алевтина', 'александра', 'алёна',
        'алена', 'алина', 'алиса', 'алла', 'альбина', 'амалия', 'анастасия', 'ангелина', 'анжела', 'анжелика', 'анна',
        'антонина', 'анфиса', 'арина', 'белла', 'божена', 'валентина', 'валерия', 'ванда', 'варвара', 'василина',
        'василиса', 'вера', 'вероника', 'виктория', 'виола', 'виолетта', 'вита', 'виталия', 'владислава', 'власта',
        'галина', 'глафира', 'дарья', 'диана', 'дина', 'ева', 'евгения', 'евдокия', 'евлампия', 'екатерина', 'елена',
        'елизавета', 'ефросиния', 'ефросинья', 'жанна', 'зиновия', 'злата', 'зоя', 'ивонна', 'изольда', 'илона', 'инга',
        'инесса', 'инна', 'ирина', 'ия', 'капитолина', 'карина', 'каролина', 'кира', 'клавдия', 'клара', 'клеопатра',
        'кристина', 'ксения', 'лада', 'лариса', 'лиана', 'лидия', 'лилия', 'лина', 'лия', 'лора', 'любава', 'любовь',
        'людмила', 'майя', 'маргарита', 'марианна', 'мариетта', 'марина', 'мария', 'марья', 'марта', 'марфа', 'марьяна',
        'матрёна', 'матрена', 'матрона', 'милена', 'милослава', 'мирослава', 'муза', 'надежда', 'настасия', 'настасья',
        'наталия', 'наталья', 'нелли', 'ника', 'нина', 'нинель', 'нонна', 'оксана', 'олимпиада', 'ольга', 'пелагея',
        'полина', 'прасковья', 'раиса', 'рената', 'римма', 'роза', 'роксана', 'руфь', 'сарра', 'светлана', 'серафима',
        'снежана', 'софья', 'софия', 'стелла', 'степанида', 'стефания', 'таисия', 'таисья', 'тамара', 'татьяна',
        'ульяна', 'устиния', 'устинья', 'фаина', 'фёкла', 'фекла', 'феодора', 'хаврония', 'христина', 'эвелина',
        'эдита', 'элеонора', 'элла', 'эльвира', 'эмилия', 'эмма', 'юдифь', 'юлиана', 'юлия', 'ядвига', 'яна',
        'ярослава',
    ];

    /** @var string[]  */
    protected static $immutableNames = [
        'николя',
    ];

    /**
     * Checks if name is mutable
     * @param string $name
     * @param null|string $gender
     * @return bool
     */
    public static function isMutable($name, $gender = null)
    {
        $name = MultibyteString::lower($name);

        if (in_array($name, static::$immutableNames, true)) {
            return false;
        }

        if ($gender === null) {
            $gender = static::detectGender($name);
        }

        // man rules
        if ($gender === Gender::MALE) {
            // soft consonant
            if (MultibyteString::lower(MultibyteString::slice($name, -1)) == 'ь' && static::isConsonant(MultibyteString::slice($name, -2, -1))) {
                return true;
            } elseif (in_array(MultibyteString::slice($name, -1), array_diff(static::$consonants, ['й', /*'Ч', 'Щ'*/]), true)) { // hard consonant
                return true;
            } elseif (MultibyteString::slice($name, -1) == 'й') {
                return true;
            } else if (in_array(MultibyteString::slice($name, -2), ['ло', 'ко'], true)) {
                return true;
            }
        } else if ($gender === Gender::FEMALE) {
            // soft consonant
            if (MultibyteString::lower(MultibyteString::slice($name, -1)) == 'ь' && static::isConsonant(MultibyteString::slice($name, -2, -1))) {
                return true;
            } else if (static::isHissingConsonant(MultibyteString::slice($name, -1))) {
                return true;
            }
        }

        // common rules
        if ((in_array(MultibyteString::slice($name, -1), ['а', 'я']) && !static::isVowel(MultibyteString::slice($name, -2, -1))) || in_array(MultibyteString::slice($name, -2), ['ия', 'ья', 'ея', 'оя'], true)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function detectGender($name)
    {
        $name = MultibyteString::lower($name);
        if (in_array($name, static::$menNames, true)) {
            return Gender::MALE;
        } elseif (in_array($name, static::$womenNames, true)) {
            return Gender::FEMALE;
        }

        $man = $woman = 0;
        $last1 = MultibyteString::slice($name, -1);
        $last2 = MultibyteString::slice($name, -2);
        $last3 = MultibyteString::slice($name, -3);

        // try to detect gender by some statistical rules
        //
        if ($last1 == 'й') {
            $man += 0.9;
        }
        if ($last1 == 'ь') {
            $man += 0.02;
        }
        if (in_array($last1, static::$consonants, true)) {
            $man += 0.01;
        }
        if (in_array($last2, ['он', 'ов', 'ав', 'ам', 'ол', 'ан', 'рд', 'мп'], true)) {
            $man += 0.3;
        }
        if (in_array($last2, ['вь', 'фь', 'ль'], true)) {
            $woman += 0.1;
        }
        if (in_array($last2, ['ла'], true)) {
            $woman += 0.04;
        }
        if (in_array($last2, ['то', 'ма'], true)) {
            $man += 0.01;
        }
        if (in_array($last3, ['лья', 'вва', 'ока', 'ука', 'ита'], true)) {
            $man += 0.2;
        }
        if (in_array($last3, ['има'], true)) {
            $woman += 0.15;
        }
        if (in_array($last3, ['лия', 'ния', 'сия', 'дра', 'лла', 'кла', 'опа'], true)) {
            $woman += 0.5;
        }
        if (in_array(MultibyteString::slice($name, -4), ['льда', 'фира', 'нина', 'лита', 'алья', 'аида'], true)) {
            $woman += 0.5;
        }

        return $man === $woman ? null
            : ($man > $woman ? Gender::MALE : Gender::FEMALE);
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

        if (static::isMutable($name, $gender)) {
            // common rules for ия and я
            if (MultibyteString::slice($name, -2) == 'ия') {
                $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                return [
                    RussianCases::IMENIT => $prefix.'я',
                    RussianCases::RODIT => $prefix.'и',
                    RussianCases::DAT => $prefix.'и',
                    RussianCases::VINIT => $prefix.'ю',
                    RussianCases::TVORIT => $prefix.'ей',
                    RussianCases::PREDLOJ => $prefix.'и',
                ];
            } elseif (MultibyteString::slice($name, -1) == 'я') {
                $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
                return [
                    RussianCases::IMENIT => $prefix . 'я',
                    RussianCases::RODIT => $prefix . 'и',
                    RussianCases::DAT => $prefix . 'е',
                    RussianCases::VINIT => $prefix . 'ю',
                    RussianCases::TVORIT => $prefix . 'ей',
                    RussianCases::PREDLOJ => $prefix . 'е',
                ];
            }

            if (!in_array($name, static::$immutableNames, true)) {
                if ($gender === null) {
                    $gender = static::detectGender($name);
                }
                if ($gender === Gender::MALE || $name === 'саша') {
                    if (($result = static::getCasesMan($name)) !== null) {
                        return $result;
                    }
                } elseif ($gender === Gender::FEMALE) {
                    if (($result = static::getCasesWoman($name)) !== null) {
                        return $result;
                    }
                }
            }
        }

        $name = MultibyteString::name($name);
        return array_fill_keys(array(RussianCases::IMENIT, RussianCases::RODIT, RussianCases::DAT, RussianCases::VINIT, RussianCases::TVORIT, RussianCases::PREDLOJ), $name);
    }

    /**
     * @param string $name
     * @return string[]|null
     * @phpstan-return array<string, string>|null
     */
    protected static function getCasesMan($name)
    {
        // special cases for Лев, Павел
        if (isset(static::$exceptions[$name])) {
            return static::$exceptions[$name];
        } elseif (in_array(MultibyteString::slice($name, -1), array_diff(static::$consonants, ['й', /*'Ч', 'Щ'*/]), true)) { // hard consonant
            if (in_array(MultibyteString::slice($name, -2), ['ек', 'ёк'], true)) { // Витек, Санек
                // case for foreign names like Салмонбек
                if (static::isConsonant(MultibyteString::slice($name, -4, -3)))
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2)).'ек';
                else
                    $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -2)).'ьк';
            } else {
                if ($name === 'пётр')
                    $prefix = MultibyteString::name(str_replace('ё', 'е', $name));
                else
                    $prefix = MultibyteString::name($name);
            }
            return [
                RussianCases::IMENIT => MultibyteString::name($name),
                RussianCases::RODIT => $prefix.'а',
                RussianCases::DAT => $prefix.'у',
                RussianCases::VINIT => $prefix.'а',
                RussianCases::TVORIT => RussianLanguage::isHissingConsonant(MultibyteString::slice($name, -1)) || MultibyteString::slice($name, -1) == 'ц' ? $prefix.'ем' : $prefix.'ом',
                RussianCases::PREDLOJ => $prefix.'е',
            ];
        } elseif (MultibyteString::slice($name, -1) == 'ь' && static::isConsonant(MultibyteString::slice($name, -2, -1))) { // soft consonant
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            return [
                RussianCases::IMENIT => $prefix.'ь',
                RussianCases::RODIT => $prefix.'я',
                RussianCases::DAT => $prefix.'ю',
                RussianCases::VINIT => $prefix.'я',
                RussianCases::TVORIT => $prefix.'ем',
                RussianCases::PREDLOJ => $prefix.'е',
            ];
        } elseif (in_array(MultibyteString::slice($name, -2), ['ай', 'ей', 'ой', 'уй', 'яй', 'юй', 'ий'], true)) {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            $postfix = MultibyteString::slice($name, -2) == 'ий' ? 'и' : 'е';
            return [
                RussianCases::IMENIT => $prefix.'й',
                RussianCases::RODIT => $prefix.'я',
                RussianCases::DAT => $prefix.'ю',
                RussianCases::VINIT => $prefix.'я',
                RussianCases::TVORIT => $prefix.'ем',
                RussianCases::PREDLOJ => $prefix.$postfix,
            ];
        } elseif (MultibyteString::slice($name, -1) == 'а' && static::isConsonant($before = MultibyteString::slice($name, -2, -1)) && !in_array($before, [/*'г', 'к', 'х', */'ц'], true)) {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            $postfix = (RussianLanguage::isHissingConsonant($before) || in_array($before, ['г', 'к', 'х'], true)) ? 'и' : 'ы';
            return [
                RussianCases::IMENIT => $prefix.'а',
                RussianCases::RODIT => $prefix.$postfix,
                RussianCases::DAT => $prefix.'е',
                RussianCases::VINIT => $prefix.'у',
                RussianCases::TVORIT => $prefix.($before === 'ш' ? 'е' : 'о').'й',
                RussianCases::PREDLOJ => $prefix.'е',
            ];
        } elseif (MultibyteString::slice($name, -2) == 'ло' || MultibyteString::slice($name, -2) == 'ко') {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            $postfix = MultibyteString::slice($name, -2, -1) == 'к' ? 'и' : 'ы';
            return [
                RussianCases::IMENIT => $prefix.'о',
                RussianCases::RODIT =>  $prefix.$postfix,
                RussianCases::DAT => $prefix.'е',
                RussianCases::VINIT => $prefix.'у',
                RussianCases::TVORIT => $prefix.'ой',
                RussianCases::PREDLOJ => $prefix.'е',
            ];
        }

        return null;
    }

    /**
     * @param string $name
     * @return string[]|null
     * @phpstan-return array<string, string>|null
     */
    protected static function getCasesWoman($name)
    {
        if (MultibyteString::slice($name, -1) == 'а' && !static::isVowel($before = (MultibyteString::slice($name, -2, -1)))) {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            if ($before != 'ц') {
                $postfix = (RussianLanguage::isHissingConsonant($before) || in_array($before, ['г', 'к', 'х'], true)) ? 'и' : 'ы';
                return [
                    RussianCases::IMENIT => $prefix.'а',
                    RussianCases::RODIT => $prefix.$postfix,
                    RussianCases::DAT => $prefix.'е',
                    RussianCases::VINIT => $prefix.'у',
                    RussianCases::TVORIT => $prefix.'ой',
                    RussianCases::PREDLOJ => $prefix.'е',
                ];
            } else {
                return [
                    RussianCases::IMENIT => $prefix.'а',
                    RussianCases::RODIT => $prefix.'ы',
                    RussianCases::DAT => $prefix.'е',
                    RussianCases::VINIT => $prefix.'у',
                    RussianCases::TVORIT => $prefix.'ей',
                    RussianCases::PREDLOJ => $prefix.'е',
                ];
            }
        } elseif (MultibyteString::slice($name, -1) == 'ь' && static::isConsonant(MultibyteString::slice($name, -2, -1))) {
            $prefix = MultibyteString::name(MultibyteString::slice($name, 0, -1));
            return [
                RussianCases::IMENIT => $prefix.'ь',
                RussianCases::RODIT => $prefix.'и',
                RussianCases::DAT => $prefix.'и',
                RussianCases::VINIT => $prefix.'ь',
                RussianCases::TVORIT => $prefix.'ью',
                RussianCases::PREDLOJ => $prefix.'и',
            ];
        } elseif (RussianLanguage::isHissingConsonant(MultibyteString::slice($name, -1))) {
            $prefix = MultibyteString::name($name);
            return [
                RussianCases::IMENIT => $prefix,
                RussianCases::RODIT => $prefix.'и',
                RussianCases::DAT => $prefix.'и',
                RussianCases::VINIT => $prefix,
                RussianCases::TVORIT => $prefix.'ью',
                RussianCases::PREDLOJ => $prefix.'и',
            ];
        }
        return null;
    }

    /**
     * @param string $name
     * @param string $case
     * @param null|string $gender
     * @return string
     * @throws \Exception
     */
    public static function getCase($name, $case, $gender = null)
    {
        $case = static::canonizeCase($case);
        $forms = static::getCases($name, $gender);
        return $forms[$case];
    }
}