<?php

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Yceruto\FormFlowBundle\FormFlowBundle;
use Yceruto\FormFlowBundle\Tests\Integration\App\ShutdownBundle;

return [
    new FrameworkBundle(),
    new TwigBundle(),
    new FormFlowBundle(),
    new ShutdownBundle(),
];
