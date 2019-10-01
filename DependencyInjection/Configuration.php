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
                ->scalarNode('templates_parameter')->defaultValue('comur_content_admin.templates')->end()
                ->scalarNode('entity_name')->defaultValue('page')->end()
                ->arrayNode('locales')
                    ->prototype('scalar')
                    ->defaultValue(['en'])
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->end()
                            ->scalarNode('controller')->defaultNull()->end()
                            ->arrayNode('controllerParams')
                                ->prototype('scalar')
                                ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('enable_comur_image_bundle')
                    ->defaultFalse()
                ->end()
                ->arrayNode('editable_tags')
                    ->prototype('scalar')
                    ->defaultValue([
                        'ul',
                        'i',
                        'li',
                        'span',
                        'td',
                        'th'
                    ])
                    ->end()
                ->end()
                ->booleanNode('show_image_size')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
