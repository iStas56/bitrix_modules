<?php

namespace Lenvendo\Conversion\Handler;

use Bitrix\Main\Localization\Loc;

class CrmMenu
{
    public static function addConversion(&$items)
    {
        $items[] = array(
            'ID' => 'CONVERSION',
            'MENU_ID' => 'menu_crm_conversion',
            'NAME' => Loc::getMessage('CONVERSION_CRM_MENU_ITEM_STORES'),
            'TITLE' => Loc::getMessage('CONVERSION_CRM_MENU_ITEM_STORES'),
            'URL' => '/crm/conversion/'
        );
    }
}