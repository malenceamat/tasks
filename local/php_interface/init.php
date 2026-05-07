<?php
use Bitrix\Main\Loader;
use MyCompany\CProp\UserTypeCProp;

Loader::includeModule("mycompany.cprop");
AddEventHandler("main", "OnUserTypeBuildList", function () {
    return [
        "USER_TYPE_ID" => "my_cprop_uf",
        "CLASS_NAME" => \MyCompany\CProp\UserTypeCProp::class,
        "DESCRIPTION" => "My complex UF property",
        "BASE_TYPE" => "string",
    ];
});
