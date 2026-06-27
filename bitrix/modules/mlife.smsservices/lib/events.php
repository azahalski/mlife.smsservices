<?php
namespace Mlife\Smsservices;


use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Events {
	
	public static $cache;
	
	public static function getList(){
		
		$events = array(
			"MSMS_NEWORDER" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','newOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','newOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_NEWORDER")
			),
			"MSMS_STATUSUPDATE" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','statusOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','statusOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_STATUSUPDATE")
			),
			"MSMS_PAYED" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','payedOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','payedOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_PAYED")
			),/*
			"MSMS_RESET_PASSWORD" => array(
				"BX_EVENT" => false,
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','resetPasswordHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','newOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_RESET_PASSWORD")
			),*/
			/*"MSMS_BXEVENT" => array(
				"BX_EVENT" => array(
					array('main','OnBeforeEventSend','mlife.smsservices','\Mlife\Smsservices\Events','OnBeforeEventSend','old'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','eventSendHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','eventSendSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT")
			)*/
		);
		
		$event = new \Bitrix\Main\Event("mlife.smsservices", "OnAfterEventsAdd",array("EVENTS"=>$events));
		$event->send();
		   if ($event->getResults()){
			  foreach($event->getResults() as $evenResult){
				 if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
				 $params = $evenResult->getParameters();
				 if(is_array($params['EVENTS'])) {
					foreach($params['EVENTS'] as $key=>$ev){
						$events[$key] = $ev;
					}
				}
			  }
		   }
		}
		
		$allType = \Bitrix\Main\Mail\Internal\EventTypeTable::getList(array(
			'select' => array('NAME','EVENT_NAME'),
			'filter' => array('LID'=>LANG)
		));
		$arAllType = array();
		while($dt = $allType->fetch()){
			$events["MSMS_BXEVENT_".$dt['EVENT_NAME']] = array(
				"BX_EVENT" => array(
					array('main','OnBeforeEventSend','mlife.smsservices','\Mlife\Smsservices\Events','OnBeforeEventSend','old'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','eventSendHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','eventSendSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT").' - '.$dt['NAME']
			);
		}
		
		return $events;
		
	}
	
	public static function OnSaleOrderEntitySaved(\Bitrix\Main\Event $event){
		
		$order = $event->getParameter("ENTITY");
		
		if($order){
		
			$orderId = $order->getId();
			
			$oldValues = $event->getParameter("VALUES");
			$isNew = $order->isNew();
			
			$arOrderFields = \Bitrix\Sale\Internals\OrderTable::getList(
				array(
					'select' => array(
						"ID",
						"DATE_INSERT_FORMAT",
						"DATE_INSERT",
						"LID",
						"ACCOUNT_NUMBER",
						"TRACKING_NUMBER",
						"PAY_SYSTEM_ID",
						"DELIVERY_ID",
						"PERSON_TYPE_ID",
						"USER_ID",
						"PAYED",
						"STATUS_ID",
						"PRICE_DELIVERY",
						"ALLOW_DELIVERY",
						/*"PRICE_PAYMENT",*/
						"PRICE",
						"CURRENCY",
						"DISCOUNT_VALUE",
						"TAX_VALUE",
						"SUM_PAID",
						"USER_DESCRIPTION",
						"AFFILIATE_ID",
						//"BASKET_PRICE_TOTAL",
						"STATUS_NAME"=>"STATUS.NAME",
						"USER_EMAIL"=>"USER.EMAIL",
						"USER_NAME"=>"USER.NAME",
						"USER_PERSONAL_PHONE"=>"USER.PERSONAL_PHONE",
						"USER_PERSONAL_MOBILE"=>"USER.PERSONAL_MOBILE",
						"USER_PERSONAL_CITY"=>"USER.PERSONAL_CITY",
						"USER_WORK_PHONE"=>"USER.WORK_PHONE",
						"USER_PERSONAL_GENDER"=>"USER.PERSONAL_GENDER"
						//"*"
						),
					'filter' => array("ID"=>$orderId)
				)
			)->fetch();
			$arOrderFields['DATE_INSERT'] = $arOrderFields['DATE_INSERT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY")));
			
			$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"));
			$arMakros = array();
			
			foreach($arOrderFields as $prop_code=>$val){
				$arMakros['#'.$prop_code.'#'] = $val;
			}
			
			while($arProp = $dbProperty->Fetch()) {
				$arMakros['#PROPERTY_'.$arProp['CODE'].'#'] = '';
			}
			
			$dbOrderProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array(
				'select'=> array("*"), 
				'filter' => array("ORDER_ID"=>$orderId)
			)
			);
					
			while($arOrderProps = $dbOrderProps->fetch()) {
				$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
			}
			
			if ($propertyCollection = $order->getPropertyCollection())
			{
				$propVal = $propertyCollection->getArray();
				foreach($propVal['properties'] as $v){
					$arMakros['#PROPERTY_'.$v['CODE'].'#'] = $v['VALUE'][0];
				}
			}
			
			$arDelivery =  array();
			if($arOrderFields['DELIVERY_ID']) $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($arOrderFields['DELIVERY_ID']); //NAME
			if(is_array($arDelivery) && isset($arDelivery["NAME"])){
				$delivery = $arDelivery["NAME"];
			}else{
				$delivery = "";
			}
			
			$arPayment = array();
			if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemActionTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
			if(is_array($arPayment) && isset($arPayment["NAME"])){
				$payment = $arPayment["NAME"];
			}else{
				if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
				if(is_array($arPayment) && isset($arPayment["NAME"])){
					$payment = $arPayment["NAME"];
				}else{
					$payment = "";
				}
			}
			
			$arMakros['#ORDER_SUM#'] = $arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY'];
			$arMakros['#DELIVERY_NAME#'] = $delivery;
			$arMakros['#PAYMENT_NAME#'] = $payment;
			
			if(\Bitrix\Main\Loader::includeModule('currency') && \Bitrix\Main\Loader::includeModule('catalog')){
				$arMakros['#ORDER_SUM_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#ORDER_SUM#'],$arOrderFields['CURRENCY']);
				$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
				$arMakros['#PRICE_DELIVERY_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_DELIVERY#'],$arOrderFields['CURRENCY']);
				//$arMakros['#PRICE_PAYMENT_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_PAYMENT#'],$arOrderFields['CURRENCY']);
				$arMakros['#PRICE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE#'],$arOrderFields['CURRENCY']);
				$arMakros['#DISCOUNT_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#DISCOUNT_VALUE#'],$arOrderFields['CURRENCY']);
				$arMakros['#TAX_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#TAX_VALUE#'],$arOrderFields['CURRENCY']);
				$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
			}
			$arMakros['#EVENT_NAME#'] = 'MSMS_ORDER_'.$arOrderFields['ID'];
			
			if($isNew){
				
				//MSMS_NEWORDER новый заказ 
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_NEWORDER',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					if($arData['PARAMS']['PHONE']){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_NEWORDER");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
				}
				
			}
			
			//MSMS_STATUSUPDATE смена статуса заказа
			if($oldValues['STATUS_ID'] && ($oldValues['STATUS_ID'] != $arOrderFields['STATUS_ID'])){
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_STATUSUPDATE',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					
					$right = false;
					
					if($arData['PARAMS']['STATUS_FROM'] == 'ALL') {
						$right = true;
					}else{
						if($arData['PARAMS']['STATUS_FROM'] == $oldValues['STATUS_ID']) $right = true;
					}
					if($right){
						$right = false;
						if($arData['PARAMS']['STATUS_TO'] == 'ALL') {
							$right = true;
						}else{
							if($arData['PARAMS']['STATUS_TO'] == $arOrderFields['STATUS_ID']) $right = true;
						}
					}
					
					if($arData['PARAMS']['PHONE'] && $right){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_STATUSUPDATE");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
					
				}
			}
			
			//MSMS_PAYED - оплата заказа
			if($oldValues['PAYED'] && ($oldValues['PAYED'] != $arOrderFields['PAYED'])){
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_PAYED',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					
					$right = false;
					if($arOrderFields['PAYED'] == $arData['PARAMS']['PAYED']) $right = true;
					
					if($arData['PARAMS']['PHONE'] && $right){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_PAYED");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
					
				}
			}
			
			
		}
		
	}

    public static function isPhpCodeSafe(string $code): bool {

        /* отключать на свой страх и риск */
        /* несмотря на то, что у юзера с правами edit_php есть возможность поменять любой код,
        тесты уязвимостей дуреют и повышают приоритет RCE до high,
        даже если атакующий будет дополнительно использовать sql injection в других модулях (который наверняка будет заблокирован проактивной защитой),
        то быстрее повысить привилегии пользователя, залезть в b_agent и т.д. чем править шаблон
        */
        //return false;

        // 1. Конструкции, запрещенные ВСЕГДА
        $hardBlacklist = ['eval', 'assert', 'include', 'include_once', 'require', 'require_once', 'constant', 'goto'];

        // 2. Опасные системные функции (прямой вызов)
        $dangerousFunctions = ['exec', 'system', 'passthru', 'shell_exec', 'proc_open', 'popen', 'pcntl_exec',
            'file_put_contents', 'file_get_contents', 'unlink', 'mkdir', 'mail', 'extract', 'create_function', 'base64_decode', 'base64_encode',
            'fopen', 'fwrite', 'fread', 'fgets', 'rename', 'copy', 'rmdir', 'chmod', 'chown', 'touch',
            'link', 'symlink', 'ini_set', 'ini_get', 'set_time_limit', 'header', 'setcookie', 'parse_ini_file', 'error_reporting',
            'move_uploaded_file', 'tmpfile',
            'readfile', 'scandir', 'phpinfo', 'getenv', 'get_defined_constants', 'get_declared_classes', 'get_defined_vars',
            'get_loaded_extensions', 'parse_str', 'show_source', 'highlight_file', 'opendir', 'readdir', 'unserialize'
        ];

        // 3. Полный список функций PHP, принимающих callable-аргументы
        $callbackFunctions = [
            'array_map', 'array_walk', 'array_walk_recursive', 'array_filter', 'array_reduce',
            'usort', 'uasort', 'uksort', 'array_diff_uassoc', 'array_diff_ukey', 'array_intersect_ukey',
            'call_user_func', 'call_user_func_array', 'forward_static_call', 'forward_static_call_array',
            'register_shutdown_function', 'register_tick_function', 'set_error_handler', 'set_exception_handler',
            'ob_start', 'preg_replace_callback', 'preg_replace_callback_array', 'spl_autoload_register',
            'closure::fromcallable', 'reflectionfunction', 'reflectionmethod' // приведены к нижнему регистру для in_array
        ];

        // тест уязвимостей не понимает, что \PhpToken - есть только с php8
        if (PHP_VERSION_ID < 80000) {
            $dangerousFunctions[] = 'preg_replace';
        }

        try {
            $tokens = \PhpToken::tokenize($code);
        } catch (\ParseError $e) {
            return false; // Код с синтаксическими ошибками блокируем
        }

        // Идентификаторы токенов для имен функций/классов в PHP 8+
        $nameTokenIds = [
            T_STRING,
            defined('T_NAME_FULLY_QUALIFIED') ? T_NAME_FULLY_QUALIFIED : -1,
            defined('T_NAME_QUALIFIED') ? T_NAME_QUALIFIED : -1,
            defined('T_NAME_RELATIVE') ? T_NAME_RELATIVE : -1
        ];

        foreach ($tokens as $index => $token) {
            if ($token->isIgnorable()) continue;

            // Блокируем обратные кавычки `ls`
            if ($token->text === '`') return false;

            // Очищаем токен от ведущего слеша (\eval -> eval) для надежной проверки черного списка
            $cleanTokenTextLower = ltrim(strtolower($token->text), '\\');
            if (in_array($cleanTokenTextLower, $hardBlacklist, true)) return false;

            // Если встречаем вызов функции, инициализацию класса или вызов метода
            if ($token->text === '(') {
                $prevToken = self::getPreviousNonSpaceToken($tokens, $index);

                if ($prevToken !== null) {

                    // Проверяем вызовы по имени (используем $nameTokenIds вместо только T_STRING)
                    if (in_array($prevToken->id, $nameTokenIds, true)) {

                        // Нормализуем имя (\system -> system, \ReflectionFunction -> reflectionfunction)
                        $calledFuncName = ltrim(strtolower($prevToken->text), '\\');

                        // 1. Проверяем, не было ли перед этим именем конструкции "new" (для ReflectionFunction и т.д.)
                        $beforeNameToken = self::getPreviousNonSpaceToken($tokens, $tokens[$index]->id === T_STRING ? $index - 1 : $index - 1); // ищем токен перед именем

                        // Для надежности найдем токен именно перед $prevToken
                        for ($p = $index - 1; $p >= 0; $p--) {
                            if ($tokens[$p]->pos < $prevToken->pos && !$tokens[$p]->isIgnorable()) {
                                if ($tokens[$p]->id === T_NEW) {
                                    // Если это создание объекта, проверяем, не опасный ли класс-коллбэк создается
                                    if (in_array($calledFuncName, ['reflectionfunction', 'reflectionmethod'], true)) {
                                        if (!self::validateCallbackArgumentsOnly($tokens, $index)) {
                                            return false;
                                        }
                                    }
                                }
                                break;
                            }
                        }

                        // 2. Прямой вызов системной функции
                        if (in_array($calledFuncName, $dangerousFunctions, true)) return false;

                        // 3. ЗАКРЫВАЕМ CALLABLE В АРГУМЕНТАХ:
                        if (in_array($calledFuncName, $callbackFunctions, true)) {
                            if (!self::validateCallbackArgumentsOnly($tokens, $index)) {
                                return false;
                            }
                        }
                    }

                    // 4. Проверка статических вызовов вроде Closure::fromCallable()
                    // Если перед скобкой стоит имя метода (T_STRING), а перед ним двоеточие (T_DOUBLE_COLON)
                    if ($prevToken->id === T_STRING && $index >= 3) {
                        $doubleColonToken = self::getPreviousNonSpaceToken($tokens, $index - 1);
                        if ($doubleColonToken !== null && $doubleColonToken->text === '::') {
                            $classToken = self::getPreviousNonSpaceToken($tokens, $doubleColonToken->pos > 0 ? $index - 2 : 0);

                            // Собираем полное выражение: "closure::fromcallable"
                            if ($classToken !== null && in_array($classToken->id, $nameTokenIds, true)) {
                                $fullStaticCall = ltrim(strtolower($classToken->text), '\\') . '::' . strtolower($prevToken->text);
                                if (in_array($fullStaticCall, $callbackFunctions, true)) {
                                    if (!self::validateCallbackArgumentsOnly($tokens, $index)) {
                                        return false;
                                    }
                                }
                            }
                        }
                    }

                    // Запрет динамических вызовов: $f(), ()(), []()
                    if ($prevToken->id === T_VARIABLE || $prevToken->text === ')' || $prevToken->text === ']') {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Блокирует передачу потенциальных callable (строк, переменных, массивов)
     * в качестве аргументов для функций высшего порядка.
     */
    private static function validateCallbackArgumentsOnly(array $tokens, int $openBracketIndex): bool {
        $bracketCount = 1;
        $i = $openBracketIndex + 1;
        $total = count($tokens);

        while ($i < $total && $bracketCount > 0) {
            $t = $tokens[$i];

            if ($t->text === '(') $bracketCount++;
            if ($t->text === ')') $bracketCount--;

            // Если мы всё ещё находимся на первом уровне аргументов текущей функции
            if ($bracketCount === 1 && $t->text !== ',') {

                // 1. Запрещаем переменные: array_map($func, ...) или array_map($var, ...)
                if ($t->id === T_VARIABLE) {
                    return false;
                }

                // 2. Запрещаем любые строки (в кавычках): array_map('system', ...) или array_map("my_func", ...)
                if ($t->id === T_CONSTANT_ENCAPSED_STRING) {
                    return false;
                }

                // 3. Запрещаем массивы: array_map(['Class', 'method'], ...)
                // Токены могут быть '[' или старый синтаксис T_ARRAY
                if ($t->text === '[' || $t->id === T_ARRAY) {
                    return false;
                }
            }

            $i++;
        }
        return true;
    }

    private static function getPreviousNonSpaceToken(array $tokens, int $currentIndex): ?\PhpToken {
        for ($i = $currentIndex - 1; $i >= 0; $i--) {
            if (!$tokens[$i]->isIgnorable()) return $tokens[$i];
        }
        return null;
    }

    /**
     * выполняет php код шаблона записанного пользователем с правами edit_php
     *
     * @param $template
     * @param $macros
     * @param $arParams
     * @return mixed
     */
    public static function executePhp($template, &$macros, &$arParams)
	{
        if(self::isPhpCodeSafe($template)){
            $result = eval('use \Bitrix\Main\Mail\EventMessageThemeCompiler; ob_start();?>' . $template . '<?php return ob_get_clean();');
            return $result;
        }
        return $template;
	}

    public static function compileTemplate($template, &$macros){
        $arParams = array();
        foreach($macros as $k=>&$v){
            $arParams[str_replace("#","",$k)] = $v;
            if(is_array($v)) continue;

            // 2. УДАЛЯЕМ теги <?php
            $vClean = preg_replace('/<\?(php)?/i', '', $v);
            $vClean = str_replace('?>', '', $vClean);

            // 3. УДАЛЯЕМ знак доллара (запрет вызова переменных)
            if (stripos($template, '<?') !== false)
                $vClean = str_replace('$', '', $vClean);
            $v = $vClean;
        }
        unset($v);

        // ПРОВЕРКА: Вызываем executePhp только при наличии PHP-кода в шаблоне
        if (stripos($template, '<?') !== false) {
            /* для совместимости со старыми версиями, будет удалено в следующих версиях
            вместо макросов в php нужно использовать переменные в $arParams
            */

            //проверяем хеш шаблона, чтобы исключить подмену шаблона прямыми запросами в базу
            $confOb = Configuration::getInstance('mlife.smsservices');
            $existingSettings = $confOb->get('template_hashes');
            if(in_array(md5($template), $existingSettings)){
                $template = str_replace(array_keys($macros), $macros, $template);
                $template = self::executePhp($template, $macros, $arParams);
            }else{
                $template = '';
            }
        }else{
            $template = str_replace(array_keys($macros), $macros, $template);
        }
        foreach($arParams as $k=>$v){
            $macros['#'.$k.'#'] = $v;
        }
        //$template = preg_replace('/(\#[^#]+\#)/is',"",$template);
        return $template;
    }
	
	//вывод таба в админке
	public static function OnAdminTabControlBegin(&$form){
		
		$module_id = "mlife.smsservices";
		$MODULE_RIGHT_ = $GLOBALS["APPLICATION"]->GetGroupRight($module_id);
		
		if( ($MODULE_RIGHT_ >= "R") && (($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order_view.php") || ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order_edit.php")))
		{
			$orderId = intval($_REQUEST["ID"]);
			
			if($orderId) {
			
				$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/css/mlife.smsservices/style.css");
				
				$res = \Mlife\Smsservices\ListTable::getList(array(
					'select' => array("*"),
					'filter' => array("=EVENT"=>'MSMS_ORDER_'.$orderId),
					'order' => array("TIME"=>"ASC")
				));
				$html = '<tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME").'</td>
				</tr>';
				$html .= '<tr><td colspan="2"><table style="width:100%;border:1px solid #000000;">';
				
				while ($arData = $res->fetch()){
					if(!is_object($arData['TIME'])) $arData['TIME'] = \Bitrix\Main\Type\DateTime::createFromTimestamp($arData['TIME']);
					$html .= '<tr>
					<td style="border:1px solid #000000;">'.htmlspecialcharsEx($arData['SENDER']).' -> <br>'.htmlspecialcharsEx($arData['PHONE']).'
					</td>
					<td style="border:1px solid #000000;">'.$arData['TIME']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI"))).' -> <br>
					<font class="status_'.(($arData['STATUS']==14 || $arData['STATUS']==15) ? 4 : $arData['STATUS']).'">'.Loc::getMessage("MLIFE_SMSSERVICES_LIST_STATUS_".$arData['STATUS']).'</font>
					</td>
					<td style="border:1px solid #000000;">'.htmlspecialcharsEx($arData['MEWSS']).'
					</td>
					</tr>';
				}
				$html .= '</table>
				<br/>
				<a href="/bitrix/admin/mlife_smsservices_sendform.php?lang=ru&event=MSMS_ORDER_'.$orderId.'">'.Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_SENDSMS").'</a>
				</td></tr>';
				
				$form->tabs[] = array("DIV" => "my_edit", "TAB" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME"), "ICON"=>"aszmagazin", "TITLE"=>Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME"), "CONTENT"=>$html);
				
			}
			
		}
		
	}
	
	//отправка писем
	public static function OnBeforeEventSend($arFields, $eventMessage){
		
		//$eventMessage['EVENT_NAME'] - тип события
		//$eventMessage['ID'] - ид шаблона
		//$eventMessage['LID'] - ид сайта
		
		//print_r(array($arFields, $eventMessage)); die();
		
		$returnSendMail = true;
		
		\Bitrix\Main\Loader::includeModule('mlife.smsservices');
		$res = \Mlife\Smsservices\EventlistTable::getList(
			array(
				'select' => array("*"),
				'filter' => array("=EVENT"=>'MSMS_BXEVENT_'.$eventMessage['EVENT_NAME'],"ACTIVE"=>"Y")
			)
		);
		while($arData = $res->fetch()){

			
			$right = false;
			
			$r_site = \Bitrix\Main\Mail\Internal\EventMessageSiteTable::getList(array(
				'select' => array("SITE_ID"),
				'filter' => array("EVENT_MESSAGE_ID"=>$eventMessage['ID'])
			));
			while($dt = $r_site->fetch()){
				if($arData['SITE_ID'] == $dt['SITE_ID']) {
					$right = true;
					break;
				}
			}
			
			if(!$right) continue;
			
			if($arData['PARAMS']['EVENT_NAME'] == $eventMessage['EVENT_NAME']){
				//print_r($arData);
				//print_r($eventMessage);
				$right = false;
				
				if($arData['PARAMS']['ID'] == 'ALL') {
					$right = true;
				}else{
					if($arData['PARAMS']['ID'] == $eventMessage['ID']) $right = true;
				}
				
				if(!$right) continue;
				
				$arMakros = array();
				foreach($arFields as $fieldKey=>$fieldVal){
					$arMakros['#'.$fieldKey.'#'] = $fieldVal;
				}
				
				$arMakros['#EVENT_NAME#'] = 'MSMS_BXEVENT_'.$eventMessage['EVENT_NAME'];
				
				$orderId = $arMakros['#ORDER_ID#'];
				
				if($orderId && strpos($eventMessage['EVENT_NAME'],'SALE')===false) $orderId = false;
				
				$arOldMacros = $arMakros;
				
				//TODO not working cron events
				if($orderId && \Bitrix\Main\Loader::includeModule('sale')){
					$arMakros = array();
					$arOrderFields = \Bitrix\Sale\Internals\OrderTable::getList(
						array(
							'select' => array(
								"ID",
								"DATE_INSERT_FORMAT",
								"DATE_INSERT",
								"LID",
								"ACCOUNT_NUMBER",
								"TRACKING_NUMBER",
								"PAY_SYSTEM_ID",
								"DELIVERY_ID",
								"PERSON_TYPE_ID",
								"USER_ID",
								"PAYED",
								"STATUS_ID",
								"PRICE_DELIVERY",
								"ALLOW_DELIVERY",
								"PRICE",
								"CURRENCY",
								"DISCOUNT_VALUE",
								"TAX_VALUE",
								"SUM_PAID",
								"USER_DESCRIPTION",
								"AFFILIATE_ID",
								"STATUS_NAME"=>"STATUS.NAME",
								"USER_EMAIL"=>"USER.EMAIL",
								"USER_NAME"=>"USER.NAME",
								"USER_PERSONAL_PHONE"=>"USER.PERSONAL_PHONE",
								"USER_PERSONAL_MOBILE"=>"USER.PERSONAL_MOBILE",
								"USER_PERSONAL_CITY"=>"USER.PERSONAL_CITY",
								"USER_WORK_PHONE"=>"USER.WORK_PHONE",
								"USER_PERSONAL_GENDER"=>"USER.PERSONAL_GENDER"
								),
							'filter' => array("ID"=>$orderId)
						)
					)->fetch();
					if($arOrderFields){
						$arOrderFields['DATE_INSERT'] = $arOrderFields['DATE_INSERT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY")));
						
						$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"));
						$arMakros = array();
						
						foreach($arOrderFields as $prop_code=>$val){
							$arMakros['#'.$prop_code.'#'] = $val;
						}
						
						while($arProp = $dbProperty->Fetch()) {
							$arMakros['#PROPERTY_'.$arProp['CODE'].'#'] = '';
						}
						
						$dbOrderProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array(
							'select'=> array("*"), 
							'filter' => array("ORDER_ID"=>$orderId)
						)
						);
								
						while($arOrderProps = $dbOrderProps->fetch()) {
							$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
						}
						
						/*if ($propertyCollection = $order->getPropertyCollection())
						{
							$propVal = $propertyCollection->getArray();
							foreach($propVal['properties'] as $v){
								$arMakros['#PROPERTY_'.$v['CODE'].'#'] = $v['VALUE'][0];
							}
						}*/
						
						$arDelivery =  array();
						if($arOrderFields['DELIVERY_ID']) $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($arOrderFields['DELIVERY_ID']); //NAME
						if(is_array($arDelivery) && isset($arDelivery["NAME"])){
							$delivery = $arDelivery["NAME"];
						}else{
							$delivery = "";
						}
						
						$arPayment = array();
						if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemActionTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
						if(is_array($arPayment) && isset($arPayment["NAME"])){
							$payment = $arPayment["NAME"];
						}else{
							if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
							if(is_array($arPayment) && isset($arPayment["NAME"])){
								$payment = $arPayment["NAME"];
							}else{
								$payment = "";
							}
						}
						
						$arMakros['#ORDER_SUM#'] = $arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY'];
						$arMakros['#DELIVERY_NAME#'] = $delivery;
						$arMakros['#PAYMENT_NAME#'] = $payment;
						
						if(false && \Bitrix\Main\Loader::includeModule('currency') && \Bitrix\Main\Loader::includeModule('catalog')){
							$arMakros['#ORDER_SUM_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#ORDER_SUM#'],$arOrderFields['CURRENCY']);
							$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
							$arMakros['#PRICE_DELIVERY_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_DELIVERY#'],$arOrderFields['CURRENCY']);
							$arMakros['#PRICE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE#'],$arOrderFields['CURRENCY']);
							$arMakros['#DISCOUNT_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#DISCOUNT_VALUE#'],$arOrderFields['CURRENCY']);
							$arMakros['#TAX_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#TAX_VALUE#'],$arOrderFields['CURRENCY']);
							$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
						}
						
						$arNmakros = array();
						foreach($arMakros as $k_m=>$v_m){
							$arNmakros["#MSS_".substr($k_m,1)] = $v_m;
						}
						$arMakros = $arNmakros;
						$arMakros['#EVENT_NAME#'] = 'MSMS_ORDER_'.$arOrderFields['ID'];
						
						if(is_array($arOldMacros)){
							foreach($arOldMacros as $k_m=>$v_m){
								$arMakros[$k_m] = $v_m;
							}
						}
						
					}
				}elseif($userId = $arMakros['#USER_ID#']){
					$r = \Bitrix\Main\UserTable::getList(array(
						'select' => array("NAME", "PERSONAL_PHONE", "PERSONAL_MOBILE", "PERSONAL_CITY", "PERSONAL_GENDER", "EMAIL", "WORK_PHONE"),
						'filter' => array("ID"=>$userId)
					))->fetch();
					if($r){
						$arMakros = array();
						foreach($r as $k_m=>$v_m){
							$arMakros['#'.$k_m.'#'] = $v_m;
						}
						$arNmakros = array();
						foreach($arMakros as $k_m=>$v_m){
							$arNmakros["#MSS_USER_".substr($k_m,1)] = $v_m;
						}
						$arMakros = $arNmakros;
						if(is_array($arOldMacros)){
							foreach($arOldMacros as $k_m=>$v_m){
								$arMakros[$k_m] = $v_m;
							}
						}
					}
				}
				
				
				if($arData['PARAMS']['PHONE']){
					$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
					$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
					$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
					$phoneAr = explode(",",$phoneAr);
					$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
					foreach($phoneAr as $phone){
						if(strlen($phone)>7){
							
							if(trim($arData['TEMPLATE'])){
								$smsOb = new \Mlife\Smsservices\Sender();
								$smsOb->event = $arMakros['#EVENT_NAME#'];
								$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT");
								$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
								$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
								
								$smsOb->event = null;
								$smsOb->eventName = null;
								
								if($arData['PARAMS']['BREAK'] == "Y") $returnSendMail = false;
							}
							
							break;
						}
					}
				}
				
				
			}
			
			
		}

		
		//запрет отправки письма
		if(!$returnSendMail) return false;
		if($eventMessage['EMAIL_FROM'] == 'EMPTY@emptu.ru') return false;
	}
	
}