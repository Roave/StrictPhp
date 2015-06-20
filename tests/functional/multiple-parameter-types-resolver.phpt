--TEST--
Verifies that pass different deep inside collection parameter to a method call raises an Exception
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithComplexParameterOnMethod();

$object->foo([['string']]);

echo "OK1!\n";

$object->foo([['123', '456', '789']]);

echo "OK2!\n";

$object->foo([true]);

echo "OK3!\n";

?>
--EXPECTF--
OK1!
OK2!
OK3!
