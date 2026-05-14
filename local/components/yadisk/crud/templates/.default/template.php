<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div style="padding:40px">
<h1>Yandex Disk</h1>
<h2>Добавить файл</h2>
<form method="POST" enctype="multipart/form-data">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="action" value="add">

    <input type="file" name="file" required>
    <button>Загрузить</button>
</form>
<hr>
<h2>Файлы</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Имя</th>
        <th>Удалить</th>
        <th>Заменить</th>
    </tr>
    <?php foreach ($arResult['FILES'] as $path): ?>
        <tr>
            <td><?= htmlspecialchars(basename($path)) ?></td>
            <td>
                <a href="?delete=<?= urlencode($path) ?>">
                    Удалить
                </a>
            </td>
            <td>
                <form method="POST" enctype="multipart/form-data">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="action" value="replace">
                    <input type="hidden" name="old_name" value="<?= htmlspecialchars($path) ?>">

                    <input type="file" name="replace_file" required>
                    <button>Заменить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
