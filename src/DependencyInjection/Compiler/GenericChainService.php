<?php

namespace Potogan\RESTBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;

/**
 * Generic chain service CompilerPass.
 *
 * Any service tagged with "potogan.rest.chain" (tag, method[, aliasField]) will be injected
 *    services tagged with "tag" throught its "method" method. If "aliasField" is set, then an
 *    alias will be taken from the subscribing tag attribute named as aliasField value, and
 *    given to method as a second argument.
 *
 * @author popy <popy.dev@gmail.com>
 */
class GenericChainService implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $chainServices = $container->findTaggedServiceIds('potogan.rest.chain');
        $watchlist = array();

        // Build a tag watchlist
        foreach ($chainServices as $id => $tags) {
            $service = $container->getDefinition($id);

            foreach ($tags as $attributes) {
                if (!isset($attributes['tag']) || !isset($attributes['method'])) {
                    throw new BadMethodCallException(sprintf(
                        'Bad "mkg_common.chain" service tag definition. Attributes tag & method are required (in "%s" service definition).',
                        $id
                    ));
                }

                $watchlist[$attributes['tag']][] = (object)array(
                    'service' => $service,
                    'method' => $attributes['method'],
                    'aliasField' => isset($attributes['aliasField']) ? $attributes['aliasField'] : null,
                );
            }
        }

        // Browse the whole list of services definitions and maps watched tags to their destination(s).
        foreach ($container->getDefinitions() as $id => $definition) {
            $intersect = array_intersect_key($watchlist, $definition->getTags());

            foreach ($intersect as $tagname => $watchers) {
                foreach ($watchers as $watcher) {
                    foreach ($definition->getTag($tagname) as $attributes) {
                        $args = array($definition);

                        if ($watcher->aliasField && !isset($attributes[$watcher->aliasField])) {
                            throw new BadMethodCallException(sprintf(
                                'Bad "%s" service tag definition for service "%s". Attribute "%s" is required (as requested by "%s" service).',
                                $tagname,
                                $id,
                                $watcher->aliasField,
                                $watcher->service->getId()
                            ));
                        }

                        if ($watcher->aliasField) {
                            $args[] = $attributes[$watcher->aliasField];
                        }

                        $watcher->service->addMethodCall($watcher->method, $args);
                    }
                }
            }
        }
    }
}
