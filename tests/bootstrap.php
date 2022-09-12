<?php

require_once __DIR__ . '/../../../tests/bootstrap.php';

$infoXml = new \SimpleXMLElement(file_get_contents(__DIR__ . '/../appinfo/info.xml'));
$app = (string)$infoXml->id;
\OC_App::registerAutoloading($app, __DIR__ . '/..');
