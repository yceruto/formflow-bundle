<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShutdownBundle extends Bundle
{
    public function shutdown(): void
    {
        restore_exception_handler();
    }
}
