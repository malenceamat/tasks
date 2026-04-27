<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->addExternalCss($templateFolder . "/style.css");
?>
<div class="article-list">
    <?php foreach ($arResult["ITEMS"] as $arItem): ?>
        <a class="article-item article-list__item" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
            <div class="article-item__background">
                <?php if (!empty($arItem["PREVIEW_PICTURE"]["SRC"])): ?>
                    <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= htmlspecialchars($arItem["NAME"]) ?>">
                <?php endif; ?>
            </div>
            <div class="article-item__wrapper">
                <div class="article-item__title">
                    <?= $arItem["NAME"] ?>
                </div>
                <div class="article-item__content">
                    <?= $arItem["PREVIEW_TEXT"] ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
