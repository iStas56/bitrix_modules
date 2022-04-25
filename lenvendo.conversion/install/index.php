<?php
defined('B_PROLOG_INCLUDED') || die;

use Lenvendo\Conversion\Entity\ConversionTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use \Bitrix\Main\Type;

class lenvendo_conversion extends CModule
{
    const MODULE_ID = 'lenvendo.conversion';
    var $MODULE_ID = self::MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = Loc::getMessage('LENVENDO_CONVERSION.MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('LENVENDO_CONVERSION.MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage('LENVENDO_CONVERSION.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('LENVENDO_CONVERSION.PARTNER_URI');
    }

    function DoInstall()
    {
        ModuleManager::registerModule(self::MODULE_ID);

        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
        $this->HatvestAllLeadAndDealsData();
    }

    function DoUninstall()
    {
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UnInstallDB();

        ModuleManager::unRegisterModule(self::MODULE_ID);
    }

    function InstallDB()
    {
        Loader::includeModule('lenvendo.conversion');

        $db = Application::getConnection();

        $storeEntity = ConversionTable::getEntity();
        if (!$db->isTableExists($storeEntity->getDBTableName())) {
            $storeEntity->createDbTable();
        }
    }

    function UnInstallDB()
    {
        Loader::includeModule('lenvendo.conversion');

        $db = Application::getConnection();

        $storeEntity = ConversionTable::getEntity();

        $db->dropTable($storeEntity->getDBTableName());
    }

    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandlerCompatible(
            'crm',
            'OnAfterCrmControlPanelBuild',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\CrmMenu',
            'addConversion'
        );

        $eventManager->registerEventHandlerCompatible(
            'crm',
            'OnAfterCrmLeadAdd',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\LeadAction',
            'addLead'
        );

        $eventManager->registerEventHandlerCompatible(
            'crm',
            'OnAfterCrmLeadUpdate',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\LeadAction',
            'updateLead'
        );

        $eventManager->registerEventHandlerCompatible(
            'crm',
            'OnAfterCrmDealAdd',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\DealAction',
            'addDeal'
        );

        $eventManager->registerEventHandlerCompatible(
            'crm',
            'OnAfterCrmDealUpdate',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\DealAction',
            'updateDeal'
        );

        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnUserTypeBuildList',
            self::MODULE_ID,
            '\Lenvendo\Conversion\UserType\DealBinding',
            'GetUserTypeDescription'
        );
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnAfterCrmControlPanelBuild',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\CrmMenu',
            'addConversion'
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnAfterCrmLeadAdd',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\LeadAction',
            'addLead'
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnAfterCrmLeadUpdate',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\LeadAction',
            'updateLead'
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnAfterCrmDealAdd',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\DealAction',
            'addDeal'
        );

        $eventManager->unRegisterEventHandler(
            'crm',
            'OnAfterCrmDealUpdate',
            self::MODULE_ID,
            '\Lenvendo\Conversion\Handler\DealAction',
            'updateDeal'
        );

        $eventManager->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            self::MODULE_ID,
            '\Lenvendo\Conversion\UserType\DealBinding',
            'GetUserTypeDescription'
        );
    }

    function InstallFiles()
    {

        $documentRoot = Application::getDocumentRoot();

        CopyDirFiles(
            __DIR__ . '/components',
            $documentRoot . '/local/components/lenvendo',
            true,
            true
        );

    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx('/local/components/lenvendo/lead.conversion');

        //return false;
    }

    private function HatvestAllLeadAndDealsData()
    {
        $result = [];
        $obRes = CCrmLead::GetList(
            $arOrder = [],
            $arFilter = [],
            $arSelect = ['ID', 'CONTACT_ID', 'COMPANY_ID', 'TITLE', 'ASSIGNED_BY', 'ASSIGNED_BY_ID', 'CREATED_BY', 'DATE_CREATE', 'DATE_CLOSED', 'ASSIGNED_BY_NAME', 'ASSIGNED_BY_SECOND_NAME'],
            $nPageTop = false
        );

        while ($arRes = $obRes->Fetch())
        {

            $result[$arRes['ID']] = [
                'LEAD_ID' => $arRes['ID'],
                'CONTACT_ID' => $arRes['CONTACT_ID'],
                'COMPANY_ID' => $arRes['COMPANY_ID'],
                'TITLE' => $arRes['TITLE'],
                'ASSIGNED_BY_ID' => $arRes['ASSIGNED_BY_ID'],
                'CREATED_BY' => $arRes['CREATED_BY'],
                'DATE_CREATE' => $arRes['DATE_CREATE'],
                'DATE_CLOSED' => $arRes['DATE_CLOSED'],
                'ASSIGNED' => $arRes['ASSIGNED_BY_LAST_NAME'] . ' ' . $arRes['ASSIGNED_BY_NAME'] . ' ' . $arRes['ASSIGNED_BY_SECOND_NAME'],
            ];
        }

        $obResDeal = CCrmDeal::GetList(
            $arOrder = ['DATE_CREATE' => 'DESC'],
            $arFilter = ['=LEAD_ID' => array_keys($result)],
            $arSelect = [],
            $nPageTop = false
        );

        while ($arRes = $obResDeal->Fetch())
        {

            $result[$arRes['LEAD_ID']]['DEAL_ID'] = $arRes['ID'];
            $result[$arRes['LEAD_ID']]['DEAL_TITLE'] = $arRes['TITLE'];
            $result[$arRes['LEAD_ID']]['DEAL_ASSIGNED_BY'] = $arRes['ASSIGNED_BY_ID'];
            $result[$arRes['LEAD_ID']]['DEAL_DATE_CREATE'] = $arRes['DATE_CREATE'];
            $result[$arRes['LEAD_ID']]['DEAL_DATE_CLOSED'] = $arRes['CLOSEDATE'];
            $result[$arRes['LEAD_ID']]['DEAL_CONTACT_FULL_NAME'] = $arRes['CONTACT_FULL_NAME'];
            $result[$arRes['LEAD_ID']]['DEAL_COMPANY_TITLE'] = $arRes['COMPANY_TITLE'];
            $result[$arRes['LEAD_ID']]['DEAL_STAGE_ID'] = $arRes['STAGE_ID'];
            $result[$arRes['LEAD_ID']]['DEAL_ASSIGNED'] = $arRes['ASSIGNED_BY_LAST_NAME'] . ' ' . $arRes['ASSIGNED_BY_NAME'] . ' ' . $arRes['ASSIGNED_BY_SECOND_NAME'];
            $result[$arRes['LEAD_ID']]['DEAL_MODIFY_BY_ID'] = $arRes['MODIFY_BY_ID'];
            $result[$arRes['LEAD_ID']]['DEAL_DATE_MODIFY'] = $arRes['DATE_MODIFY'];
            $result[$arRes['LEAD_ID']]['DEAL_OPPORTUNITY'] = $arRes['OPPORTUNITY'];
        }


        foreach ($result as $lead) {
            \Lenvendo\Conversion\Entity\ConversionTable::add([

                'LEAD_ID' => $lead['LEAD_ID'] ?? '',
                'ASSIGNED_BY_ID' => $lead['ASSIGNED_BY_ID'] ?? '',
                'CONTACT_ID' => $lead['CONTACT_ID'] ?? '',
                'COMPANY_ID' => $lead['COMPANY_ID'] ?? '',
                'CREATED_BY' => $lead['CREATED_BY'] ?? '',

                'TITLE' => $lead['TITLE'] ?? '',

                'DATE_CREATE' => new Type\DateTime($lead['DATE_CREATE']) ?? '',
                'DATE_CLOSED' => new Type\DateTime($lead['DATE_CLOSED']) ?? '',

                'DEAL_ID' => $lead['DEAL_ID'] ?? '',
                'DEAL_ASSIGNED_BY' => $lead['DEAL_ASSIGNED_BY']  ?? '',

                'DEAL_TITLE' => $lead['DEAL_TITLE']  ?? '',
                'DEAL_COMPANY_TITLE' => $lead['DEAL_COMPANY_TITLE']  ?? '',
                'DEAL_CONTACT_FULL_NAME' => $lead['DEAL_CONTACT_FULL_NAME']  ?? '',
                'DEAL_STAGE_ID' => $lead['DEAL_STAGE_ID']  ?? '',
                'DEAL_STAGE_RESULT' => $lead['DEAL_ID'] == 0 ? '' : (($lead['DEAL_STAGE_ID'] == 'LOSE' || $lead['DEAL_STAGE_ID'] == 'WON') ? ($lead['DEAL_STAGE_ID'] == 'WON' ? 'WON' : 'LOSE') : 'IN_PROCESS'),
                'DEAL_ASSIGNED' => $lead['DEAL_ASSIGNED']  ?? '',

                'DEAL_OPPORTUNITY' => $lead['DEAL_OPPORTUNITY']  ?? '',

                'DEAL_DATE_CREATE' => new Type\DateTime($lead['DEAL_DATE_CREATE'])  ?? '',
                'DEAL_DATE_CLOSED' => new Type\DateTime($lead['DEAL_DATE_CLOSED']) ?? '',
                'DEAL_DATE_MODIFY' => new Type\DateTime($lead['DEAL_DATE_MODIFY']) ?? '',

                'DEAL_MODIFY_BY_ID' => $lead['DEAL_MODIFY_BY_ID'] ?? '',
            ]);
        }
    }
}