<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";

use Bitrix\Main\Application;
use Arhitector\Yandex\Disk;

/**
 * Компонент CRUD для работы с Яндекс.Диском
 *
 * Реализует:
 * - загрузку файлов
 * - удаление файлов
 * - замену файлов
 * - получение списка файлов
 *
 */
class YadiskCrudComponent extends CBitrixComponent
{
    private Disk $disk;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $token = '';
        $this->disk = new Disk($token);
    }

    /**
     * основной метод выполнения компонента
     *
     * @return void
     */
    public function executeComponent(): void
    {
        $this->handleRequest();
        $this->prepareData();

        $this->includeComponentTemplate();
    }

    /**
     * обработка действий
     *
     * @return void
     */
    private function handleRequest(): void
    {
        $request = Application::getInstance()->getContext()->getRequest();
        // добавление файла
        if ($request->isPost() && $request->getPost("action") === "add") {
            $file = $request->getFile("file");

            $this->disk
                ->getResource($file['name'])
                ->upload($file['tmp_name'], true);

            LocalRedirect($this->getCurPage());
        }
        // удаление файла
        if ($request->get("delete")) {
            $this->disk
                ->getResource($request->get("delete"))
                ->delete();

            LocalRedirect($this->getCurPage());
        }
        // замена файла
        if ($request->isPost() && $request->getPost("action") === "replace") {
            $file = $request->getFile("replace_file");
            $old = $request->getPost("old_name");
            $this->disk->getResource($old)->upload($file['tmp_name'], true);
            LocalRedirect($this->getCurPage());
        }
    }

    /**
     * подготовка данных для шаблона
     *
     * получает список файлов с Яндекс.Диска и передаёт их в $arResult
     *
     * @return void
     */
    private function prepareData(): void
    {
        $files = [];

        foreach ($this->disk->getResources(100, 0) as $item) {
            if (is_object($item) && method_exists($item, 'getPath')) {
                $files[] = $item->getPath();
            }
        }

        $this->arResult['FILES'] = $files;
    }

    /**
     * получение текущего URL страницы
     *
     * @return string
     */
    private function getCurPage(): string
    {
        global $APPLICATION;
        return $APPLICATION->GetCurPage();
    }
}
