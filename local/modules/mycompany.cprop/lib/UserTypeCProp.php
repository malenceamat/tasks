<?php

namespace MyCompany\CProp;

class UserTypeCProp
{
    /**
     * возвращает описание пользовательского типа свойства.
     *
     * @return array
     */
    public static function GetUserTypeDescription(): array
    {
        return [
            "USER_TYPE_ID" => "my_cprop_uf",
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => "Комплексное свойство",
            "BASE_TYPE" => "string",

            "GetEditFormHTML" => [__CLASS__, "GetEditFormHTML"],
            "GetDBColumnType" => [__CLASS__, "GetDBColumnType"],
            "OnBeforeSave" => [__CLASS__, "OnBeforeSave"],
            "OnAfterFetch" => [__CLASS__, "OnAfterFetch"],
        ];
    }

    /**
     * Формирует HTML для отображения поля в форме редактирования.
     *
     * @param array $arUserField - метаданные UF-поля (ID, FIELD_NAME, VALUE и др.)
     * @param array $arHtmlControl - данные для HTML-контрола (NAME — атрибут name, VALUE — текущее значение)
     *
     * @return string
     */
    public static function GetEditFormHTML(array $arUserField, array $arHtmlControl): string
    {
        $name = htmlspecialcharsbx($arHtmlControl['NAME']);
        // значение по умолчанию, если поле ещё не заполнялось
        $value = [
            "TITLE" => "",
            "TEXT"  => ""
        ];
        // извлекаем сырое значение из параметров формы
        $rawValue = $arHtmlControl["VALUE"] ?? $arUserField["VALUE"] ?? null;
        // после OnAfterFetch данные могут быть обёрнуты в дополнительный ключ VALUE
        if (is_array($rawValue) && isset($rawValue['VALUE']) && is_array($rawValue['VALUE'])) {
            $rawValue = $rawValue['VALUE'];
        }
        if (is_array($rawValue) && isset($rawValue['TITLE'], $rawValue['TEXT'])) {
            // данные уже распакованы (пришли из OnAfterFetch как массив)
            $value = $rawValue;
        } elseif (is_string($rawValue) && !empty($rawValue)) {
            //данные пришли как JSON-строка (первое открытие формы или fallback)
            $decoded = json_decode($rawValue, true);
            if (is_array($decoded) && isset($decoded['TITLE'], $decoded['TEXT'])) {
                $value = $decoded;
            }
        }
        ob_start();
        ?>
        <div style="padding:10px;border:1px solid #ccc;">
            <b>Название:</b><br>
            <input type="text" name="<?= $name ?>[TITLE]" value="<?= htmlspecialcharsbx($value['TITLE']) ?>" style="width:100%;max-width:500px;">
            <br><br>
            <b>Описание:</b><br>
            <textarea name="<?= $name ?>[TEXT]" style="width:100%;height:200px;"><?= htmlspecialcharsbx($value['TEXT']) ?></textarea>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * вызывается перед сохранением значения в БД.
     *
     * @param array $arUserField - метаданные UF-поля
     * @param array $value - значение, пришедшее из формы.
     *
     * @return string
     */
    public static function OnBeforeSave(array $arUserField, array $value): string
    {
        if (empty($value)) {
            return '';
        }
        // если значение пришло не массивом — возвращаем как есть (защита от некорректных данных)
        if (!is_array($value)) {
            return (string)$value;
        }
        return json_encode([
            "TITLE" => $value["TITLE"] ?? "",
            "TEXT"  => $value["TEXT"] ?? ""
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * вызывается после извлечения значения из БД.
     * распаковывает JSON-строку в массив для дальнейшей работы.
     *
     * @param array $arUserField - метаданные UF-поля
     * @param array $value - значение из БД.
     *
     * @return array
     */
    public static function OnAfterFetch(array $arUserField, array $value): array
    {
        if (!empty($value['VALUE']) && is_string($value['VALUE'])) {
            $decoded = json_decode($value['VALUE'], true);
            if (is_array($decoded)) {
                $value['VALUE'] = $decoded;
            }
        }

        return $value;
    }

    /**
     * возвращает тип колонки в БД для хранения значения поля.
     *
     * @return string
     */
    public static function GetDBColumnType(): string
    {
        return "text";
    }
}
