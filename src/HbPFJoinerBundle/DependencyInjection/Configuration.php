<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFJoinerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Hanaboso\PipesPhpSdk\HbPFJoinerBundle\DependencyInjection
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('hbpf');

        $rootNode->children()
            ->arrayNode('joiner');

        return $treeBuilder;
    }

}
