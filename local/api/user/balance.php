<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

// Получение ID пользователя из запроса
$request = Application::getInstance()->getContext()->getRequest();
$userId = intval($request->getQuery("userId"));

if (!$userId) {
    echo json_encode(['error' => 'Invalid userId']);
    exit;
}

if (!Loader::includeModule("main")) {
    echo json_encode(['error' => 'Failed to load module "main"']);
    exit;
}

$user = CUser::GetByID($userId)->Fetch();
if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$balance = $user['UF_POINTS']; // Здесь UF_POINTS - это пользовательское поле с балансом пользователя

echo json_encode(['userId' => $userId, 'balance' => $balance]);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");