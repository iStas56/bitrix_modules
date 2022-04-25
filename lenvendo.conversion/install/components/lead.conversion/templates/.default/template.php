<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Config\Option;
use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

if (!Loader::includeModule('crm')) {
    ShowError('Модуль CRM не подключен');
    return;
}

//echo "<pre>";
//print_r('button');
//echo "</pre>";

$APPLICATION->IncludeComponent(
    'bitrix:crm.control_panel',
    '',
    array(
        'ID' => 'CONVERSION',
        'ACTIVE_ITEM_ID' => 'CONVERSION',
    ),
    $component
);


ob_start();
?>
<style>
    .time_credit_block .intranet-contact-item-selected:before {
        display: none;
    }
    .time_credit_block .intranet-contact-name-text {
        max-width: 100%;
    }
    .time_credit_block .intranet-contact-logo {
        color: #fff;
        font-weight: bold;
        font-size: 20px;
        text-align: center;
        line-height: 40px;
    }
</style>
<div class="intranet-contact-block time_credit_block">
    <div class="intranet-contact-wrap" id="intranet-contact-wrap" style="padding-top: 10px">
        <div class="intranet-contact-list" id="intranet-contact-list" style="margin-left: -14.1167px; margin-top: -14.1167px;">


            <div class="intranet-contact-item intranet-contact-item-selected intranet-contact-item-darkgreen" title="Форма на сайт" id="feed-add-post-form-link-text-form" style="width: 250px; height: 111.757px; margin-left: 14.1167px; margin-top: 14.1167px;">
                <div class="intranet-contact-logo-container">
                    <span class="intranet-contact-logo"><?=$arResult['CONVERSIONS']['COUNT_LEAD']?></span>
                </div>
                <div class="intranet-contact-name">
                    <span class="intranet-contact-name-text">Количество лидов, шт</span>
                </div>
            </div>

            <div class="intranet-contact-item intranet-contact-item-selected intranet-contact-item-green" title="Почта" onclick="window.open(\'/mail/\',\'_blank\');" style="width: 250px; height: 111.757px; margin-left: 14.1167px; margin-top: 14.1167px;">
                <div class="intranet-contact-logo-container">
                    <span class="intranet-contact-logo"><?=$arResult['CONVERSIONS']['COUNT_SUCCESS']?></span>
                </div>
                <div class="intranet-contact-name">
                    <span class="intranet-contact-name-text">Количество выигранных сделок, шт</span>
                </div>
            </div>

            <div class="intranet-contact-item intranet-contact-item-selected intranet-contact-item-blue" title="Почта" onclick="window.open(\'/mail/\',\'_blank\');" style="width: 250px; height: 111.757px; margin-left: 14.1167px; margin-top: 14.1167px;">
                <div class="intranet-contact-logo-container">
                    <span class="intranet-contact-logo"><?=round($arResult['CONVERSIONS']['COVERSION'])?></span>
                </div>
                <div class="intranet-contact-name">
                    <span class="intranet-contact-name-text">Конверсия, %</span>
                </div>
            </div>

        </div>
    </div>
</div>
<?
$Html = ob_get_clean();

global $APPLICATION;
$APPLICATION->AddViewContent('below_pagetitle', $Html);



//$asset = Asset::getInstance();
//$asset->addJs('/bitrix/js/crm/interface_grid.js');
//
//$gridManagerId = $arResult['GRID_ID'] . '_MANAGER';

$snippet = new Snippet();

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.grid',
    'titleflex',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'ROWS' => $arResult['ROWS'],
        'SORT' => $arResult['SORT'],
        'FILTER' => $arResult['FILTER'],
        'FILTER_PRESETS' => $arResult['FILTER_PRESETS'],
        'IS_EXTERNAL_FILTER' => false,
        'ENABLE_LIVE_SEARCH' => $arResult['ENABLE_LIVE_SEARCH'],
        'DISABLE_SEARCH' => $arResult['DISABLE_SEARCH'],
        'ENABLE_ROW_COUNT_LOADER' => true,
        'AJAX_ID' => '',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_LOADER' => null,
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y',)
);






?>


<?php //require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>

