<?php
namespace Mlife\Smsservices;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class EventlistTable extends Entity\DataManager
{
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
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\StringField('SITE_ID', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 10),
					);
				}
				)
			),
			new Entity\StringField('SENDER', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 50),
					);
				}
				)
			),
			new Entity\StringField('EVENT', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 50),
					);
				}
				)
			),
			new Entity\StringField('NAME', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			),
			new Entity\StringField('TEMPLATE', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 2500),
					);
				},
                'fetch_data_modification' => function ($value) {
                    // Если это не строка, то декодировать нечего — возвращаем как есть
                    if (!is_string($value) || trim($value) === '') {
                        return is_array($value) ? $value : [];
                    }
                    $trimmed = trim($value);

                    // 1. Проверяем на JSON
                    // Строка JSON должна начинаться на {, [, ", цифру или true/false/null
                    $firstChar = $trimmed[0] ?? '';
                    if (in_array($firstChar, ['{', '[', '"', 't', 'f', 'n'] ) || is_numeric($firstChar)) {
                        $jsonDecoded = json_decode($trimmed, true); // true вернет массив вместо объекта
                        if (json_last_error() === JSON_ERROR_NONE) {
                            return $jsonDecoded;
                        }
                    }
                    // 2. Проверяем на PHP Serialized
                    // Сериализованные данные обычно имеют формат a:0:{}, o:4:"Name":... или s:5:"Value";
                    if (preg_match('/^[aOisb]:\d+:/', $trimmed) || $trimmed === 'b:0;' || $trimmed === 'b:1;' || $trimmed === 'N;') {
                        // Использование @ подавляет Notice, если строка была похожа на serialized, но оказалась битой
                        $unserialized = @unserialize($trimmed, ['allowed_classes'=>false]);
                        if ($unserialized !== false || $trimmed === 'b:0;') {
                            return $unserialized;
                        }
                    }

                    // Если ни один формат не подошел, возвращаем исходную строку
                    return is_array($value) ? $value : [];
                }
				)
			),
			new Entity\StringField('PARAMS', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 6255),
					);
				}
				)
			),
			new Entity\StringField('ACTIVE', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
				)
			)
		);
	}
	
	
	public static function onAfterAdd(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('fields');
		\Mlife\Smsservices\EventlistTable::addEvent($params['EVENT']);
	}
	
	public static function onAfterUpdate(\Bitrix\Main\Entity\Event $event){
		$params = $event->getParameter('fields');
		\Mlife\Smsservices\EventlistTable::addEvent($params['EVENT']);
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
}