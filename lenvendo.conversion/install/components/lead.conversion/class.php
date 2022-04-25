<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UserTable;
use Bitrix\Main\Grid;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\Uri;
use Lenvendo\Conversion\Entity\ConversionTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


class LeadConversionComponent extends \CBitrixComponent {

    const GRID_ID = 'CONVERSION_LIST_BY_LEAD';
    const SORTABLE_FIELDS = ['ID', 'TITLE', 'ASSIGNED_BY_ID', 'DATE_CREATE', 'DATE_CLOSED', 'DEAL_DATE_CREATE', 'DEAL_DATE_CLOSED', 'DEAL_TITLE', 'DEAL_ASSIGNED_BY', 'DEAL_MODIFY_BY_ID', 'DEAL_STAGE_RESULT', 'DEAL_OPPORTUNITY'];
    const FILTERABLE_FIELDS = array('LEAD_ID', 'DATE_CREATE', 'ASSIGNED_BY_ID', 'DEAL_ASSIGNED_BY', 'DEAL_MODIFY_BY_ID', 'DEAL_STAGE_ID');

    private static array $headers;
    private static $filterFields;
    private static $filterPresets;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;

        parent::__construct($component);

        self::$headers = [
            [
                'id' => 'ID',
                'name' => "ID",
                'sort' => 'ID',
                'first_order' => 'desc',
                'type' => 'int',
            ],
            [
                'id' => 'TITLE',
                'name' => "Лид",
                'sort' => 'TITLE',
                'default' => true,
            ],
            [
                'id' => 'DATE_CREATE',
                'name' => "Дата создания лида",
                'sort' => 'DATE_CREATE',
                'default' => false,
            ],
            [
                'id' => 'DATE_CLOSED',
                'name' => "Дата закрытия лида",
                'sort' => 'DATE_CLOSED',
                'default' => false,
            ],
            [
                'id' => 'DEAL_TITLE',
                'name' => "Сделка",
                'sort' => 'DEAL_TITLE',
                'default' => true,
            ],
            [
                'id' => 'DEAL_DATE_CREATE',
                'name' => "Дата создания сделки",
                'sort' => 'DEAL_DATE_CREATE',
                'default' => false,
            ],
            [
                'id' => 'DEAL_DATE_CLOSED',
                'name' => "Дата закрытия сделки",
                'sort' => 'DEAL_DATE_CLOSED',
                'default' => false,
            ],
            [
                'id' => 'DEAL_CONTACT_FULL_NAME',
                'name' => "Контакт",
                'sort' => 'DEAL_CONTACT_FULL_NAME',
                'default' => false,
            ],
            [
                'id' => 'DEAL_COMPANY_TITLE',
                'name' => "Компания",
                'sort' => 'DEAL_COMPANY_TITLE',
                'default' => false,
            ],
            [
                'id' => 'DEAL_OPPORTUNITY',
                'name' => "Сумма сделки",
                'sort' => 'DEAL_OPPORTUNITY',
                'default' => false,
            ],
            [
                'id' => 'DEAL_STAGE_RESULT',
                'name' => "Стадия сделки",
                'sort' => 'DEAL_STAGE_RESULT',
                'default' => false,
            ],
            [
                'id' => 'ASSIGNED_BY_LEAD',
                'name' => "Ответсвенный лида",
                'sort' => 'ASSIGNED_BY_ID',
                'default' => true,
            ],
            [
                'id' => 'ASSIGNED_BY_DEAL',
                'name' => "Ответсвенный сделки",
                'sort' => 'DEAL_ASSIGNED_BY',
                'default' => false,
            ],
            [
                'id' => 'ASSIGNED_BY_DEAL_MODIFY',
                'name' => "Сотрудник закрывший сделку",
                'sort' => 'DEAL_MODIFY_BY_ID',
                'default' => false,
            ],
        ];

        self::$filterFields = [
            [
                'id' => 'LEAD_ID',
                'name' => 'ID'
            ],
            [
                'id' => 'DATE_CREATE', 'name' => 'Дата', 'type' => 'date'
            ],
            [
                'id' => 'ASSIGNED_BY_ID',
                'name' => 'Ответсвенный лида',
                'type' => 'entity_selector',
                'params' => [
                    'multiple' => 'Y',
                    'dialogOptions' => [
                        'height' => 240,
                        'context' => 'filter',
                        'entities' => [
                            [
                                'id' => 'user',
                                'options' => [
                                    'inviteEmployeeLink' => false
                                ],
                            ],
                            [
                                'id' => 'department',
                            ]
                        ]
                    ],
                ],
                'default' => true,
            ],
            [
                'id' => 'DEAL_ASSIGNED_BY',
                'name' => 'Ответсвенный сделки',
                'type' => 'entity_selector',
                'params' => [
                    'multiple' => 'Y',
                    'dialogOptions' => [
                        'height' => 240,
                        'context' => 'filter',
                        'entities' => [
                            [
                                'id' => 'user',
                                'options' => [
                                    'inviteEmployeeLink' => false
                                ],
                            ],
                            [
                                'id' => 'department',
                            ]
                        ]
                    ],
                ],
                'default' => true,
            ],
            [
                'id' => 'DEAL_MODIFY_BY_ID',
                'name' => 'Сотрудник закрывший сделку',
                'type' => 'entity_selector',
                'params' => [
                    'multiple' => 'Y',
                    'dialogOptions' => [
                        'height' => 240,
                        'context' => 'filter',
                        'entities' => [
                            [
                                'id' => 'user',
                                'options' => [
                                    'inviteEmployeeLink' => false
                                ],
                            ],
                            [
                                'id' => 'department',
                            ]
                        ]
                    ],
                ],
                'default' => true,
            ]
        ];

