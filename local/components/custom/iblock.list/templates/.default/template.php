<?php if (!empty($arResult['ITEMS'])): ?>
    <?php foreach ($arResult['ITEMS'] as $iblockId => $items): ?>
        <h3>Инфоблок <?= $iblockId ?></h3>
        <ul>
            <?php foreach ($items as $item): ?>
                <li>
                    <?= $item['NAME'] ?> (ID: <?= $item['ID'] ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
<?php else: ?>
    <p>Нет элементов</p>
<?php endif; ?>
