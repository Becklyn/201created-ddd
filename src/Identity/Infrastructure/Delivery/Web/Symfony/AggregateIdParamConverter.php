<?php

namespace C201\Ddd\Identity\Infrastructure\Delivery\Web\Symfony;

use C201\Ddd\Identity\Domain\AggregateId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Christoph Hautzinger <ch@201created.de>
 * @since  2019-11-15
 */
class AggregateIdParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $aggregateId = $request->attributes->get($configuration->getName());
        $class = $configuration->getClass();

        $request->attributes->set($configuration->getName(), $class::fromString($aggregateId));

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        if (!class_exists($configuration->getClass())) {
            return false;
        }

        return in_array(AggregateId::class, class_implements($configuration->getClass()));
    }
}
