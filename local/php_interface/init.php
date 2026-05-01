<?php
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

EventManager::getInstance()->addEventHandler(
    "iblock",
    "OnAfterIBlockElementAdd",
    ["MyLogger", "log"]
);

EventManager::getInstance()->addEventHandler(
    "iblock",
    "OnAfterIBlockElementUpdate",
    ["MyLogger", "log"]
);
class MyLogger
{
    public static function log(&$arFields)
    {
        if (empty($arFields['ID'])) {
            return;
        }
        Loader::includeModule('iblock');
        // убираем логирование самого лога
        $logIblock = CIBlock::GetList([], ['CODE' => 'LOG'])->Fetch();
        if (!$logIblock) return;

        if ($arFields['IBLOCK_ID'] == $logIblock['ID']) {
            return;
        }

        // инфоблок
        $iblock = CIBlock::GetByID($arFields['IBLOCK_ID'])->Fetch();
        // элемент
        $element = CIBlockElement::GetByID($arFields['ID'])->Fetch();
        // разделы (рекурсия)
        $sectionPath = self::getSectionPath($element['IBLOCK_SECTION_ID']);
        // создаем\находим раздел
        $sectionId = self::getOrCreateSection($logIblock['ID'], $iblock['CODE'], $iblock['NAME']);
        // создаем лог
        $el = new CIBlockElement();
        $text = $iblock['NAME'] . ' -> ' . $sectionPath . ' -> ' . $element['NAME'];

        $el->Add([
            "IBLOCK_ID" => $logIblock['ID'],
            "IBLOCK_SECTION_ID" => $sectionId,
            "NAME" => $arFields['ID'],
            "ACTIVE_FROM" => date("d.m.Y H:i:s"),
            "PREVIEW_TEXT" => $text,
        ]);
    }

    /**
     * рекурсия разделов
     *
     * @param $sectionId
     * @return string
     */
    private static function getSectionPath($sectionId)
    {
        $names = [];
        while ($sectionId) {
            $section = CIBlockSection::GetByID($sectionId)->Fetch();
            if (!$section) break;

            array_unshift($names, $section['NAME']);
            $sectionId = $section['IBLOCK_SECTION_ID'];
        }

        return implode(' -> ', $names);
    }

    /**
     * создание разделов
     *
     * @param $iblockId
     * @param $code
     * @param $name
     * @return false|int|mixed
     */
    private static function getOrCreateSection($iblockId, $code, $name)
    {
        $res = CIBlockSection::GetList([], [
            'IBLOCK_ID' => $iblockId,
            'CODE' => $code
        ]);
        if ($section = $res->Fetch()) {
            return $section['ID'];
        }
        $bs = new CIBlockSection();

        return $bs->Add([
            'IBLOCK_ID' => $iblockId,
            'NAME' => $name,
            'CODE' => $code,
            'IBLOCK_SECTION_ID' => 0
        ]);
    }
}

/**
 * крон по удалению логов
 *
 * @return string
 * @throws \Bitrix\Main\LoaderException
 */
function clearOldLogsAgent()
{
    Loader::includeModule('iblock');
    $iblock = CIBlock::GetList([], ['CODE' => 'LOG'])->Fetch();
    $res = CIBlockElement::GetList(
        ["ID" => "DESC"],
        ["IBLOCK_ID" => $iblock['ID']],
        false,
        false,
        ["ID"]
    );
    $count = 0;

    while ($el = $res->Fetch()) {
        $count++;
        if ($count > 10) {
            CIBlockElement::Delete($el['ID']);
        }
    }

    return "clearOldLogsAgent();";
}
