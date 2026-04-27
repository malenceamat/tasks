<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->addExternalCss($templateFolder . "/style.css");
$sectionName = "";
if (!empty($arResult["IBLOCK_SECTION_ID"])) {
    $rs = CIBlockSection::GetByID($arResult["IBLOCK_SECTION_ID"]);
    if ($sec = $rs->Fetch()) {
        $sectionName = $sec["NAME"];
    }
}
?>
<div class="article-card">
    <?php if ($sectionName): ?>
        <div class="article-card__section">
            Раздел: <?= $sectionName ?>
        </div>
    <?php endif; ?>
    <div class="article-card__title">
        <?= $arResult["NAME"] ?>
    </div>
    <div class="article-card__date">
        <?= $arResult["DISPLAY_ACTIVE_FROM"] ?>
    </div>
    <div class="article-card__content">
        <?php if (!empty($arResult["DETAIL_PICTURE"]["SRC"])): ?>
            <img src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>">
        <?php endif; ?>
        <div class="article-card__text">
            <?= $arResult["DETAIL_TEXT"] ?>
        </div>
        <a href="/news/">Назад к новостям</a>
    </div>
</div>
