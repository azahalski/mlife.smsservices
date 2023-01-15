## Описание модуля ##

Модуль позволяет отправлять смс уведомления с админ панели, сохраняет историю всех отправленных сообщений.
Возможна отправка уведомлений с любых других компонентов с использованием api модуля.

**Модуль можно установить на все редакции 1С-Битрикс.**

**Требования**: база данных MySQL (с Oracle и MsSQL работать не будет), Curl, SimpleXML (для sms4b.ru).

Список поддерживаемых смс шлюзов:
SMSC.RU, SMSPILOT.RU, LITTLESMS.RU, SMS.RU, SMS4B.RU, SMS16.RU, USER.REKLAMAVKARMANE.RU, IQSMS.RU - Смс дисконт, STREAM-TELECOM.RU, SMS-ASSISTENT.BY, SMSP.BY, SMS-ASSISTENT.RU, ROCKETSMS.BY, PIR.COMPANY, SMSGK.RU, DEVINOTELE.COM, P1SMS.RU, QTELECOM.RU, SMS96.RU, TARGETSMS.RU, IBATELE.COM, ESPUTNIK.COM, BYTEHAND.COM, SMS-FLY.UA, SMS.BY, SMSINT.RU, REDSMS.RU, SMSPRO.NIKITA.KG, MTS.BY, SMSC.RU - Viber, DEVINOTELE.COM - Viber, REDSMS.RU - Viber

