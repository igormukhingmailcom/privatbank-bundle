<?php

namespace Mukhin\PrivatbankBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mukhin_privatbank');
        $rootNode
            ->fixXmlConfig('merchant')
            ->children()
                ->arrayNode('merchants')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('merchant_id')
                                ->info('Merchant ID')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('merchant_secret')
                                ->info('Merchant Secret')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('card_number')
                                ->info('Card number associated with given Merchant')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
