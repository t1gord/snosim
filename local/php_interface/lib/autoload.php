<?php

use Bitrix\Main\Loader;

//Автозагрузка наших классов
Loader::registerAutoLoadClasses(null, [
    'lib\usertype\CUserTypeTimesheet' => APP_CLASS_FOLDER . 'usertype/CUserTypeProp.php',
]);