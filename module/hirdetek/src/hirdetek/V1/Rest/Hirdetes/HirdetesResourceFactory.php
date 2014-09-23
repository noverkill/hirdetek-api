<?php
namespace hirdetek\V1\Rest\Hirdetes;

class HirdetesResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('hirdetek\V1\Rest\Hirdetes\HirdetesMapper');
        return new HirdetesResource($mapper);
    }
}
