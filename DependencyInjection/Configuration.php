<?php
namespace Bcremer\Sculpin\Bundle\LessBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
    * {@inheritdoc}
    */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;

        $rootNode = $treeBuilder->root('sculpin_less');

        $rootNode
            ->children()
                ->arrayNode('extensions')
                    ->defaultValue(['less'])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('files')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
