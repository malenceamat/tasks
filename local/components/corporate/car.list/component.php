<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

// подключаем модуль инфоблоков
if (!Loader::includeModule('iblock')) {
    ShowError('Модуль iblock не подключён');
    return;
}

// ID инфоблоков из параметров компонента
$carsIblockId = $arParams['IBLOCK_CARS_ID'];
$bookingsIblockId = $arParams['IBLOCK_BOOKINGS_ID'];
$positionsIblockId = $arParams['IBLOCK_POSITIONS_ID'];

// получаем время поездки из GET-параметров
$dateStartRaw = isset($_GET['date_start']) ? trim($_GET['date_start']) : '';
$dateEndRaw = isset($_GET['date_end']) ? trim($_GET['date_end']) : '';

if (empty($dateStartRaw) || empty($dateEndRaw)) {
    ShowError('Не переданы параметры date_start и date_end');
    return;
}
// получаем текущего пользователя
global $USER;
if (!$USER->IsAuthorized()) {
    ShowError('Пользователь не авторизован');
    return;
}
$currentUserId = $USER->GetID();
// получаем должность текущего пользователя
$dbUser = CUser::GetByID($currentUserId);
$arUser = $dbUser->Fetch();
$positionId = $arUser['UF_POSITION'];
if (!$positionId) {
    ShowError('У пользователя не задана должность');
    return;
}
// получаем допустимые категории комфорта для этой должности
$dbPosition = CIBlockElement::GetByID($positionId);
$arPosition = $dbPosition->GetNextElement();
$arPositionProps = $arPosition->GetProperties();
$allowedCategories = [];
if (!empty($arPositionProps['COMFORT_CATEGORIES']['VALUE'])) {
    $val = $arPositionProps['COMFORT_CATEGORIES']['VALUE'];
    $allowedCategories = is_array($val) ? $val : [$val];
    $allowedCategories = array_filter(array_map('intval', $allowedCategories));
}
if (empty($allowedCategories)) {
    ShowError('Для вашей должности не заданы категории комфорта');
    return;
}
// получаем все автомобили с допустимой категорией комфорта
$arCars = [];
$rsCars = CIBlockElement::GetList(
    ['NAME' => 'ASC'],
    [
        'IBLOCK_ID' => $carsIblockId,
        'ACTIVE' => 'Y',
        'PROPERTY_COMFORT_CATEGORY' => $allowedCategories,
    ],
    false,
    false,
    ['ID', 'NAME', 'PROPERTY_COMFORT_CATEGORY', 'PROPERTY_DRIVER']
);
while ($arCar = $rsCars->GetNext()) {
    $arCars[$arCar['ID']] = $arCar;
}
if (empty($arCars)) {
    $this->arResult = ['CARS' => []];
    return;
}
$carIds = array_keys($arCars);
// получаем занятые автомобили в указанный период
$bookedCarIds = [];
$rsBookings = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => $bookingsIblockId,
        'ACTIVE' => 'Y',
    ],
    false,
    false,
    ['ID', 'PROPERTY_CAR', 'PROPERTY_DATE_START', 'PROPERTY_DATE_END']
);
$requestStartTs = strtotime($dateStartRaw);
$requestEndTs = strtotime($dateEndRaw);
while ($arBooking = $rsBookings->GetNext()) {
    $bookingCarId = $arBooking['PROPERTY_CAR_VALUE'];
    $bookingStart = $arBooking['PROPERTY_DATE_START_VALUE'];
    $bookingEnd = $arBooking['PROPERTY_DATE_END_VALUE'];
    // конвертируем формат д.м.г ч:м:с в timestamp
    $bookingStartTs = strtotime(str_replace('.', '-', substr($bookingStart, 0, 10)) . substr($bookingStart, 10));
    $bookingEndTs = strtotime(str_replace('.', '-', substr($bookingEnd, 0, 10)) . substr($bookingEnd, 10));
    // проверяем пересечение интервалов
    if ($bookingStartTs < $requestEndTs && $bookingEndTs > $requestStartTs) {
        $bookedCarIds[] = $bookingCarId;
    }
}
// оставляем только свободные автомобили
$freeCars = [];
foreach ($arCars as $carId => $arCar) {
    if (!in_array($carId, $bookedCarIds)) {
        $freeCars[] = $arCar;
    }
}
// собираем ID водителей и категорий
$driverIds = [];
$categoryIds = [];
foreach ($freeCars as $arCar) {
    if (!empty($arCar['PROPERTY_DRIVER_VALUE'])) {
        $driverIds[] = $arCar['PROPERTY_DRIVER_VALUE'];
    }
    if (!empty($arCar['PROPERTY_COMFORT_CATEGORY_VALUE'])) {
        $categoryIds[] = $arCar['PROPERTY_COMFORT_CATEGORY_VALUE'];
    }
}
// получаем имена водителей
$drivers = [];
if (!empty($driverIds)) {
    $rsDrivers = CIBlockElement::GetList(
        [],
        ['ID' => $driverIds],
        false,
        false,
        ['ID', 'NAME']
    );
    while ($arDriver = $rsDrivers->GetNext()) {
        $drivers[$arDriver['ID']] = $arDriver['NAME'];
    }
}
// получаем названия категорий
$categories = [];
if (!empty($categoryIds)) {
    $rsCategories = CIBlockElement::GetList(
        [],
        ['ID' => $categoryIds],
        false,
        false,
        ['ID', 'NAME']
    );
    while ($arCategory = $rsCategories->GetNext()) {
        $categories[$arCategory['ID']] = $arCategory['NAME'];
    }
}
// формируем итоговый массив для вывода
$arResultCars = [];
foreach ($freeCars as $arCar) {
    $driverId = $arCar['PROPERTY_DRIVER_VALUE'];
    $categoryId = $arCar['PROPERTY_COMFORT_CATEGORY_VALUE'];

    $arResultCars[] = [
        'ID' => $arCar['ID'],
        'MODEL' => $arCar['NAME'],
        'CATEGORY' => $categories[$categoryId] ?? '—',
        'DRIVER' => $drivers[$driverId] ?? '—',
    ];
}
$this->arResult = [
    'CARS' => $arResultCars,
    'DATE_START' => $dateStartRaw,
    'DATE_END' => $dateEndRaw,
];
