--TEST--
Verifies that an object that doesn't initialize all the properties in a parent class is considered as a failure
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithIncorrectlyInitializedParentClassProperties();
?>
--EXPECTF--
%ACatchable fatal error: Argument 1 passed to %a must be %a array, null given%a