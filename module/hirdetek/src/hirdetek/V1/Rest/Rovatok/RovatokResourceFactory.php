<?php
namespace hirdetek\V1\Rest\Rovatok;

class RovatokResourceFactory
{
    public function __invoke($services)
    {
		$mapper = $services->get('hirdetek\V1\Rest\Rovatok\RovatokMapper');
        return new RovatokResource($mapper);
    }
}
