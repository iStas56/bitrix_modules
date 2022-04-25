<?php
namespace Lenvendo\Declension\Interfaces;

interface Cases
{
    const NOMINATIVE = 'nominative';        //именительный   что, кто?
    const GENITIVE = 'genitive';            //родительный    чего, кого?
    const DATIVE = 'dative';                //дательный      чему, кому?
    const ACCUSATIVE = 'accusative';        //винительный    что, кого?
    const ABLATIVE = 'ablative';            //творительный   чем, кем?
    const PREPOSITIONAL = 'prepositional';  //предложный     о ком, о чем?
}
