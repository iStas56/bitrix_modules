<?php

namespace Lenvendo\Conversion\Handler;

class DealAction
{
    public static function addDeal(&$items) {

        \Lenvendo\Conversion\LeadService\LeadService::getDeal($items['ID']);
    }

    public static function updateDeal(&$items) {
        \Lenvendo\Conversion\LeadService\LeadService::getDeal($items['ID']);
    }
}