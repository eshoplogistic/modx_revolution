<?php

$_lang['area_eshoplogistic_main'] = 'Основные';
$_lang['area_eshoplogistic_payment'] = 'Способы оплаты';
$_lang['area_eshoplogistic_order'] = 'Заказ';

$_lang['setting_eshoplogistic_api_key'] = 'Ключ API eShopLogistic';
$_lang['setting_eshoplogistic_api_key_desc'] = '<a href="https://eshoplogistic.ru" target="_blank">eshoplogistic.ru</a>';
$_lang['setting_eshoplogistic_payment_card'] = 'Методы оплаты MS2, ассоциируемые с типом «Оплата картой»';
$_lang['setting_eshoplogistic_payment_card_desc'] = 'Указать id методов, через запятую. Например: 2,3,4';
$_lang['setting_eshoplogistic_payment_cash'] = 'Методы оплаты MS2, ассоциируемые с типом «Оплата наличными»';
$_lang['setting_eshoplogistic_payment_cash_desc'] = 'Указать id методов, через запятую. Например: 2,3,4';
$_lang['setting_eshoplogistic_payment_cashless'] = 'Методы оплаты MS2, ассоциируемые с типом «Безналичный расчёт»';
$_lang['setting_eshoplogistic_payment_cashless_desc'] = 'Указать id методов, через запятую. Например: 2,3,4';
$_lang['setting_eshoplogistic_payment_prepay'] = 'Методы оплаты MS2, ассоциируемые с типом «Предоплата»';
$_lang['setting_eshoplogistic_payment_prepay_desc'] = 'Указать id методов, через запятую. Например: 2,3,4';
$_lang['setting_eshoplogistic_frontend_js'] = 'JS-файл для фронта';
$_lang['setting_eshoplogistic_frontend_js_desc'] = 'Можно указать тут свой скрипт или перенести логику в свой js-файл и очистить поле.';
$_lang['setting_eshoplogistic_frontend_js'] = 'JS-файл для фронта';
$_lang['setting_eshoplogistic_frontend_js_desc'] = 'Можно указать тут свой скрипт или перенести логику в свой js-файл и очистить поле.';
$_lang['setting_eshoplogistic_no_delivery_id'] = 'Способ доставки по-умолчанию';
$_lang['setting_eshoplogistic_no_delivery_id_desc'] = 'ID способа доставки MS2, если не получено ни одного результата по другим вариантам.';
$_lang['setting_eshoplogistic_frontend_css'] = 'СSS-файл для фронта';
$_lang['setting_eshoplogistic_frontend_css_desc'] = 'Можно указать тут свой файл или перенести стили в свой css-файл и очистить поле.';
$_lang['setting_eshoplogistic_session_livetime'] = 'Время жизни кэша данных доставок, часы';
$_lang['setting_eshoplogistic_session_livetime_desc'] = 'Для снижения количества запросов к сервису eShopLogistic (+ ускорения получения данных),'
		. ' устанавливайте время кэширования.';
$_lang['setting_eshoplogistic_widget_keys'] = 'Секретные коды виджетов';
$_lang['setting_eshoplogistic_widget_keys_desc'] = 'Если вы хотите принимать заказы через виджеты, укажите секретные код, через запятую. '
		. 'Документация тут: <a href="https://docs.eshoplogistic.ru/widget/#item-18">https://docs.eshoplogistic.ru/widget/#item-18</a>';
$_lang['setting_eshoplogistic_message_ordersuccess'] = 'Сообщение при успешном создании заказа';
$_lang['setting_eshoplogistic_message_ordersuccess_desc'] = 'Выводится в виджете в случае успешного создания заказа.';
$_lang['setting_eshoplogistic_message_ordererror'] = 'Сообщение при ошибке создания заказа';
$_lang['setting_eshoplogistic_message_ordererror_desc'] = 'Выводится в виджете в случае ошибки.';
$_lang['setting_eshoplogistic_message_orderprefix'] = 'Префикс номера заказа';
$_lang['setting_eshoplogistic_message_orderprefix_desc'] = '';
$_lang['setting_eshoplogistic_payment_on'] = 'Учитывать способ оплаты';
$_lang['setting_eshoplogistic_payment_on_desc'] = 'Укажите «Да», если при расчёте стоимости доставки в корректирующих правилах у вас учитывается способ оплаты. Документация тут: <a href="https://docs.eshoplogistic.ru/personal-area/#item-13">https://docs.eshoplogistic.ru/personal-area/#item-13</a>';