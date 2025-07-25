<?php

namespace Container2KXDDT6;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDoctrineMongodb_Odm_DefaultConnectionService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'doctrine_mongodb.odm.default_connection' shared service.
     *
     * @return \MongoDB\Client
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/mongodb/mongodb/src/Client.php';

        return $container->services['doctrine_mongodb.odm.default_connection'] = new \MongoDB\Client($container->getEnv('resolve:MONGODB_URL'), [], ['typeMap' => ['root' => 'array', 'document' => 'array'], 'driver' => ['name' => 'symfony-mongodb', 'version' => '2.11.2']]);
    }
}
