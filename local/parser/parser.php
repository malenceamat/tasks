<?php

use Bitrix\Main\Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

Loader::includeModule('iblock');
$iblock = CIBlock::GetList([], [
    "CODE" => "parser_data"
])->Fetch();

$IBLOCK_ID = $iblock['ID'];

$el = new CIBlockElement();
$file = fopen(__DIR__ . "/vacancy.csv", "r");
$headers = fgetcsv($file, 0, ',');

// чистим заголовки
$headers = array_map(function ($h) {
    return trim(str_replace("\xEF\xBB\xBF", '', $h));
}, $headers);

$map = [
    'Комбинат' => 'COMPANY',
    'Местоположение' => 'LOCATION',
    'Название должности' => 'NAME',
    'Требования' => 'REQUIRE',
    'Обязанности' => 'DUTY',
    'Условия работы' => 'CONDITIONS',
    'Зарплата' => 'SALARY',
    'Категория позиции' => 'TYPE',
    'Тип занятости:' => 'JOB',
    'График работы' => 'SCHEDULE',
    'Сфера деятельности' => 'FIELD',
    'Кому направить резюме (e-mail)' => 'EMAIL',
];

while (($row = fgetcsv($file, 0, ',')) !== false) {

    $row = array_map('trim', $row);
    $data = [];

    foreach ($headers as $i => $header) {
        if ($header === '') {
            continue;
        }
        $data[$header] = $row[$i] ?? '';
    }

    $prop = [];
    foreach ($data as $key => $value) {
        if (!isset($map[$key])) {
            continue;
        }
        $value = trim($value);
        if ($value === '') {
            continue;
        }
        $prop[$map[$key]] = $value;
    }
    $name = $data['Название должности'] ?? 'Без названия';
    $arLoad = [
        "IBLOCK_ID" => $IBLOCK_ID,
        "NAME" => $name,
        "ACTIVE" => "Y",
        "PROPERTY_VALUES" => $prop,
    ];
    $id = $el->Add($arLoad);
}

fclose($file);

echo "DONE";
