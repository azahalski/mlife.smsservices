<?php
namespace Mlife\Smsservices;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config;
Loc::loadMessages(__FILE__);

class EventlistTable extends Entity\DataManager
{
    const HASH_ALGO = 'sha512';
    public static $prevHash = '';
	public static $oldId = null;
	
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_smsservices_eventlist';
	}
	
	public static function getMap()
	{
		return [
			new Entity\IntegerField('ID', [
				'primary' => true,
				'autocomplete' => true,
            ]),
			new Entity\StringField('SITE_ID', [
				'required' => true,
				'validation' => function(){
					return [
						new Entity\Validator\Length(null, 10),
                    ];
				}
            ]),
			new Entity\StringField('SENDER', [
				'required' => false,
				'validation' => function(){
					return [
						new Entity\Validator\Length(null, 50),
                    ];
				}
            ]),
			new Entity\StringField('EVENT', [
				'required' => true,
				'validation' => function(){
					return [
						new Entity\Validator\Length(null, 50),
                    ];
				}
            ]),
			new Entity\StringField('NAME', [
				'required' => true,
				'validation' => function(){
					return [
						new Entity\Validator\Length(null, 255),
                    ];
				}
            ]),
            new Entity\StringField('PARAMS', [
                    'required' => false,
                    'validation' => function(){
                        return [
                            new Entity\Validator\Length(null, 6255),
                        ];
                    },
                    'fetch_data_modification' => function(){
                    return [
                        function ($value) {
                            // Если это не строка, то декодировать нечего — возвращаем как есть
                            if (!is_string($value) || trim($value) === '') {
                                return is_array($value) ? $value : '';
                            }
                            $trimmed = trim($value);

                            // 1. Проверяем на JSON
                            // Строка JSON должна начинаться на {, [, ", цифру или true/false/null
                            $firstChar = $trimmed[0] ?? '';
                            if (in_array($firstChar, ['{', '[', '"', 't', 'f', 'n']) || is_numeric($firstChar)) {
                                return $trimmed;
                            }
                            // 2. Проверяем на PHP Serialized
                            // Сериализованные данные обычно имеют формат a:0:{}, o:4:"Name":... или s:5:"Value";
                            if (preg_match('/^[aOisb]:\d+:/', $trimmed) || $trimmed === 'b:0;' || $trimmed === 'b:1;' || $trimmed === 'N;') {
                                // Использование @ подавляет Notice, если строка была похожа на serialized, но оказалась битой
                                $unserialized = @unserialize($trimmed, ['allowed_classes' => false]);
                                return \Bitrix\Main\Web\Json::encode($unserialized);
                            }

                            // Если ни один формат не подошел, возвращаем исходную строку
                            return $value;
                        }
                    ];
                    }
            ]),
            new Entity\StringField('ACTIVE', [
                    'required' => false,
                    'validation' => function(){
                        return [
                            new Entity\Validator\Length(null, 1),
                        ];
                    }
            ]),
			new Entity\StringField('TEMPLATE', [
				'required' => false,
				'validation' => function(){
					return [
                        // 1. Стандартный валидатор длины строки
                        new Entity\Validator\Length(null, 2500),

                        // 2. Кастомный валидатор на PHP-код и символ #
                        new class extends Entity\Validator\Base {
                            public function validate($value, $primary, array $row, Entity\Field $field)
                            {
                                // Проверяем наличие символа #
                                if (stripos($value, '<?') !== false && stripos($value, '#') !== false) {
                                    return new \Bitrix\Main\Entity\FieldError(
                                        $field,
                                        'Символ "#" запрещен в тексте php шаблона. Используйте значение из $arParams["MACROS"] вместо #MACROS#',
                                        'HASH_SYMBOL_DENIED'
                                    );
                                }

                                return true;
                            }
                        }
                    ];
				},
                //пишем хеши в .settings модуля, чтобы исключить подмену шаблона в базе через sql injection
                'save_data_modification' => function(){
                    return [ function ($value) {

                        if(stripos($value, '<?') === false) return $value;

                        $confOb = Config\Configuration::getInstance('mlife.smsservices');
                        $existingSettings = $confOb->get('template_hashes');

                        $hash = self::getHash($value);

                        //установка новых значений
                        if (!is_array($existingSettings)) $existingSettings = [];

                        $existingSettingsOld = $existingSettings;
                        if(self::$prevHash){
                            $existingSettings = [];
                            foreach($existingSettingsOld as $v){
                                if($v == self::$prevHash) continue;
                                $existingSettings[] = $v;
                            }
                        }

                        if(!in_array($hash, $existingSettings)) {
                            $existingSettings[] = $hash;
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

                        return $value;
                    }];
                },
            ])
        ];
	}
	
	public static function onAfterAdd(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('fields');
		\Mlife\Smsservices\EventlistTable::addEvent($params['EVENT']);
	}
	
	public static function onAfterUpdate(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('fields');
		\Mlife\Smsservices\EventlistTable::addEvent($params['EVENT']);
	}

    public static function onBeforeUpdate(\Bitrix\Main\Entity\Event $event){
        $fields = $event->getParameter('fields');
        self::$prevHash = '';
        if($fields['TEMPLATE']){
            self::$prevHash = self::getHash($fields['TEMPLATE']);
        }
    }

	public static function onBeforeDelete(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('id');
		if($params['ID']){
			$ar = \Mlife\Smsservices\EventlistTable::getRowById($params['ID']);
			self::$oldId = $ar['EVENT'];
		}
	}
	
	public static function onAfterDelete(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('id');
		if($params['ID']){
			$ar = \Mlife\Smsservices\EventlistTable::getList(array(
				'select' => array('ID'),
				'filter' => array('EVENT'=>self::$oldId),
				'limit' => 1
			));
			if(!$ar->fetch()) \Mlife\Smsservices\EventlistTable::removeEvent(self::$oldId);
		}
		self::$oldId = null;
	}
	
	public static function addEvent($eventCode) {
		
		$events = \Mlife\Smsservices\Events::getList();
		
		if(is_array($events[$eventCode])) {
			
			$ev = $events[$eventCode];
			
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			foreach($ev['BX_EVENT'] as $val){
				if($val[2] !== null){
					$new = ($val[5] == 'new') ? true : false;
					if($new) {
						$eventManager->registerEventHandler($val[0], $val[1], $val[2], $val[3], $val[4]);
					}else{
						$eventManager->registerEventHandlerCompatible($val[0], $val[1], $val[2], $val[3], $val[4]);
					}
				}
			}
			
		}
		
	}
	
	public static function removeEvent($eventCode) {
		
		$events = \Mlife\Smsservices\Events::getList();
		
		$ev = $events[$eventCode];
		
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		
		foreach($ev['BX_EVENT'] as $val){
			if($val[2] !== null){
				UnRegisterModuleDependences($val[0], $val[1], $val[2], $val[3], $val[4]);
			}
		}
		
	}
	
	public static function removeAllEvent() {
		$events = \Mlife\Smsservices\Events::getList();
		
		foreach($events as $evCode=>$ev){
			foreach($ev['BX_EVENT'] as $val){
				if($val[2] !== null){
					UnRegisterModuleDependences($val[0], $val[1], $val[2], $val[3], $val[4]);
				}
			}
		}
	}

    private static function getDefaultKey(): string
    {
        static $defaultKey = null;
        if ($defaultKey === null)
        {
            $defaultKey = Config\Option::get('main', 'signer_default_key', false);
            if (!$defaultKey)
            {
                $defaultKey = hash('sha512', \Bitrix\Main\Security\Random::getString(64));
                Config\Option::set('main', 'signer_default_key', $defaultKey);
            }

            $options = Config\Configuration::getValue("crypto");
            if(isset($options["crypto_key"]))
            {
                $defaultKey .= $options["crypto_key"];
            }
        }

        return $defaultKey;
    }

    private static function getSecret(): string
    {
        $settings = Config\Configuration::getInstance('awz.bxapi');
        if($settings){
            $secret = $settings->get('app_rest_auth_secret');
        }
        if(!$secret) $secret = self::getDefaultKey();
        return $secret;
    }

    public static function getHash(string $msg = ''){
        $key = self::getSecret();
        $algorithm = self::HASH_ALGO;
        return hash_hmac($algorithm, $msg, $key, false);
    }
}