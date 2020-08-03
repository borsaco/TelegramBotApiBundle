<?php

namespace Borsaco\TelegramBotApiBundle\DependencyInjection;

use App\Kernel;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
    	$builder = new TreeBuilder('telegram_bot_api');
        $rootNode = \method_exists($builder, 'getRootNode') ? $builder->getRootNode() : $builder->root('telegram_bot_api');

	$rootNode
	    	->children()
		->arrayNode('bots')
		    ->useAttributeAsKey('name')
		    ->prototype('array')
			->children()
			    ->scalarNode( 'token' )->isRequired()->end()
                ->booleanNode('maintenance')->defaultFalse()->end()
			->end()
		    ->end()
		->end()
		->scalarNode('default')->defaultNull()->end()
		->scalarNode('proxy')->defaultNull()->end()
		->booleanNode('async_requests')->defaultFalse()->end()
		->arrayNode('development')
		    ->children()
			->arrayNode('developers_id')->prototype('scalar')->end()
		    ->end()
			->arrayNode('maintenance')
			    ->children()
				->scalarNode('text')->defaultValue('The robot is being repaired! Please come back later.')->end()
			    ->end()
			->end()
		    ->end()
		->end()
        ->end();

        return $builder;
    }

}
