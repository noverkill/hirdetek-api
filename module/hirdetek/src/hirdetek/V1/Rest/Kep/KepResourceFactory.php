<?php
namespace hirdetek\V1\Rest\Kep;

class KepResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('hirdetek\V1\Rest\Hirdetes\KepMapper');
        return new KepResource($mapper);
    }
}
