<?php

declare(strict_types=1);

namespace Phpro\SuluMessengerFailedQueueBundle\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function Psl\Type\non_empty_string;
use function Psl\Type\shape;

class SuluMessengerFailedQueueExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'lists' => [
                        'directories' => [
                            __DIR__.'/../../../../config/lists',
                        ],
                    ],
                    'resources' => [
                        'phpro_messenger_failed_queue' => [
                            'routes' => [
                                'list' => 'phpro.messenger_failed_queue_list',
                                'detail' => 'phpro.messenger_failed_queue_fetch',
                            ],
                        ],
                    ],
                ]
            );
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../../config/services'));
        $loader->load('commands.yaml');
        $loader->load('controllers.yaml');
        $loader->load('query.yaml');
        $loader->load('repositories.yaml');
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = shape([
            'failure_transport_service' => non_empty_string(),
            'failure_transport_table' => non_empty_string(),
            'failure_transport_queue_name' => non_empty_string(),
        ])->assert($this->processConfiguration($configuration, $configs));

        $container->setAlias(
            'sulu_messenger_failed_queue.transport',
            $config['failure_transport_service']
        );

        $container->setParameter('sulu_messenger_failed_queue.table', $config['failure_transport_table']);
        $container->setParameter('sulu_messenger_failed_queue.queue_name', $config['failure_transport_queue_name']);
    }
}
