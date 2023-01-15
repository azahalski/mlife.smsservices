<?
$MESS['MLIFE_SMSSERVICES_FIELDS_TO'] = 'Получатель смс';
$MESS['MLIFE_SMSSERVICES_FIELDS_APPSMS'] = 'App сообщение';
$MESS['MLIFE_SMSSERVICES_FIELDS_STATUS_TO'] = 'Текущий статус';
$MESS['MLIFE_SMSSERVICES_FIELDS_STATUS_ALL'] = 'Все статусы';
$MESS['MLIFE_SMSSERVICES_FIELDS_STATUS_FROM'] = 'Начальный статус';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS'] = 'Доступные макросы';
$MESS['MLIFE_SMSSERVICES_FIELDS_PAYED_Y'] = 'Поступление оплаты';
$MESS['MLIFE_SMSSERVICES_FIELDS_PAYED_N'] = 'Снятие оплаты';
$MESS['MLIFE_SMSSERVICES_FIELDS_PAYED'] = 'Операция по оплате заказа';
$MESS['MLIFE_SMSSERVICES_FIELDS_BXEVENT_BREAK'] = 'Запретить отправку письма после отправки смс';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_BXEVENT'] = 'Описание почтового события будет доступно после нажатия кнопки применить.';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_BXEVENT_NOTE'] = 'Список макросов анологичен системному почтовому событию.';
$MESS['MLIFE_SMSSERVICES_FIELDS_EVENT_NAME'] = 'Тип события в почтовой системе';
$MESS['MLIFE_SMSSERVICES_FIELDS_BXEVENTID'] = 'Идентификатор почтового шаблона';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER'] = '
#ID# - ид заказа<br>
#LID# - ид сайта<br>
#ACCOUNT_NUMBER# - номер заказа<br>
#TRACKING_NUMBER# - трекинг код<br>
#PAY_SYSTEM_ID# - ид платедной системы<br>
#DELIVERY_ID# - ид службы доставки<br>
#PERSON_TYPE_ID# - ид типа плательщика<br>
#USER_ID# - ид пользователя<br>
#PAYED# - оплата заказа<br>
#STATUS_ID# - код статуса<br>
#PRICE_DELIVERY# - стоимость доставки<br>
#PRICE_DELIVERY_FORMAT# - стоимость доставки (форматированная)<br>
#ALLOW_DELIVERY# - доставка разрешена<br>
#PRICE# - сумма заказа<br>
#PRICE_FORMAT# - сумма заказа (форматированная)<br>
#CURRENCY# - код валюты<br>
#DISCOUNT_VALUE# - сумма скидки<br>
#DISCOUNT_VALUE_FORMAT# - сумма скидки (форматированная)<br>
#TAX_VALUE# - наценка<br>
#TAX_VALUE_FORMAT# - наценка (форматированная)<br>
#SUM_PAID# - уже оплачено<br>
#SUM_PAID_FORMAT# - уже оплачено (форматированная)<br>
#USER_DESCRIPTION# - комментарий пользователя<br>
#STATUS_NAME# - название статуса<br>
#USER_EMAIL# - email пользователя<br>
#USER_NAME# - имя пользователя<br>
#USER_PERSONAL_PHONE# - телефон пользователя<br>
#USER_PERSONAL_MOBILE# - мобильный телефон пользователя<br>
#USER_PERSONAL_CITY# - город пользователя<br>
#USER_WORK_PHONE# - рабочий телефон пользователя<br>
#USER_PERSONAL_GENDER# - пол пользователя<br>
#ORDER_SUM# - сумма заказа без доставки<br>
#ORDER_SUM_FORMAT# - сумма заказа без доставки (форматированная)<br>
#DELIVERY_NAME# - название службы доставки<br>
#PAYMENT_NAME# - название платежной системы<br>
#DATE_INSERT_FORMAT# - дата заказа со временем<br>
#DATE_INSERT# - дата заказа<br><br>
<font style="font-size:12px;">* в поле телефон, можно указать несколько значений через запятую (будет взят первый найденный номер телефона)<br>
* для отправки сообщения на несколько номеров - следует создать дополнительный шаблон<br></font>
';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_NEWORDER_'] = '<b>Получаемые автоматически в случае найденного ORDER_ID</b><br>
#MSS_ID# - ид заказа<br>
#MSS_LID# - ид сайта<br>
#MSS_ACCOUNT_NUMBER# - номер заказа<br>
#MSS_TRACKING_NUMBER# - трекинг код<br>
#MSS_PAY_SYSTEM_ID# - ид платедной системы<br>
#MSS_DELIVERY_ID# - ид службы доставки<br>
#MSS_PERSON_TYPE_ID# - ид типа плательщика<br>
#MSS_USER_ID# - ид пользователя<br>
#MSS_PAYED# - оплата заказа<br>
#MSS_STATUS_ID# - код статуса<br>
#MSS_PRICE_DELIVERY# - стоимость доставки<br>
#MSS_PRICE_DELIVERY_FORMAT# - стоимость доставки (форматированная)<br>
#MSS_ALLOW_DELIVERY# - доставка разрешена<br>
#MSS_PRICE# - сумма заказа<br>
#MSS_PRICE_FORMAT# - сумма заказа (форматированная)<br>
#MSS_CURRENCY# - код валюты<br>
#MSS_DISCOUNT_VALUE# - сумма скидки<br>
#MSS_DISCOUNT_VALUE_FORMAT# - сумма скидки (форматированная)<br>
#MSS_TAX_VALUE# - наценка<br>
#MSS_TAX_VALUE_FORMAT# - наценка (форматированная)<br>
#MSS_SUM_PAID# - уже оплачено<br>
#MSS_SUM_PAID_FORMAT# - уже оплачено (форматированная)<br>
#MSS_USER_DESCRIPTION# - комментарий пользователя<br>
#MSS_STATUS_NAME# - название статуса<br>
#MSS_USER_EMAIL# - email пользователя<br>
#MSS_USER_NAME# - имя пользователя<br>
#MSS_USER_PERSONAL_PHONE# - телефон пользователя<br>
#MSS_USER_PERSONAL_MOBILE# - мобильный телефон пользователя<br>
#MSS_USER_PERSONAL_CITY# - город пользователя<br>
#MSS_USER_WORK_PHONE# - рабочий телефон пользователя<br>
#MSS_USER_PERSONAL_GENDER# - пол пользователя<br>
#MSS_ORDER_SUM# - сумма заказа без доставки<br>
#MSS_ORDER_SUM_FORMAT# - сумма заказа без доставки (форматированная)<br>
#MSS_DELIVERY_NAME# - название службы доставки<br>
#MSS_PAYMENT_NAME# - название платежной системы<br>
#MSS_DATE_INSERT_FORMAT# - дата заказа со временем<br>
#MSS_DATE_INSERT# - дата заказа<br><br>
<font style="font-size:12px;">* в поле телефон, можно указать несколько значений через запятую (будет взят первый найденный номер телефона)<br>
* для отправки сообщения на несколько номеров - следует создать дополнительный шаблон<br></font>
';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_USER_'] = '<b>Получаемые автоматически в случае найденного USER_ID</b><br>
#MSS_USER_EMAIL# - email пользователя<br>
#MSS_USER_NAME# - имя пользователя<br>
#MSS_USER_PERSONAL_PHONE# - телефон пользователя<br>
#MSS_USER_PERSONAL_MOBILE# - мобильный телефон пользователя<br>
#MSS_USER_PERSONAL_CITY# - город пользователя<br>
#MSS_USER_WORK_PHONE# - рабочий телефон пользователя<br>
#MSS_USER_PERSONAL_GENDER# - пол пользователя<br>
';
$MESS['MLIFE_SMSSERVICES_FIELDS_MACROS_RESET_PASSWORD'] = '<br>
#PHONE# - телефон пользователя<br>
#USER_ID# - ид найденного пользователя<br>
#NEWPASS# - новый пароль<br>
#ORDER_ID# - номер заказа, если есть<br>
';
