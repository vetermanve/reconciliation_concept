<?php

use ReconciliationTool\ReconciliationAdapter\ReconciliationUserDatabaseNew;
use ReconciliationTool\ReconciliationAdapter\ReconciliationUserDatabaseOld;

chdir(__DIR__);

include 'vendor/autoload.php';

$config = include 'config.php';

$adapterNew = new ReconciliationUserDatabaseNew();
$adapterOld = new ReconciliationUserDatabaseOld();

$processor = new \ReconciliationTool\ReconciliationProcessor();

$processor->addAdapter($adapterNew);
$processor->addAdapter($adapterOld);

{
    $storage = new \ReconciliationTool\CompareStorage($config['db']['dsn'], $config['db']['user'], $config['db']['password']);
}

$processor->setStorage($storage);

$processor->process();

var_dump($processor->getRawDiff());