<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

// Получение ID пользователя из запроса
$request = Application::getInstance()->getContext()->getRequest();
$userId = intval($request->getQuery("userId"));

if (!$userId) {
    echo json_encode(['error' => 'Invalid userId']);
    exit;
}

if (!Loader::includeModule("iblock")) {
    echo json_encode(['error' => 'Failed to load module "iblock"']);
    exit;
}

// Получение транзакций пользователя из инфоблока
$arTransactions = [];
$arSelect = ["ID", "NAME", "PROPERTY_POINTS_CHANGE", "PROPERTY_CHANGE_TYPE", "TIMESTAMP_X"];
$arFilter = [
    "IBLOCK_ID" => 1,
    "PROPERTY_USER_ID" => $userId
];
$rsTransactions = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
while ($arTransaction = $rsTransactions->Fetch()) {
    $arTransactions[] = [
        "transactionId" => $arTransaction["ID"],
        "timestamp" => $arTransaction["TIMESTAMP_X"],
        "amount" => $arTransaction["PROPERTY_POINTS_CHANGE_VALUE"],
        "type" => $arTransaction["PROPERTY_CHANGE_TYPE_VALUE"]
    ];
}

echo json_encode($arTransactions);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>