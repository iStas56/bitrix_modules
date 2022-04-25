<?php

namespace Lenvendo\Conversion\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\UserTable;
use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class ConversionTable extends DataManager
{
    public static function getTableName()
    {
        return 'lenvendo_conversion';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array('primary' => true, 'autocomplete' => true)),

            new IntegerField('LEAD_ID'),
            new IntegerField('ASSIGNED_BY_ID'),
            new IntegerField('CONTACT_ID'),
            new IntegerField('COMPANY_ID'),
            new IntegerField('CREATED_BY'),


            new StringField('TITLE'),

            new Entity\DatetimeField('DATE_CREATE'),
            new Entity\DatetimeField('DATE_CLOSED'),

            new IntegerField('DEAL_ID'),
            new IntegerField('DEAL_ASSIGNED_BY'),

            new StringField('DEAL_TITLE'),
            new StringField('DEAL_COMPANY_TITLE'),
            new StringField('DEAL_CONTACT_FULL_NAME'),
            new StringField('DEAL_STAGE_ID'),
            new StringField('DEAL_STAGE_RESULT'),
            new StringField('DEAL_ASSIGNED'),

            new FloatField('DEAL_OPPORTUNITY'),

            new Entity\DatetimeField('DEAL_DATE_CREATE'),
            new Entity\DatetimeField('DEAL_DATE_CLOSED'),
            new Entity\DatetimeField('DEAL_DATE_MODIFY'),

            new IntegerField('DEAL_MODIFY_BY_ID'),

            new ReferenceField(
                'ASSIGNED_BY_ID',
                UserTable::getEntity(),
                array('=this.ASSIGNED_BY_ID' => 'ref.ID')
            )
        );
    }
}