        self::$filterPresets = array(
//            'deals' => array(
//                'name' => 'deals',
//                'fields' => array(
//                    'DEAL_STAGE_ID' => "LOSE",
//                    //'ASSIGNED_BY_ID_name' => $USER->GetFullName(),
//                )
//            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function executeComponent() {

        if (!Loader::includeModule('lenvendo.conversion')) {
            ShowError('Модуль "Конверсия сделок" не установлен');
            return;
        }

        global $USER;

        if ($this->startResultCache()) {

            $context = Context::getCurrent();
            $request = $context->getRequest();

            //region Sort
            $grid = new Grid\Options(self::GRID_ID);
            $gridSort = $grid->getSorting();
            $sort = array_filter(
                $gridSort['sort'],
                function ($field) {
                    return in_array($field, self::SORTABLE_FIELDS);
                },
                ARRAY_FILTER_USE_KEY
            );
            if (empty($sort)) {
                $sort = array('ASSIGNED_BY_ID' => 'desc');
            }
            //endregion

            //region Filter
            $gridFilter = new Filter\Options(self::GRID_ID, self::$filterPresets);
            $gridFilterValues = $gridFilter->getFilter(self::$filterFields);
            $gridFilterValues = array_filter(
                $gridFilterValues,
                function ($fieldName) {
                    return in_array($fieldName, self::FILTERABLE_FIELDS);
                },
                ARRAY_FILTER_USE_KEY
            );
            //endregion

            // Получение лидов и сделок из БД
            $leadData = $this->getAllLead([
                'filter' => $gridFilterValues,
                'order' => $sort
            ]);

            echo "<pre>";
            //print_r($gridFilterValues);
            echo "</pre>";

            // Подготовка данных для грида
            $rows = $this->getRowsForGrid($leadData);

            // Расчет конверсии
            $conversionData = $this->getConversion($leadData);

            $this->arResult = [
                'ROWS' => $rows,
                'GRID_ID' => self::GRID_ID,
                'HEADERS' => self::$headers,
                'SORT' => $sort,
                'FILTER' => self::$filterFields,
                'FILTER_PRESETS' => self::$filterPresets,
                'ENABLE_LIVE_SEARCH' => false,
                'DISABLE_SEARCH' => true,
                'CONVERSIONS' => $conversionData
            ];

            $this->IncludeComponentTemplate();
        }

    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAllLead($params = []): array
    {

        $dbConvers = ConversionTable::getList($params);
        $conversion = $dbConvers->fetchAll();
        $userIds = [];
        foreach ($conversion as $userId) {

            if ($userId['ASSIGNED_BY_ID'] > 0 && !in_array($userId['ASSIGNED_BY_ID'], $userIds))
                $userIds[] = $userId['ASSIGNED_BY_ID'];

            if ($userId['DEAL_ASSIGNED_BY'] > 0 && !in_array($userId['DEAL_ASSIGNED_BY'], $userIds))
                $userIds[] = $userId['DEAL_ASSIGNED_BY'];

            if ($userId['DEAL_MODIFY_BY_ID'] > 0 && !in_array($userId['DEAL_MODIFY_BY_ID'], $userIds))
                $userIds[] = $userId['DEAL_MODIFY_BY_ID'];

        }

        $dbUsers = UserTable::getList(array(
            'filter' => array('=ID' => $userIds)
        ));
        $users = array();
        foreach ($dbUsers as $user) {
            $users[$user['ID']] = $user;
        }


        foreach ($conversion as &$value) {

            if (intval($value['ASSIGNED_BY_ID']) > 0) {
                $value['ASSIGNED_BY_LEAD'] = $users[$value['ASSIGNED_BY_ID']];
            }

            if (intval($value['DEAL_ASSIGNED_BY']) > 0) {
                $value['ASSIGNED_BY_DEAL'] = $users[$value['DEAL_ASSIGNED_BY']];
            }

            if (intval($value['DEAL_MODIFY_BY_ID']) > 0) {
                $value['ASSIGNED_BY_DEAL_MODIFY'] = $users[$value['DEAL_MODIFY_BY_ID']];
            }
        }

        return $conversion;
    }

    /**
     * @param $data
     * @return array
     */
    public function getConversion($data): array
    {

        $result = [];

        $result['COUNT_LEAD'] = count($data);
        $result['COUNT_SUCCESS'] = 0;

        foreach ($data as $lead) {
            if (!empty($lead['DEAL_ID']) && $lead['DEAL_STAGE_ID'] == "WON")
                $result['COUNT_SUCCESS']++;
        }

        $result['COVERSION'] = ((100 * $result['COUNT_SUCCESS']) / $result['COUNT_LEAD']) . '%';

        return $result;
    }

    /**
     * @param $leadData
     * @return array
     */
    private function getRowsForGrid($leadData) : array
    {
        $rows = [];
        foreach ($leadData as $lead) {

            global $APPLICATION;

            $rows[] = array(
                'id' => $lead['LEAD_ID'],
                'data' => $lead,
                'columns' => array(
                    'ID' => $lead['LEAD_ID'],
                    'TITLE' => '<a href="/crm/lead/details/' . $lead['LEAD_ID'] . '/' . '">' . $lead['TITLE'] . '</a>',
                    'DATE_CREATE' => $lead['DATE_CREATE'],
                    'DATE_CLOSED' => ($lead['DEAL_ID'] == 0) ? $lead['DATE_CLOSED'] : '',
                    'ASSIGNED_BY_LEAD' => empty($lead['ASSIGNED_BY_LEAD']) ? '' : CCrmViewHelper::PrepareUserBaloonHtml(
                        array(
                            'PREFIX' => "CONVERSION_LEAD_{$lead['ID']}_RESPONSIBLE",
                            'USER_ID' => $lead['ASSIGNED_BY_ID'],
                            'USER_NAME'=> CUser::FormatName(CSite::GetNameFormat(), $lead['ASSIGNED_BY_LEAD']),
                            'USER_PROFILE_URL' => str_replace(
                                '#USER_ID#',
                                $lead['ASSIGNED_BY_ID'],
                                Option::get('intranet', 'path_user', '', SITE_ID),
                            )
                        )
                    ),
                    'ASSIGNED_BY_DEAL' => empty($lead['ASSIGNED_BY_DEAL']) && $lead['DEAL_ID'] == 0 ? '' : CCrmViewHelper::PrepareUserBaloonHtml(
                        array(
                            'PREFIX' => "CONVERSION_DEAL_{$lead['ID']}_RESPONSIBLE",
                            'USER_ID' => $lead['DEAL_ASSIGNED_BY'],
                            'USER_NAME'=> CUser::FormatName(CSite::GetNameFormat(), $lead['ASSIGNED_BY_DEAL']),
                            'USER_PROFILE_URL' => str_replace(
                                '#USER_ID#',
                                $lead['DEAL_ASSIGNED_BY'],
                                Option::get('intranet', 'path_user', '', SITE_ID),
                            )
                        )
                    ),
                    'DEAL_TITLE' => ($lead['DEAL_ID'] == 0) ? '' : '<a href="/crm/deal/details/' . $lead['DEAL_ID'] . '/' . '">' . $lead['DEAL_TITLE'] . '</a>',
                    'DEAL_DATE_CREATE' => ($lead['DEAL_ID'] == 0) ? '' : $lead['DEAL_DATE_CREATE'],
                    'DEAL_DATE_CLOSED' => ($lead['DEAL_ID'] == 0) ? '' : $lead['DEAL_DATE_CLOSED'],
                    'DEAL_CONTACT_FULL_NAME' => $lead['DEAL_CONTACT_FULL_NAME'] ?? '',
                    'DEAL_COMPANY_TITLE' => $lead['DEAL_COMPANY_TITLE'] ?? '',
                    'DEAL_OPPORTUNITY' => ($lead['DEAL_ID'] == 0) ? '' : $lead['DEAL_OPPORTUNITY'],
                    'DEAL_STAGE_RESULT' => ($lead['DEAL_ID'] == 0) ? '' : ($lead['DEAL_STAGE_RESULT'] == 'IN_PROCESS' ? 'В работе' : (($lead['DEAL_STAGE_RESULT'] == 'WON') ? 'Выиграна' : 'Проиграна')),
                    'ASSIGNED_BY_DEAL_MODIFY' => ($lead['DEAL_STAGE_RESULT'] == 'WON' || $lead['DEAL_STAGE_RESULT'] == 'LOSE') && !empty($lead['ASSIGNED_BY_DEAL_MODIFY']) && $lead['DEAL_ID'] > 0 ? CCrmViewHelper::PrepareUserBaloonHtml(
                        array(
                            'PREFIX' => "CONVERSION_DEAL_{$lead['ID']}_RESPONSIBLE",
                            'USER_ID' => $lead['DEAL_MODIFY_BY_ID'],
                            'USER_NAME'=> CUser::FormatName(CSite::GetNameFormat(), $lead['ASSIGNED_BY_DEAL_MODIFY']),
                            'USER_PROFILE_URL' => str_replace(
                                '#USER_ID#',
                                $lead['DEAL_MODIFY_BY_ID'],
                                Option::get('intranet', 'path_user', '', SITE_ID),
                            )
                        )
                    ) : '',
                )
            );
        }

        return $rows;
    }
}


