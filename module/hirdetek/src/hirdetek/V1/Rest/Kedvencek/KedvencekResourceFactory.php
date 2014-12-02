<?php
namespace hirdetek\V1\Rest\Kedvencek;

class KedvencekResourceFactory
{
    public function __invoke($services) {
        
        $mapper = $services->get('hirdetek\V1\Rest\Megosztas\KedvencekMapper');
        return new KedvencekResource($mapper);
    }
}
