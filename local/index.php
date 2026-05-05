<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->IncludeComponent(
    "custom:iblock.list",
    "",
    [
        "IBLOCK_TYPE" => "content",
        "IBLOCK_ID" => 0
    ]
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
