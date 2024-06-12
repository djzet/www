<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?><h1>Авторизация</h1>
 <?$APPLICATION->IncludeComponent(
	"local:user.login",
	"",
	Array(
		"AUTH_FORGOT_PASSWORD_URL" => "",
		"AUTH_REGISTER_URL" => "/auth/register.php",
		"AUTH_SUCCESS_URL" => "/"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>