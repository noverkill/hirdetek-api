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
            'hirdetek.rest.rovatok' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/rovatok[/:rovatok_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Rovatok\\Controller',
                    ),
                ),
            ),
            'hirdetek.rest.regio' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/regio[/:regio_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Regio\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'hirdetek.rest.hirdetes',
            1 => 'hirdetek.rest.rovatok',
            2 => 'hirdetek.rest.regio',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResource' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResourceFactory',
            'hirdetek\\V1\\Rest\\Rovatok\\RovatokResource' => 'hirdetek\\V1\\Rest\\Rovatok\\RovatokResourceFactory',
            'hirdetek\\V1\\Rest\\Regio\\RegioResource' => 'hirdetek\\V1\\Rest\\Regio\\RegioResourceFactory',
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
            'collection_query_whitelist' => array(
                0 => 'search',
                1 => 'rovat',
                2 => 'regio',
                3 => 'minar',
                4 => 'maxar',
            ),
            'page_size' => 25,
            'page_size_param' => 'ps',
            'entity_class' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesCollection',
            'service_name' => 'hirdetes',
        ),
        'hirdetek\\V1\\Rest\\Rovatok\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Rovatok\\RovatokResource',
            'route_name' => 'hirdetek.rest.rovatok',
            'route_identifier_name' => 'rovatok_id',
            'collection_name' => 'rovatok',
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
            'page_size_param' => 'ps',
            'entity_class' => 'hirdetek\\V1\\Rest\\Rovatok\\RovatokEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Rovatok\\RovatokCollection',
            'service_name' => 'rovatok',
        ),
        'hirdetek\\V1\\Rest\\Regio\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Regio\\RegioResource',
            'route_name' => 'hirdetek.rest.regio',
            'route_identifier_name' => 'regio_id',
            'collection_name' => 'regio',
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
            'page_size_param' => 'ps',
            'entity_class' => 'hirdetek\\V1\\Rest\\Regio\\RegioEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Regio\\RegioCollection',
            'service_name' => 'regio',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Regio\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Regio\\Controller' => array(
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
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Regio\\Controller' => array(
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
            'hirdetek\\V1\\Rest\\Rovatok\\RovatokEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.rovatok',
                'route_identifier_name' => 'rovatok_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Rovatok\\RovatokCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.rovatok',
                'route_identifier_name' => 'rovatok_id',
                'is_collection' => true,
            ),
            'hirdetek\\V1\\Rest\\Regio\\RegioEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.regio',
                'route_identifier_name' => 'regio_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Regio\\RegioCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.regio',
                'route_identifier_name' => 'regio_id',
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
