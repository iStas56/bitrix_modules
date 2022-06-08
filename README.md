# bitrix_modules для модуля склонений(declension)

Подключение модуля
Loader::includeModule('lenvendo.declension');

Константы падежей
Lenvendo\Declension\Interfaces\Cases::NOMINATIVE    	 //именительный    что, кто?
Lenvendo\Declension\Interfaces\Cases::GENITIVE            	 //родительный       чего, кого?
Lenvendo\Declension\Interfaces\Cases::DATIVE                	 //дательный           чему, кому?
Lenvendo\Declension\Interfaces\Cases::ACCUSATIVE         	 //винительный       что, кого?
Lenvendo\Declension\Interfaces\Cases::ABLATIVE             	 //творительный     чем, кем?
Lenvendo\Declension\Interfaces\Cases::PREPOSITIONAL 	 //предложный        о ком, о чем?


Склонение имен в определенном падеже

Lenvendo\Declension\Declension::inflectName($fullname, $case, $gender = null);

Аргументы:
* $fullname - имя в формате Имя, Фамилия Имя или Фамилия Имя Отчество.
* $case - нужный падеж. Одна из констант Lenvendo\Declension\Interfaces\Cases ИЛИ строка (nominative, genitive, dative, ablative или prepositional).
* $gender - пол владельца имени. одна из констант Gender (Lenvendo\Declension\Interfaces\Gender::MALE или Lenvendo\Declension\Interfaces\Gender::FEMALE) ИЛИ строка (m для мужского имени, f для женского имени). Если не указывать, будет произведена попытка автоматического определения.

Пример.

Lenvendo\Declension\Declension::inflectName("Иванов Иван Иванович", Cases::GENITIVE) => «Иванова Ивана Ивановича»
Lenvendo\Declension\Declension::inflectName("Иванов Иван", 'dative') => «Иванову Ивану»
Lenvendo\Declension\Declension::inflectName("Иван", Cases::ABLATIVE) => «Иваном»


Склонение отдельных частей имени

Для склонения отдельных частей имени есть три класса:
* FirstNamesInflection - класс для склонения имён.
* MiddleNamesInflection - класс для склонения отчеств.
* LastNamesInflection - класс для склонения фамилий.


use Lenvendo\Declension\FirstNamesInflection;
use Lenvendo\Declension\MiddleNamesInflection;
use Lenvendo\Declension\LastNamesInflection;

Имя:
$name = 'Иван';

echo FirstNamesInflection::getCase($name, Cases::DATIVE); => Ивану

print_r(FirstNamesInflection::getCases($name)); =>
(
    [nominative] => Иван
    [genitive] => Ивана
    [dative] => Ивану
    [accusative] => Ивана
    [ablative] => Иваном
    [prepositional] => Иване
)


Отчество:
$name = 'Иванович';

echo MiddleNamesInflection::getCase($name, Cases::DATIVE); => Ивановичу

print_r(MiddleNamesInflection::getCases($name)); =>
(
    [nominative] => Иванович
    [genitive] => Ивановича
    [dative] => Ивановичу
    [accusative] => Ивановича
    [ablative] => Ивановичем
    [prepositional] => Ивановиче
)


Фамилия:
$name = 'Иванов';

LastNamesInflection::getCase($name, Cases::DATIVE); => Иванову

print_r(LastNamesInflection::getCases($name)); => 
(
    [nominative] => Иванов
    [genitive] => Иванова
    [dative] => Иванову
    [accusative] => Иванова
    [ablative] => Ивановым
    [prepositional] => Иванове
)

 



Для получения сразу всех склонений для имени используйте другую функцию:

