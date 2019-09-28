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
    	if (Kernel::VERSION_ID >= 40200) {
            $builder = new TreeBuilder('telegram_bot_api');
        } else {
            $builder = new TreeBuilder();
        }
        $rootNode = \method_exists($builder, 'getRootNode') ? $builder->getRootNode() : $builder->root('telegram_bot_api');

	$rootNode
	    	->children()
		->arrayNode('bots')
		    ->useAttributeAsKey('name')
		    ->prototype('array')
			->children()
			    ->scalarNode( 'token' )->isRequired()->end()
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
				->booleanNode('enable')->defaultFalse()->end()
				->scalarNode('text')->defaultValue('The robot is being repaired! Please come back later.')->end()
			    ->end()
			->end()
		    ->end()
		->end()
        ->end();

        return $builder;
    }

}
