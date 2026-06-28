<?
use Bitrix\Main\Config\Configuration;
$moduleId = "mlife.smsservices";
if(\Bitrix\Main\Loader::includeModule($moduleId)) {
    $templates = \Mlife\Smsservices\EventlistTable::getList()->fetchAll();
	$confOb = Configuration::getInstance('mlife.smsservices');
    $existingSettings = [];
	foreach($templates as $row){
		if(stripos($row['TEMPLATE'], '<?') === false) continue;
		$hash = md5($row['TEMPLATE']);
		if(!in_array($hash, $existingSettings)) {
			$existingSettings[] = $hash;
		}
	}
	$confOb->add('template_hashes', $existingSettings);
	//запись значений
	$moduleConfigPath = getLocalPath("modules/mlife.smsservices/.settings.php");

	if ($moduleConfigPath) {
		$path = preg_replace(
			"'[\\\\/]+'",
			"/",
			\Bitrix\Main\Loader::getDocumentRoot() . $moduleConfigPath
		);

		if (file_exists($path)) {
			$dataTmp = include($path);
			$data = is_array($dataTmp) ? $dataTmp : [];
			$data['template_hashes'] = ['value' => $confOb->get('template_hashes'), 'readonly' => 0];
			$data = var_export($data, true);
			if (!is_writable($path))
				@chmod($path, 0644);
			file_put_contents($path, "<" . "?php\nreturn " . $data . ";\n");
		}
	}
}