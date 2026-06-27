<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "mlife.smsservices";

\Bitrix\Main\Loader::includeModule($module_id);
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetAdditionalCSS("/bitrix/css/".$module_id."/style.css");
	
class MlifeRowListAdmin extends \Mlife\Smsservices\Main {
	
	public function __construct($params) {
		parent::__construct($params);
	}
	
	public function getMlifeRowListAdminCustomRow($row){
		
		$row->AddViewField("SENDER", ($row->arRes['SENDER']) ? htmlspecialcharsEx($row->arRes['SENDER']) : \Bitrix\Main\Config\Option::get("mlife.smsservices","sender","",""));
		$row->AddViewField("TEMPLATE", '<pre style="font-size:10px;line-height:12px;padding:0;margin:0;">'.htmlspecialcharsEx($row->arRes['TEMPLATE']).'</pre>');
		$row->AddCheckField("ACTIVE");
		
		$params = $row->arRes['PARAMS'];
		$html = '';
		foreach($params as $name=>$val){
			$html .= htmlspecialcharsEx($name).': '.htmlspecialcharsEx($val).';<br/>';
		}
		$row->AddViewField("PARAMS", '<pre style="font-size:10px;line-height:12px;padding:0;margin:0;">'.$html.'</pre>');
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddInputField("SENDER", array("size"=>20));
		
		
		$sHTML = '<textarea rows="7" cols="50" name="FIELDS['.$row->arRes['ID'].'][TEMPLATE]">'.htmlspecialcharsBack($row->arRes['TEMPLATE']).'</textarea>';
		$row->AddEditField("TEMPLATE", $sHTML);
		
	}
	
}

$arParams = array(
	"PRIMARY" => "ID",
	"ENTITY" => "\\Mlife\\Smsservices\\EventlistTable",
	"FILE_EDIT" => 'mlife_smsservices_eventlist_edit.php',
	"BUTTON_CONTECST" => array(),
	"ADD_GROUP_ACTION" => array("delete","edit"),
	"COLS" => true,
	"FIND" => array(
		"NAME","EVENT","SITE_ID"
	),
	"LIST" => array("ACTIONS" => array("delete","edit")),
	"CALLBACK_ACTIONS" => array()
);

$adminCustom = new MlifeRowListAdmin($arParams);
$adminCustom->defaultInterface();