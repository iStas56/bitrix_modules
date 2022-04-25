<?php

namespace Lenvendo\Conversion\LeadService;

use \Bitrix\Main\Type;
use CCrmDeal;
use CCrmLead;
use Lenvendo\Conversion\Entity\ConversionTable;

class LeadService
{

    public static function getLead($leadId) {

        $lead = [];
        $obRes = CCrmLead::GetList(
            $arOrder = [],
            $arFilter = ['ID' => $leadId],
            $arSelect = [],
            $nPageTop = false
        );

        if ($arRes = $obRes->Fetch())
        {

            $lead = [
                'LEAD_ID' => $arRes['ID'],
                'CONTACT_ID' => $arRes['CONTACT_ID'],
                'COMPANY_ID' => $arRes['COMPANY_ID'],
                'TITLE' => $arRes['TITLE'],
                'ASSIGNED_BY_ID' => $arRes['ASSIGNED_BY_ID'],
                'CREATED_BY' => $arRes['CREATED_BY'],
                'DATE_CREATE' => $arRes['DATE_CREATE'],
                'DATE_CLOSED' => $arRes['DATE_CLOSED'],
            ];
        }

        if (!empty($lead))
            self::addLeadToBd($lead);

    }

    public static function getDeal($dealId) {
        $obResDeal = CCrmDeal::GetList(
            $arOrder = [],
            $arFilter = ['ID' => $dealId],
            $arSelect = [],
            $nPageTop = false
        );

        if ($arRes = $obResDeal->Fetch()) {
            $leadId = self::getLeadId($arRes['LEAD_ID']);
            self::writeDataDealToLead($leadId, $arRes);
        }
    }

    public static function getLeadId($leadId) {
        $dbConvers = ConversionTable::getList(['filter' => ['LEAD_ID' => $leadId]]);
        $conversion = $dbConvers->fetch();

        return $conversion['ID'];
    }

    private static function addLeadToBd($lead)
    {
        \Lenvendo\Conversion\Entity\ConversionTable::add([

            'LEAD_ID' => $lead['LEAD_ID'] ?? '',
            'ASSIGNED_BY_ID' => $lead['ASSIGNED_BY_ID'] ?? '',
            'CONTACT_ID' => $lead['CONTACT_ID'] ?? '',
            'COMPANY_ID' => $lead['COMPANY_ID'] ?? '',
            'CREATED_BY' => $lead['CREATED_BY'] ?? '',

            'TITLE' => $lead['TITLE'] ?? '',

            'DATE_CREATE' => new Type\DateTime($lead['DATE_CREATE']) ?? '',
            'DATE_CLOSED' => new Type\DateTime($lead['DATE_CLOSED']) ?? '',

        ]);
    }

    public static function writeDataDealToLead($leadId, $arRes) {

        ConversionTable::update($leadId, [
            'DEAL_ID' => $arRes['ID'] ?? '',
            'DEAL_ASSIGNED_BY' => $arRes['ASSIGNED_BY_ID']  ?? '',

            'DEAL_TITLE' => $arRes['TITLE']  ?? '',
            'DEAL_COMPANY_TITLE' => $arRes['COMPANY_TITLE']  ?? '',
            'DEAL_CONTACT_FULL_NAME' => $arRes['CONTACT_FULL_NAME']  ?? '',
            'DEAL_STAGE_ID' => $arRes['STAGE_ID']  ?? '',
            'DEAL_STAGE_RESULT' => $arRes['ID'] == 0 ? '' : (($arRes['STAGE_ID'] == 'LOSE' || $arRes['STAGE_ID'] == 'WON') ? ($arRes['STAGE_ID'] == 'WON' ? 'WON' : 'LOSE') : 'IN_PROCESS'),
            'DEAL_ASSIGNED' => $arRes['ASSIGNED_BY']  ?? '',

            'DEAL_OPPORTUNITY' => $arRes['OPPORTUNITY']  ?? '',

            'DEAL_DATE_CREATE' => new Type\DateTime($arRes['DATE_CREATE'])  ?? '',
            'DEAL_DATE_CLOSED' => new Type\DateTime($arRes['CLOSEDATE']) ?? '',
            'DEAL_DATE_MODIFY' => new Type\DateTime($arRes['DATE_MODIFY']) ?? '',

            'DEAL_MODIFY_BY_ID' => $arRes['MODIFY_BY_ID'] ?? '',
        ]);
    }
}