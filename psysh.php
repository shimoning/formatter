#!/usr/bin/env php
<?php

namespace Shimoning\Formatter;

require_once __DIR__ . '/vendor/autoload.php';

echo __NAMESPACE__ . " shell\n";
echo "-----\nexample:\n";
echo "var_dump(Time::number2clock(100));\n";
echo "var_dump(Sql::sanitizeTextForSearchQuery('%test'));\n-----\n\n";

$sh = new \Psy\Shell();

$sh->addCode(sprintf("namespace %s;", __NAMESPACE__));

$_SERVER['HTTP_HOST']='localhost';

$sh->run();

echo "\n-----\nBye.\n";
