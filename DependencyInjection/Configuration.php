<?php

namespace Puzzle\App\BlogBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('puzzle_app_blog');

        $rootNode
            ->children()
                ->scalarNode('title')->defaultValue('blog.title')->end()
                ->scalarNode('description')->defaultValue('blog.description')->end()
                ->scalarNode('icon')->defaultValue('blog.icon')->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('article')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('list')->defaultValue('PuzzleAppBlogBundle:Article:list.html.twig')->end()
                                ->scalarNode('show')->defaultValue('PuzzleAppBlogBundle:Article:show.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('list')->defaultValue('PuzzleAppBlogBundle:Category:list.html.twig')->end()
                                ->scalarNode('show')->defaultValue('PuzzleAppBlogBundle:Catgeory:show.html.twig')->end()
                            ->end()
                        ->end()
                        ->arrayNode('comment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('list')->defaultValue('PuzzleAppBlogBundle:Comment:list.html.twig')->end()
                                ->scalarNode('create')->defaultValue('PuzzleAppBlogBundle:Comment:create.html.twig')->end()
                                ->scalarNode('show')->defaultValue('PuzzleAppBlogBundle:Comment:show.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
