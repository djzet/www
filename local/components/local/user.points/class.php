<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Uri;
use Bitrix\Iblock\ElementTable;

class CustomUserPointsComponent extends CBitrixComponent
{
    protected $iblockId = 1;
    public function onPrepareComponentParams($arParams)
    {
        $arParams["PAGE_SIZE"] = isset($arParams["PAGE_SIZE"]) ? intval($arParams["PAGE_SIZE"]) : 10;
        return $arParams;
    }

    public function executeComponent()
    {
        global $USER;
        if (!$USER->IsAuthorized()) {
            ShowError("Авторизируйтесь для просмотра данной страници");
            return;
        }

        $userId = $USER->GetID();
        $userFields = CUser::GetByID($userId)->Fetch();
        $this->arResult['USER_ID'] = $userId;
        $this->arResult['USER_POINTS'] = $userFields['UF_POINTS'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($userId);
        }

        $this->arResult['TRANSACTIONS'] = $this->getUserTransactions($userId);

        $this->includeComponentTemplate();
    }

    protected function handlePost($userId)
    {
        global $APPLICATION;
        $request = Application::getInstance()->getContext()->getRequest();
        $pointsToAdd = intval($request->getPost("points_to_add"));
        $pointsToDeduct = intval($request->getPost("points_to_deduct"));

        if ($pointsToAdd > 0) {
            $this->addPoints($userId, $pointsToAdd);
        }

        if ($pointsToDeduct > 0) {
            $this->deductPoints($userId, $pointsToDeduct);
        }

        // Редирект на ту же страницу
        $uri = new Uri($APPLICATION->GetCurPage());
        LocalRedirect($uri->getUri());
    }

    protected function addPoints($userId, $points)
    {
        global $USER;

        $userFields = CUser::GetByID($userId)->Fetch();
        $currentPoints = $userFields['UF_POINTS'];
        $newPoints = $currentPoints + $points;

        $USER->Update($userId, ['UF_POINTS' => $newPoints]);
        $this->logTransaction($userId, $points, 'add');
    }

    protected function deductPoints($userId, $points)
    {
        global $USER;

        $userFields = CUser::GetByID($userId)->Fetch();
        $currentPoints = $userFields['UF_POINTS'];

        if ($points > $currentPoints) {
            ShowError("Insufficient points");
            return;
        }

        $newPoints = $currentPoints - $points;
        $USER->Update($userId, ['UF_POINTS' => $newPoints]);
        $this->logTransaction($userId, $points, 'deduct');
    }

    protected function logTransaction($userId, $points, $type)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            throw new \Exception("Failed to load module 'iblock'");
        }

        $el = new \CIBlockElement;

        $arFields = [
            "IBLOCK_ID" => $this->iblockId,
            "NAME" => "Транзакция для пользователя $userId",
            "PROPERTY_VALUES" => [
                "USER_ID" => $userId,
                "POINTS_CHANGE" => $points,
                "CHANGE_TYPE" => $type,
            ]
        ];
        $el->Add($arFields);
    }

    protected function getUserTransactions($userId)
    {
        if (!Loader::includeModule('iblock')) {
            throw new \Exception("Failed to load module 'iblock'");
        }

        $transactions = [];
        $filter = ["IBLOCK_ID" => $this->iblockId, "PROPERTY_USER_ID" => $userId];
        $select = ["ID", "TIMESTAMP_X", "PROPERTY_POINTS_CHANGE", "PROPERTY_CHANGE_TYPE"];
        $order = ["TIMESTAMP_X" => "DESC"];
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $pageSize = $this->arParams["PAGE_SIZE"];

        $result = CIBlockElement::GetList($order, $filter, false, ["nPageSize" => $pageSize, "iNumPage" => $page], $select);
        while ($row = $result->Fetch()) {
            $transactions[] = $row;
        }

        $this->arResult["TOTAL_TRANSACTIONS"] = $result->NavRecordCount;
        $this->arResult["PAGE"] = $page;
        $this->arResult["PAGE_SIZE"] = $pageSize;

        return $transactions;
    }
}
?>