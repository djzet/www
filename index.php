<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("1С-Битрикс: Управление сайтом");
?>

<?php
global $USER;

if ($USER->IsAuthorized()) {
    echo "<a href=/?logout=yes&" . bitrix_sessid_get() . ">Выйти</a>" . "<br>";
} else {
    echo "Вы не авторизованы <a href='/auth/login.php'>Авторизация</a> <a href='/auth/register.php'>Регистрация</a>";
}
?>
<?php
$APPLICATION->IncludeComponent(
    "local:user.points",
    "",
    array(),
    false
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>