<?php

namespace Cs278\Mktemp\Tests\TestCase;

use PHPUnit\Framework\TestCase;

if (\PHP_VERSION_ID < 70000 || !(new \ReflectionClass(TestCase::class))->getMethod('setUp')->hasReturnType()) {
    require __DIR__.'/PHPUnitSetUpTearDownTraitV5.php';
} else {
    require __DIR__.'/PHPUnitSetUpTearDownTraitV8.php';
}
