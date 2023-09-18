<?php

namespace Cs278\Mktemp\Tests\TestCase;

trait PHPUnitSetUpTearDownTrait
{
    public function setUp()
    {
        $this->doSetup();
    }

    public function tearDown()
    {
        $this->doTearDown();
    }
}
