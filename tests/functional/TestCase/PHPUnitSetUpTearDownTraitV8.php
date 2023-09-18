<?php

namespace Cs278\Mktemp\Tests\TestCase;

trait PHPUnitSetUpTearDownTrait
{
    public function setUp(): void
    {
        $this->doSetup();
    }

    public function tearDown(): void
    {
        $this->doTearDown();
    }
}
