<?php

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Yceruto\FormFlowBundle\FormFlowBundle;
use Yceruto\FormFlowBundle\Tests\Integration\App\TestBundle;

return [
    new FrameworkBundle(),
    new TwigBundle(),
    new WebProfilerBundle(),
    new FormFlowBundle(),
    new TestBundle(),
];
