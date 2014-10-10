<?php
namespace hirdetek\V1\Rest\Regio;

class RegioResourceFactory
{
    public function __invoke($services)
    {
		$mapper = $services->get('hirdetek\V1\Rest\Regio\RegioMapper');
        return new RegioResource($mapper);
    }
}
