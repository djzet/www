<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="user-points">
    <p>User ID: <?= $arResult['USER_ID'] ?></p>
    <p>Баланс: <?= $arResult['USER_POINTS'] ?></p>
    
    <form method="post">
        <div>
            <label>Начислить баллы:</label>
            <input type="number" name="points_to_add" min="1">
            <button type="submit">Начислить</button>
        </div>
    </form>
    
    <form method="post">
        <div>
            <label>Списать баллы:</label>
            <input type="number" name="points_to_deduct" min="1">
            <button type="submit">Списать</button>
        </div>
    </form>

    <h3>История транзакций</h3>
    <?php if (!empty($arResult['TRANSACTIONS'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Дата и время</th>
                    <th>Тип транзакции</th>
                    <th>Баллы</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arResult['TRANSACTIONS'] as $transaction): ?>
                    <tr>
                        <td><?= $transaction['TIMESTAMP_X'] ?></td>
                        <td><?= $transaction['PROPERTY_CHANGE_TYPE_VALUE'] == 'add' ? 'Начисление' : 'Списание' ?></td>
                        <td><?= $transaction['PROPERTY_POINTS_CHANGE_VALUE'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        $totalPages = ceil($arResult["TOTAL_TRANSACTIONS"] / $arResult["PAGE_SIZE"]);
        $currentPage = $arResult["PAGE"];
        ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">&laquo; Назад</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i == $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">Далее &raquo;</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Транзакций нет.</p>
    <?php endif; ?>
</div>
