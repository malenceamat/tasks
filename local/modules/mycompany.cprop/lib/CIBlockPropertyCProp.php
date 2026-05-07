<?php

namespace MyCompany\CProp;

use Bitrix\Main\Loader;
use CFileMan;

/**
 * UserType кастомного свойства инфоблока.
 *
 */
class CIBlockPropertyCProp
{
    /**
     * описание пользовательского типа свойства
     *
     * @return array
     */
    public static function GetUserTypeDescription(): array
    {
        return [
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "my_cprop",
            "DESCRIPTION" => "Моё комплексное свойство",

            "GetPropertyFieldHtml" => [__CLASS__, "GetPropertyFieldHtml"],
            "ConvertToDB" => [__CLASS__, "ConvertToDB"],
            "ConvertFromDB" => [__CLASS__, "ConvertFromDB"],
        ];
    }

    /**
     * формирование HTML поля в админке элемента инфоблока
     *
     * @param array $arProperty - описание свойства
     * @param array $value - текущее значение
     * @return string
     */
    public static function GetPropertyFieldHtml(array $arProperty, array $value): string
    {
        // подключаем модуль визуального редактора Bitrix
        Loader::includeModule("fileman");
        // значение по умолчанию
        $data = [
            "TITLE" => "",
            "TEXT" => ""
        ];
        // если есть сохранённое значение — декодируем JSON
        if (!empty($value["VALUE"])) {
            $decoded = json_decode($value["VALUE"], true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
        $id = $arProperty["ID"];
        ob_start();
        ?>
        <div style="padding:10px;border:1px solid #ccc;">
            <b>Название:</b><br>
            <input type="text" name="PROP[<?= $id ?>][0][VALUE]" value="<?= htmlspecialcharsbx($data["TITLE"]) ?>" style="width:100%">
            <br>
            <b>HTML описание:</b><br>
            <?php
            CFileMan::AddHTMLEditorFrame(
                "PROP{$id}VALUE_TEXT",
                $data["TEXT"],
                "TEXT",
                "html",
                [
                    "height" => 300,
                    "width" => "100%"
                ]
            );
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * подготовка данных перед сохранением в БД
     *
     * @param array $arProperty
     * @param array $arValue
     * @return array
     */
    public static function ConvertToDB(array $arProperty, array $arValue)
    {
        $id = $arProperty["ID"];
        $title = $_POST["PROP"][$id][0]["VALUE"] ?? '';
        $text  = $_POST["PROP{$id}VALUE_TEXT"] ?? '';
        // сохраняем как json строку
        $arValue["VALUE"] = json_encode([
            "TITLE" => $title,
            "TEXT" => $text
        ], JSON_UNESCAPED_UNICODE);

        return $arValue;
    }

    /**
     * обратное преобразование из БД
     *
     * @param array $arValue
     * @return array
     */
    public static function ConvertFromDB(array $arValue): array
    {
        return $arValue;
    }
}
