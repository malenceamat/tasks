<?$APPLICATION->IncludeComponent(
    "bitrix:news.detail",
    "custom_detail",
    array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
        "CACHE_TYPE" => "N",

    ),
    $component
);?>
