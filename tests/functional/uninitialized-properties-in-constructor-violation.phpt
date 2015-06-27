--TEST--
Verifies that an object that doesn't initialize all its properties in a public constructor is considered as a failure
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithIncorrectlyInitializingConstructor();
?>
--EXPECTF--
%ACatchable fatal error: Argument 1 passed to %a must be %a array, null given%a