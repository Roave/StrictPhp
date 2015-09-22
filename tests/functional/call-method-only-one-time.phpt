--TEST--
Verifies that method is called only one time
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithMethodOutput();

$object->hello();
?>
--EXPECTF--
Hello
