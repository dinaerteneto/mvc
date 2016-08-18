<?php
$aRoute = explode('/',$_GET['r']);
$sController = $aRoute[0];
$sAction = $aRoute[1];

$aParams = $_REQUEST;
unset($aParams['r']);

$class = "controllers\\{$sController}Controller";
$oController = new $class;

$GLOBALS['CONTROLLER'] = $sController;
$GLOBALS['ACTION'] = $sAction;

call_user_method_array($sAction, $oController, $aParams);