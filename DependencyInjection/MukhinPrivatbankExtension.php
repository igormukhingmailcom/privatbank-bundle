<?php

namespace Mukhin\PrivatbankBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MukhinPrivatbankExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['merchants'] as $name=>$parameters) {
            $merchantDefinition = new Definition(
                $container->getParameter('mukhin_privatbank.merchant.class'),
                [$parameters['merchant_id'], $parameters['merchant_secret'], $parameters['card_number']]
            );
            $merchantDefinition->addTag('mukhin_privatbank.merchant');
            $container->setDefinition(
                sprintf('mukhin_privatbank.merchant.%s', $name),
                $merchantDefinition
            );
        }
    }
}