echo Lenvendo\Declension\Declension::getNameCases("Иванов Иван Иванович»);

Array
(
    [nominative] => Иванов Иван Иванович
    [genitive] => Иванова Ивана Ивановича
    [dative] => Иванову Ивану Ивановичу
    [accusative] => Иванова Ивана Ивановича
    [ablative] => Ивановым Иваном Ивановичем
    [prepositional] => Иванове Иване Ивановиче
)

Для определения пола:
echo Lenvendo\Declension\Declension::detectGender("Иванов Иван Иванович")

Если удалось определить пол, будет возвращена одна из констант класса Lenvendo\Declension\Interfaces\Gender


Для склонения существительных, используемых с количеством предметов/чего-либо предназначена функция pluralize:
Lenvendo\Declension\Declension::pluralize($count, $noun)

* $count - количество предметов.
* $noun - существительное ИЛИ существительное с прилагательными. Примеры: "сообщение", "новое сообщение", "небольшая лампа", "новый и свободный дом".


Примеры:

echo Lenvendo\Declension\Declension::pluralize(15, 'замароженная рыба') => «15 замароженных рыб»
echo 'Отпуск на ' . Lenvendo\Declension\Declension::pluralize(15, 'календарный день’); => «Отпуск на 15 календарных дней»


Склонение в единственном числе


echo Lenvendo\Declension\NounDeclension::getCase(‘поле', Cases::GENITIVE) => «поля»

Lenvendo\Declension\NounDeclension::getCases('линейка')

Array
(
    [nominative] => линейка
    [genitive] => линейки
    [dative] => линейке
    [accusative] => линейку
    [ablative] => линейкой
    [prepositional] => линейке
)


Склонение во множественном числе

echo Lenvendo\Declension\NounPluralization::getCase('поле', Cases::GENITIVE) => «полей»

Lenvendo\Declension\NounPluralization::getCases('линейка')
Array
(
    [nominative] => линейки
    [genitive] => линеек
    [dative] => линейкам
    [accusative] => линейки
    [ablative] => линейками
    [prepositional] => линейках
)



Склонение должностей

Lenvendo\Declension\Declension::declensionOfPosts($role, $cases)

Аргументы:
* $role - Должность в виде строки
* $case - нужный падеж. Одна из констант Lenvendo\Declension\Interfaces\Cases ИЛИ строка (nominative, genitive, dative, ablative или prepositional).


Должность состоящая из нескольких слов:
$role = «Дежурный системный администратор 1й линии 1й категории»

Lenvendo\Declension\Declension::declensionOfPosts($role, Cases::GENITIVE) => «Дежурнoго системнoго администратора 1й линии 1й категории»
Lenvendo\Declension\Declension::declensionOfPosts($role, Cases::DATIVE) =>  «Дежурному системному администратору 1й линии 1й категории» 

Должность из двух слов разделенных тире:
Lenvendo\Declension\Declension::declensionOfPosts(«Техник-конструктор», Cases::GENITIVE) => «Техника-конструктора»
Lenvendo\Declension\Declension::declensionOfPosts(«Техник-конструктор», Cases::DATIVE) => «Технику-конструктору»

Женские должности:
Lenvendo\Declension\Declension::declensionOfPosts(«Проводница», Cases::GENITIVE) => «Проводницы»
Lenvendo\Declension\Declension::declensionOfPosts(«Проводница», Cases::ABLATIVE) => «Проводницой»


Генерация цифр в строку

$number = 15034;
echo $number . ' (' . CardinalNumeralGenerator::getCase($number, Cases::NOMINATIVE) . ') '; => 15034 (пятнадцать тысяч тридцать четыре)

Валюты

use \Lenvendo\Declension\Interfaces\Currency;
use Lenvendo\Declension\MoneySpeller;

$employee = 'Иванов Иван Иванович';
$number = 15034;

Сумма записывается цифрами, а валюта словами
echo MoneySpeller::spell($number, Currency::RUBLE, MoneySpeller::SHORT_FORMAT); => 15034 рубля 56 копеек

Сумма и валюта записываются словами( если третий параметр пустой, либо указать MoneySpeller::NORMAL_FORMAT)
echo MoneySpeller::spell($number, Currency::RUBLE); => пятнадцать тысяч тридцать четыре рубля пятьдесят шесть копеек

Сумма и валюта записываются словами. Сумма дублируется цифрами в скобках
echo MoneySpeller::spell($number, Currency::RUBLE, MoneySpeller::DUPLICATION_FORMAT); => пятнадцать тысяч тридцать четыре (15034) рубля пятьдесят шесть (56) копеек

Сумма записывается словами и цифрами (в скобках), валюта - словами.
echo MoneySpeller::spell($number, Currency::YUAN, MoneySpeller::CLARIFICATION_FORMAT); => 15034 (пятнадцать тысяч тридцать четыре) юаня 56 (пятьдесят шесть) цзя

Также можно указать падеж для склонения четвёртым параметром:
echo MoneySpeller::spell($number, Currency::RUBLE, MoneySpeller::DUPLICATION_FORMAT, Cases::DATIVE); => пятнадцати тысячам тридцати четырем (15034) рублям пятидесяти шести (56) копейкам


Доступные валюты:
$	Currency::DOLLAR	доллар
€	Currency::EURO	евро
¥	Currency::YEN	иена
£	Currency::POUND	фунт
Fr	Currency::FRANC	франк
元	Currency::YUAN	юань
Kr	Currency::KRONA	крона
MXN	Currency::PESO	песо
₩	Currency::WON	вон
₺	Currency::LIRA	лира
₽	Currency::RUBLE	рубль
₹	Currency::RUPEE	рупия
R$	Currency::REAL	реал
R	Currency::RAND	рэнд
₴	Currency::HRYVNIA	гривна

Пример. Генерация документа о выдаче премии сотруднику:
echo 'Выдать ' .  Lenvendo\Declension\Declension::inflectName($employee, Cases::DATIVE) . ' премию в размере ' .
    MoneySpeller::spell($number, Currency::RUBLE, MoneySpeller::DUPLICATION_FORMAT);
Вывод:
Выдать Иванову Ивану Ивановичу премию в размере пятнадцать тысяч тридцать четыре (15034) рубля пятьдесят шесть (56) копеек


Предлоги

use Lenvendo\Declension\Traits\RussianLanguage;
use Lenvendo\Declension\FirstNamesInflection;
use Lenvendo\Declension\LastNamesInflection;
use Lenvendo\Declension\Declension;


echo RussianLanguage::about(Declension::inflectName("Иванов Иван Иванович", Cases::PREPOSITIONAL)); => об Иванове Иване Ивановиче

Отдельно имя:
echo RussianLanguage::about(FirstNamesInflection::getCase( 'Андрей', Cases::PREPOSITIONAL)); => об Андрее

Фамилия:
echo RussianLanguage::about(LastNamesInflection::getCase( 'Пушкин', Cases::PREPOSITIONAL)); => о Пушкине



Окончания глаголов

use \Lenvendo\Declension\Traits\RussianLanguage;
use \Lenvendo\Declension\Interfaces\Gender;


Аргументы:
* $verb - глагол в мужском роде и прошедшем времени.
* $gender - необходимый род глагола. Если указано не Gender::MALE, то будет произведено преобразование в женский род.


$name = 'Анастасия';
$gender = Gender::FEMALE;

echo $name.' '.RussianLanguage::verb('добавил', $gender); => Анастасия добавила
echo $name.' '.RussianLanguage::verb('поделился', $gender).' публикацией'; => Анастасия поделилась публикацией

Также можно определять пол на лету:
$gender = Lenvendo\Declension\Declension::detectGender($name);
echo $name.' '.RussianLanguage::verb('добавил', $gender); => Анастасия добавила

Случай с мужским именем:
$name = 'Сергей';
$gender = Lenvendo\Declension\Declension::detectGender($name);
echo $name.' '.RussianLanguage::verb('добавил', $gender); => Сергей добавил
