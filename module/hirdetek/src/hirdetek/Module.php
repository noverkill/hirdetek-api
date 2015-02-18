<?php
namespace hirdetek;

use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'ZF\Apigility\Autoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
      return array(
        'factories' => array(
          'hirdetek\V1\Rest\Hirdetes\HirdetesMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Hirdetes\HirdetesMapper($adapter);
          },
          'hirdetek\V1\Rest\Hirdetes\KepMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Kep\KepMapper($adapter);
          },
          'hirdetek\V1\Rest\Rovatok\RovatokMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Rovatok\RovatokMapper($adapter);
          },
          'hirdetek\V1\Rest\Regio\RegioMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Regio\RegioMapper($adapter);
          },
          'hirdetek\V1\Rest\Megosztas\MegosztasMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Megosztas\MegosztasMapper($adapter);
          },
          'hirdetek\V1\Rest\Megosztas\KedvencekMapper' =>  function ($sm) {
            $adapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new \hirdetek\V1\Rest\Kedvencek\KedvencekMapper($adapter);
          }
        )
      );
    }
}
