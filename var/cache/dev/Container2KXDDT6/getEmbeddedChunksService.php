<?php

namespace Container2KXDDT6;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getEmbeddedChunksService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Command\EmbeddedChunks' shared autowired service.
     *
     * @return \App\Command\EmbeddedChunks
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/src/Command/EmbeddedChunks.php';

        $container->privates['App\\Command\\EmbeddedChunks'] = $instance = new \App\Command\EmbeddedChunks(($container->services['doctrine_mongodb.odm.default_document_manager'] ?? $container->load('getDoctrineMongodb_Odm_DefaultDocumentManagerService')), $container->getEnv('VOYAGE_API_KEY'), $container->getEnv('VOYAGE_ENDPOINT'), $container->getEnv('BATCH_SIZE'), $container->getEnv('MAX_RETRIES'), ($container->privates['http_client.uri_template'] ?? $container->load('getHttpClient_UriTemplateService')));

        $instance->setName('app:embed-chunks');
        $instance->setDescription('This command will create embedding for the stored chunks using Voyage AI API key and store into MongoDB database');

        return $instance;
    }
}