Установить модуль можно по ссылке - [Модуль смс уведомления](http://marketplace.1c-bitrix.ru/solutions/mlife.smsservices/)



## Настройка модуля ##

| Параметр                                | Описание                                                                                                                                                                                                 |
|-----------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Шлюз                                    | Укажите шлюз отправки смс сообщений                                                                                                                                                                      |
| Логин, Пароль*	                         | Данные доступа к сервису (описание ниже)                                                                                                                                                                 |
| Отправитель	                            | Регистрируется на сервисе                                                                                                                                                                                |
| Получить список отправителей с сервиса	 | Можно отметить данную опцию и нажать сохранить, если данные для подключения к шлюзу верны - то список доступных отправителей будет получен в автоматическом режиме, после чего вы можете выбрать нужного |
| Время кеширования списка отправителей	  | Время в секундах для кеширования списка отправителей, можно указать например 1 месяц (2592000)                                                                                                           |
| Время кеширования баланса	              | Время в секундах для кеширования баланса, не рекомендуется устанавливать данное значение менее чем 1 минута. Сервис может временно заблокироват ip адрес                                                 |
| Кодировка	                              | Кодировка отправляемых сообщений: windows-1251, utf-8                                                                                                                                                    |

**SMSC.RU** - в настройках доступа указываем: логин и пароль от сервиса.

**SMSPILOT.RU** - в настройках доступа указываем: API-ключ (найти его можно на вкладке Настройки в личном кабинете сервиса).

**LITTLESMS.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль -  API-key (найти его можно на вкладке Настройки API в личном кабинете сервиса).

**SMS.RU** - в настройках доступа указываем: API_ID (найти его можно на вкладке Настройки - Сменить api_id в личном кабинете сервиса), логин указывать не обязательно.

**SMS4B.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа из внешних программ (может отличаться от пароля из аккаунта).

**SMS16.RU** - в настройках доступа указываем: в поле пароль - Токен для авторизации по XML, найти его можно на вкладке настройки в личном кабинете сервиса, логин указывать не обязательно!

**USER.REKLAMAVKARMANE.RU** - в настройках доступа указываем: в поле логин вводим 'ID клиента:ID_СМС-сервиса' (ID клиента символ 'точка с запятой' ID СМС-сервиса) найти их можно на вкладке профиль в личном кабинете сервиса, в поле пароль указываем Ключ API, найти их можно на вкладке профиль в личном кабинете сервиса.

**SMS-ASSISTENT.BY** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**SMSP.BY** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем апи ключ доступа к сервису. (ключ можно найти в личном кабинете на сервисе, возможна ручная модерация и активация доступа к API на сервисе)
в поле логин необходимо указать apiurl, например: cabinet.smsp.by||логин

**SMS-ASSISTENT.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**ROCKETSMS.BY** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**PIR.COMPANY** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису либо пароль для доступа к шлюзу, если задан в настройках на сервисе. (шлюз по умолчанию - bitrix.pir.company, если ваш шлюз отличается то указываем его в логине в формате bitrix.pir.company||логин)

**TARGETSMS.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису либо пароль для доступа к шлюзу, если задан в настройках на сервисе. (шлюз по умолчанию - sms.targetsms.ru, если ваш шлюз отличается то указываем его в логине в формате my5.t-sms.ru||логин)

**SMS96.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису либо пароль для доступа к шлюзу, если задан в настройках на сервисе. (шлюз по умолчанию - kabinet.sms96.ru, если ваш шлюз отличается то указываем его в логине в формате kabinet.sms96.ru||логин)

**SMSGK.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**STREAM-TELECOM.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**DEVINOTELE.COM** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**P1SMS.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**QTELECOM.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**IBATELE.COM** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису либо пароль для доступа к шлюзу, если задан в настройках на сервисе. (шлюз по умолчанию - lk.ibatele.com, если ваш шлюз отличается то указываем его в логине в формате lk.ibatele.com||логин)

**ESPUTNIK.COM** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**SMS-FLY.UA** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**SMSINT.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**SMSPRO.NIKITA.KG** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**IQSMS.RU - Смс дисконт** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**SMS.BY** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису. В качестве имени отправителя нужно указывать его идентификатор на сервисе а не фактическое название.

**BYTEHAND.COM** - в настройках доступа указываем: в поле логин указываем ИД, в поле пароль указываем ключ для доступа к сервису (найти можно на странице настройки в личном кабинете на сервисе).

**REDSMS.RU** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль -  API-key (найти его можно на вкладке Настройки -> API в личном кабинете сервиса).

**SMSC.RU - Viber** - в настройках доступа указываем: логин и пароль от сервиса.

**MTS.BY** - в настройках доступа указываем: в поле логин apiurl||логин (например для ид услуги 8224, api.br.mts.by/8224||myLogin), в поле пароль - пароль для отправки смс по апи.

**DEVINOTELE.COM - Viber** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа к сервису.

**STREAM-TELECOM.RU - Viber** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль указываем пароль для доступа по api.

**REDSMS.RU - Viber** - в настройках доступа указываем: логин для доступа к сервису, в поле пароль -  API-key (найти его можно на вкладке Настройки -> API в личном кабинете сервиса).

## Пример шаблона отправки сообщения в viber, в случае недоставки переотправка в смс (разделитель |||)##
```
#!php
Тестовое сообщение для Viber|||Тестовое сообщение для смс
```

## Документация для разработчиков ##

### CMlifeSmsServices::checkPhoneNumber ###

```
#!php
CMlifeSmsServices::checkPhoneNumber(
     string phone,
     boolean all = true
)
```
метод для валидации номера телефона, возвращает массив с результатом проверки: array('phone','check')

phone: номер телефона очищенный от мусора

check: true - верный формат номера, false - неверный формат номера

| Параметр | Описание                                                             |
|----------|----------------------------------------------------------------------|
| phone    | Обязательный параметр, номер телефона                                |
| all      | true - проверка номера по всему миру, false - проверка номера по снг |

** Примеры использования: **


```
#!php
if (CModule::IncludeModule('mlife.smsservices')){

     $obSmsServ = new CMlifeSmsServices();

     $phone = '+375(25)777-77-75';
     $phoneCheck = $obSmsServ->checkPhoneNumber($phone);
     $phone = $phoneCheck['phone'];

     if($phoneCheck['check']) {
          echo 'Номер: '.$phone.' - существует';
     }else{
          echo $phone.' - формат номера неверный, либо отправка смс на ваш номер невозможна';
     }

}

//или
if (\Bitrix\Main\Loader::includeModule('mlife.smsservices')){

     $transport = new \Mlife\Smsservices\Sender();
     
     $phone = '+375(25)777-77-75';
     $phoneCheck = $transport->checkPhoneNumber($phone);
     $phone = $phoneCheck['phone'];

     if($phoneCheck['check']) {
          echo 'Номер: '.$phone.' - существует';
     }else{
          echo $phone.' - формат номера неверный, либо отправка смс на ваш номер невозможна';
     }

}
```

### CMlifeSmsServices::sendSms ###


```
#!php
CMlifeSmsServices::sendSms(
     string phones,
     string mess,
     string time = 0,
     boolean||string sender = false,
     string prim = '',
     boolean addHistory = true,
     boolean||array update = false,
     boolean||array error = false,
)
```

метод для отправки смс, в случае успеха возвращает объект: ('id','cnt','cost','balance'), 
в случае ошибки: ('error','error_code')

id: id сообщения возвращаемое сервисом, может использоваться для проверки статуса сообщения на сервисе

cnt: количество отправленных смс сообщений

cost: стоимость рассылки

balance: новый остаток на счете клиента

error: текст ошибки

error_code: код ошибки

параметры cnt,cost,balance - возвращаются не всеми сервисами! В случае, если сервис после отправки сообщения 
не передает данные параметры они будут пустые!

| Параметр   | Описание                                                                              |
|------------|---------------------------------------------------------------------------------------|
| phones     | Обязательный параметр, номер телефона формат +7921 777 88 99                          |
| mess       | Обязательный параметр, текст сообщения                                                |
| time       | Время отправки сообщения в UNIXTIME, 0 - текущее                                      |
| sender     | Отправитель сообщения, по умолчанию используется отправитель с настроек модуля        |
| prim       | Примечание к сообщению, по умолчанию пустое                                           |
| addHistory | Определяет запись сообщения в историю, по умолчанию сохраняет true                    |
| update     | Определяет следует ли обновить статус сообщения, по умолчанию false                   |
| error      | параметр для рекурсии, в случае ошибок на сервисе сообщение также добавится в историю |

** Примеры использования: **

```
#!php
if (CModule::IncludeModule('mlife.smsservices')){

     $obSmsServ = new CMlifeSmsServices();

     $phones = '+375257777775';
     $mess = 'Тестовое сообщение';
     $sender = 'DED-MOROZ';
     $arSend = $obSmsServ->sendSms($phones,$mess,0,$sender);

     if($arSend->error) {
          echo 'Ошибка отправки смс: '.$arSend->error.', код ошибки: '.$arSend->error_code;
     }else{
          echo 'Сообщение успешно отправлено, Стоимость рассылки:'.$arSend->cost.' руб.';
     }

}

//или
if (\Bitrix\Main\Loader::includeModule('mlife.smsservices')){

     $transport = new \Mlife\Smsservices\Sender();
     
     $phones = '+375257777775';
     $mess = 'Тестовое сообщение';
     $sender = 'DED-MOROZ';
     $arSend = $transport->sendSms($phones,$mess,0,$sender);

     if($arSend->error) {
          echo 'Ошибка отправки смс: '.$arSend->error.', код ошибки: '.$arSend->error_code;
     }else{
          echo 'Сообщение успешно отправлено, Стоимость рассылки:'.$arSend->cost.' руб.';
     }

}

//отправка сообщения на app шлюз
if (\Bitrix\Main\Loader::includeModule('mlife.smsservices')){

     $transport = new \Mlife\Smsservices\Sender();
     $transport->app = true;
     
     $phones = '+375257777775';
     $mess = 'Тестовое сообщение';
     $sender = 'DED-MOROZ';
     $arSend = $transport->sendSms($phones,$mess,0,$sender);

     if($arSend->error) {
          echo 'Ошибка отправки смс: '.$arSend->error.', код ошибки: '.$arSend->error_code;
     }else{
          echo 'Сообщение успешно отправлено, Стоимость рассылки:'.$arSend->cost.' руб.';
     }

}

//отправка сообщения через резервный шлюз минуя основной
if (\Bitrix\Main\Loader::includeModule('mlife.smsservices')){

     $transport = new \Mlife\Smsservices\Sender();
     $transport->reserve = true;
     
     $phones = '+375257777775';
     $mess = 'Тестовое сообщение';
     $sender = 'DED-MOROZ';
     $arSend = $transport->sendSms($phones,$mess,0,$sender);

     if($arSend->error) {
          echo 'Ошибка отправки смс: '.$arSend->error.', код ошибки: '.$arSend->error_code;
     }else{
          echo 'Сообщение успешно отправлено, Стоимость рассылки:'.$arSend->cost.' руб.';
     }

}

```