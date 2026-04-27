<?$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "custom_news",
    array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "NEWS_COUNT" => 20,
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "CACHE_TYPE" => "N",
    ),
    $component
);?>
