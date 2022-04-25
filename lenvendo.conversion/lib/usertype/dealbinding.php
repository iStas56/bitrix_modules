<?php

namespace Lenvendo\Conversion\UserType;


use Lenvendo\Conversion\Entity\ConversionTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserField\TypeBase;

class DealBinding extends TypeBase
{
    const USER_TYPE_ID = 'dealbinding';

    function GetUserTypeDescription ()
    {
        return array(
            'USER_TYPE_ID' => static::USER_TYPE_ID,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('CRM_CONVERSION_BINDING'),
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_INT,
            'EDIT_CALLBACK' => array(__CLASS__, 'GetPublicEdit'),
            'VIEW_CALLBACK' => array(__CLASS__, 'GetPublicView')
        );
    }

    function GetDBColumnType ($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "int(18)";
            case "oracle":
                return "number(18)";
            case "mssql":
                return "int";
        }
        return "int";
    }

    function GetFilterHTML($arUserField, $arHtmlControl)
    {
        return sprintf(
            '<input type="text" name="%s" size="%s" value="%s">',
            $arHtmlControl['NAME'],
            $arUserField['SETTINGS']['SIZE'],
            $arHtmlControl['VALUE']
        );
    }

    function GetFilterData($arUserField, $arHtmlControl)
    {
        return array(
            'id' => $arHtmlControl['ID'],
            'name' => $arHtmlControl['NAME'],
            'filterable' => ''
        );
    }

    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        return !empty($arHtmlControl['VALUE']) ? self::getConversionLink($arHtmlControl['VALUE']) : '&nbsp;';
    }

    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return self::getConversionSelector($arHtmlControl["NAME"], $arHtmlControl["VALUE"]);
    }

    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return self::getConversionSelector($arHtmlControl["NAME"], $arHtmlControl["VALUE"]);
    }

    public static function GetPublicView($arUserField, $arAdditionalParameters = array())
    {
        return !empty($arUserField['VALUE']) ? self::getConversionLink($arUserField['VALUE']) : '&nbsp;';
    }

    public static function GetPublicEdit($arUserField, $arAdditionalParameters = array())
    {
        $fieldName = static::getFieldName($arUserField, $arAdditionalParameters);
        $value = static::getFieldValue($arUserField, $arAdditionalParameters);
        $value = reset($value);

        return self::getConversionSelector($fieldName, $value);
    }

    function OnSearchIndex($arUserField)
    {
        if(is_array($arUserField["VALUE"]))
            return implode("\r\n", $arUserField["VALUE"]);
        else
            return $arUserField["VALUE"];
    }

    private static function getConversionSelector($fieldName, $fieldValue = null)
    {
        if (!Loader::includeModule('lenvendo.conversion')) {
            return '';
        }

        $dbConversion = ConversionTable::getList(array('select' => array('ID', 'NAME')));
        $conversions = $dbConversion->fetchAll();

        $isNoValue = $fieldValue === null;

        ob_start();
        ?>
        <select name="<?= $fieldName ?>">
            <option value="" <?= $isNoValue ? 'selected' : '' ?>>
                <?= Loc::getMessage('CRM_CONVERSION_NO_BINDING') ?>
            </option>
            <? foreach ($conversions as $conversion): ?>
                <?
                $selected = $conversion['ID'] == $fieldValue ? 'selected' : '';
                ?>
                <option value="<?= $conversion['ID'] ?>" <?= $selected ?>>
                    <?= htmlspecialcharsbx($conversion['NAME']) ?>
                </option>
            <? endforeach; ?>
        </select>
        <?
        $selectorHtml = ob_get_clean();

        return $selectorHtml;
    }

    private static function getConversionLink($conversionId)
    {
        if (!Loader::includeModule('lenvendo.conversion')) {
            return '';
        }

        $dbConversion = ConversionTable::getById($conversionId);
        $conversion = $dbConversion->fetch();

        if (empty($conversion)) {
            return '';
        }


        $conversionDetailTemplate = Option::get('lenvendo.conversion', 'CONVERSION_DETAIL_TEMPLATE');
        $conversionUrl = \CComponentEngine::makePathFromTemplate(
            $conversionDetailTemplate,
            array('CONVERSION_ID' => $conversion['ID'])
        );

        return '<a href="' . htmlspecialcharsbx($conversionUrl) . '">' . htmlspecialcharsbx($conversion['NAME']) . '</a>';
    }
}