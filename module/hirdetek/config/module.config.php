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
            'hirdetek.rest.megosztas' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/megosztas[/:megosztas_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Megosztas\\Controller',
                    ),
                ),
            ),
            'hirdetek.rest.kedvencek' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/kedvencek[/:kedvencek_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Kedvencek\\Controller',
                    ),
                ),
            ),
            'hirdetek.rest.kep' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/kep[/:kep_id]',
                    'defaults' => array(
                        'controller' => 'hirdetek\\V1\\Rest\\Kep\\Controller',
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
            3 => 'hirdetek.rest.megosztas',
            4 => 'hirdetek.rest.kedvencek',
            5 => 'hirdetek.rest.kep',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResource' => 'hirdetek\\V1\\Rest\\Hirdetes\\HirdetesResourceFactory',
            'hirdetek\\V1\\Rest\\Rovatok\\RovatokResource' => 'hirdetek\\V1\\Rest\\Rovatok\\RovatokResourceFactory',
            'hirdetek\\V1\\Rest\\Regio\\RegioResource' => 'hirdetek\\V1\\Rest\\Regio\\RegioResourceFactory',
            'hirdetek\\V1\\Rest\\Megosztas\\MegosztasResource' => 'hirdetek\\V1\\Rest\\Megosztas\\MegosztasResourceFactory',
            'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekResource' => 'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekResourceFactory',
            'hirdetek\\V1\\Rest\\Kep\\KepResource' => 'hirdetek\\V1\\Rest\\Kep\\KepResourceFactory',
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
                5 => 'ord',
                6 => 'ordir',
                7 => 'userid',
            ),
            'page_size' => 50,
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
            'page_size' => 50,
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
            'page_size' => 50,
            'page_size_param' => 'ps',
            'entity_class' => 'hirdetek\\V1\\Rest\\Regio\\RegioEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Regio\\RegioCollection',
            'service_name' => 'regio',
        ),
        'hirdetek\\V1\\Rest\\Megosztas\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Megosztas\\MegosztasResource',
            'route_name' => 'hirdetek.rest.megosztas',
            'route_identifier_name' => 'megosztas_id',
            'collection_name' => 'megosztas',
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
            'page_size' => 50,
            'page_size_param' => null,
            'entity_class' => 'hirdetek\\V1\\Rest\\Megosztas\\MegosztasEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Megosztas\\MegosztasCollection',
            'service_name' => 'megosztas',
        ),
        'hirdetek\\V1\\Rest\\Kedvencek\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekResource',
            'route_name' => 'hirdetek.rest.kedvencek',
            'route_identifier_name' => 'kedvencek_id',
            'collection_name' => 'kedvencek',
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
            'page_size' => 50,
            'page_size_param' => null,
            'entity_class' => 'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekCollection',
            'service_name' => 'kedvencek',
        ),
        'hirdetek\\V1\\Rest\\Kep\\Controller' => array(
            'listener' => 'hirdetek\\V1\\Rest\\Kep\\KepResource',
            'route_name' => 'hirdetek.rest.kep',
            'route_identifier_name' => 'kep_id',
            'collection_name' => 'kep',
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
            'page_size' => 50,
            'page_size_param' => null,
            'entity_class' => 'hirdetek\\V1\\Rest\\Kep\\KepEntity',
            'collection_class' => 'hirdetek\\V1\\Rest\\Kep\\KepCollection',
            'service_name' => 'kep',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Regio\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Megosztas\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Kedvencek\\Controller' => 'HalJson',
            'hirdetek\\V1\\Rest\\Kep\\Controller' => 'HalJson',
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
            'hirdetek\\V1\\Rest\\Megosztas\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Kedvencek\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Kep\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
                2 => 'image/jpeg',
            ),
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Regio\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Megosztas\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Kedvencek\\Controller' => array(
                0 => 'application/vnd.hirdetek.v1+json',
                1 => 'application/json',
            ),
            'hirdetek\\V1\\Rest\\Kep\\Controller' => array(
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
            'hirdetek\\V1\\Rest\\Megosztas\\MegosztasEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.megosztas',
                'route_identifier_name' => 'megosztas_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Megosztas\\MegosztasCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.megosztas',
                'route_identifier_name' => 'megosztas_id',
                'is_collection' => true,
            ),
            'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.kedvencek',
                'route_identifier_name' => 'kedvencek_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Kedvencek\\KedvencekCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.kedvencek',
                'route_identifier_name' => 'kedvencek_id',
                'is_collection' => true,
            ),
            'hirdetek\\V1\\Rest\\Kep\\KepEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.kep',
                'route_identifier_name' => 'kep_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'hirdetek\\V1\\Rest\\Kep\\KepCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'hirdetek.rest.kep',
                'route_identifier_name' => 'kep_id',
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
                    'POST' => false,
                    'PATCH' => true,
                    'PUT' => true,
                    'DELETE' => true,
                ),
            ),
            'hirdetek\\V1\\Rest\\Rovatok\\Controller' => array(
                'entity' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'hirdetek\\V1\\Rest\\Regio\\Controller' => array(
                'entity' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'hirdetek\\V1\\Rest\\Megosztas\\Controller' => array(
                'entity' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'hirdetek\\V1\\Rest\\Kedvencek\\Controller' => array(
                'entity' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
        ),
    ),
    'zf-content-validation' => array(
        'hirdetek\\V1\\Rest\\Hirdetes\\Controller' => array(
            'input_filter' => 'hirdetek\\V1\\Rest\\Hirdetes\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'hirdetek\\V1\\Rest\\Hirdetes\\Validator' => array(
            0 => array(
                'name' => 'file',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Uploaded imag',
                'allow_empty' => true,
                'continue_if_empty' => true,
                'error_message' => 'Nem sikerült feltölteni a képet',
                'type' => 'Zend\\InputFilter\\FileInput',
            ),
        ),
    ),
);
