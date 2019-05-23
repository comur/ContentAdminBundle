<?php


namespace Comur\ContentAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('comur_content_admin');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('locales')
                    ->prototype('scalar')
                    ->defaultValue(['en'])
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
