<?php
return array(
    'router' => array(
        'routes' => array(
            'hirdetek.rest.hirdetes' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/hirdetes[/:hirdetes_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Hirdetes\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'hirdetek.rest.hirdetes',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResource' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResourceFactory',
        ),
    ),
    'zf-rest' => array(
        'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResource',
            'route_name' => 'hirdetek.rest.hirdetes',
            'route_identifier_name' => 'hirdetes_id',
            'collection_name' => 'hirdetes',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesCollection',
            'service_name' => 'hirdetes',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.hirdetes',
                'route_identifier_name' => 'hirdetes_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.hirdetes',
                'route_identifier_name' => 'hirdetes_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-mvc-auth' => array(
        'authorization' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
                'entity' => array(
                    'GET' => false,
                    'POST' => true,
                    'PATCH' => true,
                    'PUT' => true,
                    'DELETE' => true,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => true,
                    'PATCH' => true,
                    'PUT' => true,
                    'DELETE' => true,
                ),
            ),
        ),
    ),
);
