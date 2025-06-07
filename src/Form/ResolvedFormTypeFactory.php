<?php

namespace Yceruto\FormFlowBundle\Form;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

final class ResolvedFormTypeFactory implements ResolvedFormTypeFactoryInterface
{
    public function createResolvedType(FormTypeInterface $type, array $typeExtensions, ?ResolvedFormTypeInterface $parent = null): ResolvedFormTypeInterface
    {
        return new ResolvedFormType($type, $typeExtensions, $parent);
    }
}
