<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->IncludeComponent(
    "yadisk:crud",
    "",
    []
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
