<?php

namespace Lenvendo\Conversion\Handler;

class LeadAction
{
    public static function addLead(&$items) {
        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/logLead.html", "<br/><br/><pre>".print_r($items,true)."</pre><br/><br/>", FILE_APPEND);

        \Lenvendo\Conversion\LeadService\LeadService::getLead($items['ID']);
    }

    public static function updateLead(&$items) {
        file_put_contents($_SERVER["DOCUMENT_ROOT"]."/logLead.html", "<br/><br/><pre>".print_r($items,true)."</pre><br/><br/>", FILE_APPEND);
    }
}