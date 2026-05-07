<?php

use Bitrix\Main\ModuleManager;

class mycompany_cprop extends CModule
{
    public $MODULE_ID = "mycompany.cprop";
    public $MODULE_NAME = "Комплексное свойство";
    public $MODULE_DESCRIPTION = "Комплексное свойство инфоблока";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;

    function __construct()
    {
        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        RegisterModuleDependences(
            "iblock",
            "OnIBlockPropertyBuildList",
            $this->MODULE_ID,
            "MyCompany\\CProp\\CIBlockPropertyCProp",
            "GetUserTypeDescription"
        );
    }

    public function DoUninstall()
    {
        UnRegisterModuleDependences(
            "iblock",
            "OnIBlockPropertyBuildList",
            $this->MODULE_ID,
            "MyCompany\\CProp\\CIBlockPropertyCProp",
            "GetUserTypeDescription"
        );

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
