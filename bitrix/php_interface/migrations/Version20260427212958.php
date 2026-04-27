<?php

namespace Sprint\Migration;


class Version20260427212958 extends Version
{
    protected $description = "Vacancy parser iblock";

    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->saveIblockType([
            'ID' => 'parser',
            'LANG' => [
                'ru' => [
                    'NAME' => 'Парсер',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы',
                ],
            ],
        ]);
        $iblockId = $helper->Iblock()->saveIblock([
            'NAME' => 'Вакансии',
            'CODE' => 'parser_data',
            'IBLOCK_TYPE_ID' => 'parser',
            'LID' => 's1',
            'SITE_ID' => ['s1'],
        ]);
        $props = [
            'COMPANY' => 'Комбинат',
            'LOCATION' => 'Местоположение',
            'REQUIRE' => 'Требования',
            'DUTY' => 'Обязанности',
            'CONDITIONS' => 'Условия',
            'SALARY' => 'Зарплата',
            'JOB' => 'Тип занятости',
            'SCHEDULE' => 'График',
            'FIELD' => 'Сфера',
            'EMAIL' => 'Email',
            'TYPE' => 'Тип',
        ];
        foreach ($props as $code => $name) {
            $helper->Iblock()->saveProperty($iblockId, [
                'NAME' => $name,
                'CODE' => $code,
                'PROPERTY_TYPE' => 'S',
            ]);
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $helper->Iblock()->deleteIblockIfExists('parser_data');
        $helper->Iblock()->deleteIblockTypeIfExists('parser');
    }
}
