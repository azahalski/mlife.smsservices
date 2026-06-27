<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$module_id = "mlife.smsservices";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if (! ($MODULE_RIGHT >= "R"))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
	
$APPLICATION->SetTitle(Loc::getMessage("MLIFESS_BALANCE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetAdditionalCSS("/bitrix/css/mlife.smsservices/style.css");

\Bitrix\Main\Loader::includeModule($module_id);
$smsServices = new \Mlife\Smsservices\Sender();
$arrBalance = $smsServices->getBalance();
?>
<?phpforeach($arrBalance as $key=>$val){?>
<?phpif($val){?>
<div class="balance">
<div class="titleTransport"><?=Loc::getMessage("MLIFESS_BALANCE_TRANSPORT_".ToUpper($key))?></div>
<?php
if($val->error) {
?>
<?=Loc::getMessage("MLIFESS_BALANCE_ERR_".$val->error_code)?> (<?=$val->error?>)
<?php}else{?>
<?=Loc::getMessage("MLIFESS_BALANCE_OST")?>: <strong><?=$val->balance?></strong>

<?php}?>
</div>
<?php}?>
<?php}?>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>