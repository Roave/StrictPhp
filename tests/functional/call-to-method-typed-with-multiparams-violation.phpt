--TEST--
Verifies that calling a method with a typed multiple array parameter annotation causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

(new \StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation())
    ->method([], [['foo']], true);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation())
    ->method([1, 2, 3], [['bar']], false);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation())
    ->method([new stdClass(), new stdClass(), new stdClass()], [['foo']], true);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation())
    ->method([], true, false);

echo "Never reached\n";

?>
--EXPECTF--
OK
OK
OK
%Aatal error: Argument 2 passed to StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation::method() must be of the type array, boolean given%a