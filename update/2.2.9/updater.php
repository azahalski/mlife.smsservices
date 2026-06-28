<?
$moduleId = "mlife.smsservices";
if(\Bitrix\Main\Loader::includeModule($moduleId)) {
    $templates = \Mlife\Smsservices\EventlistTable::getList()->fetchAll();
	foreach($templates as $row){
		\Mlife\Smsservices\EventlistTable::update(['ID'=>$row['ID']], $row);
	}
}