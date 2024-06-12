<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?><h1>Регистрация</h1>
 <?$APPLICATION->IncludeComponent(
	"local:user.register",
	"",
	Array(
		"AUTH" => "Y",
		"REQUIRED_FIELDS" => array("EMAIL"),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array("EMAIL"),
		"SUCCESS_PAGE" => "/index.php",
		"USER_PROPERTY" => array("UF_POINTS"),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>