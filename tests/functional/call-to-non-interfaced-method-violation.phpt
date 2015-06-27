--TEST--
Verifies that calling a non-interfaced method in a context that is only aware of the interface causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$helloSayer = new \StrictPhpTestAsset\ClassWithHelloImplementationAndAdditionalMethod();
$helloUser  = new \StrictPhpTestAsset\ClassThatDependsOnHello();

$helloUser->sayHello($helloSayer, 'Marco');

echo "\nOK";

$helloUser->doSomethingElseWithHello($helloSayer);

echo 'Never reached'

?>
--EXPECTF--
%ACatchable fatal error: call to undefined method ...