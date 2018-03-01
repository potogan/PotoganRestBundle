<?php

namespace Potogan\RESTBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('potogan_rest');

        $rootNode
            ->children()
                ->scalarNode('http_client')
                    ->info('Provide any php-http/client-implementation service id.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('request_factory')
                    ->info('Provide any RequestFactory implementation as defined in php-http/message-factory.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('stream_factory')
                    ->info('Provide any StreamFactory implementation as defined in php-http/message-factory.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()

                ->arrayNode('uri_class_map')
                    ->normalizeKeys(false)
                    ->ignoreExtraKeys()

                    ->scalarPrototype()
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
