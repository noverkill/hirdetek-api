<?php
namespace Users\V1\Rest\Users;

class UsersResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('Users\V1\Rest\Users\UsersMapper');
        return new UsersResource($mapper);
    }
}
