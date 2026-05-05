<?php

use Bitrix\Main\Loader;

/**
 * компонент списка элементов инфоблоков
 *
 */
class IblockListComponent extends CBitrixComponent
{
    /**
     * основной метод выполнения компонента
     *
     * @return void
     */
    public function executeComponent(): void
    {
        // проверка подключения модулей
        if (!$this->checkModules()) {
            return;
        }
        // подготовка входных параметров
        $this->prepareParams();
        // загрузка элементов
        $this->loadElements();
        // подключение шаблона
        $this->includeComponentTemplate();
    }

    /**
     * проверка подключения необходимых модулей
     *
     * @return bool
     */
    private function checkModules(): bool
    {
        if (!Loader::includeModule('iblock')) {
            ShowError('Модуль iblock не подключен');
            return false;
        }

        return true;
    }

    /**
     * подготовка входных параметров
     *
     * @return void
     */
    private function prepareParams(): void
    {
        // тип инфоблока (строка)
        $this->arParams['IBLOCK_TYPE'] = trim($this->arParams['IBLOCK_TYPE'] ?? '');
        // ID инфоблока (число)
        $this->arParams['IBLOCK_ID'] = (int)($this->arParams['IBLOCK_ID'] ?? 0);
        // дополнительный фильтр (массив)
        $this->arParams['FILTER'] = $this->arParams['FILTER'] ?? [];
    }

    /**
     * получение элементов инфоблоков
     *
     * @return void
     */
    private function loadElements(): void
    {
        // базовый фильтр - только активные элементы
        $filter = [
            'ACTIVE' => 'Y'
        ];
        // если указан конкретный инфоблок
        if ($this->arParams['IBLOCK_ID'] > 0) {
            $filter['IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];
        } else {
            // если не указан тип — ошибка
            if (!$this->arParams['IBLOCK_TYPE']) {
                ShowError('Не указан тип инфоблока');
                return;
            }
            // фильтр по типу инфоблока
            $filter['IBLOCK_TYPE'] = $this->arParams['IBLOCK_TYPE'];
        }
        // дополнительный пользовательский фильтр
        if (!empty($this->arParams['FILTER'])) {
            $filter = array_merge($filter, $this->arParams['FILTER']);
        }
        // запрос элементов инфоблока
        $res = CIBlockElement::GetList(
            ['ID' => 'DESC'],
            $filter,
            false,
            false,
            ['ID', 'NAME', 'IBLOCK_ID']
        );
        // формируем группированный массив результата
        while ($item = $res->Fetch()) {
            $this->arResult['ITEMS'][$item['IBLOCK_ID']][] = $item;
        }
    }
}
