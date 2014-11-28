<?php
namespace hirdetek\V1\Rest\Megosztas;

class MegosztasResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('hirdetek\V1\Rest\Megosztas\MegosztasMapper');
        return new MegosztasResource($mapper);
    }
}
