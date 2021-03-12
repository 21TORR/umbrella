<?php declare(strict_types=1);

namespace Torr\Umbrella\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class UmbrellaBundleConfiguration implements ConfigurationInterface
{
	/**
	 * @inheritDoc
	 */
	public function getConfigTreeBuilder () : TreeBuilder
	{
		$tree = new TreeBuilder("umbrella");

		$tree->getRootNode()
			->children()
				->scalarNode("templates_directory")
					->defaultValue("_layout")
					->info("The path to the layout templates directory. Relative to the project dir.")
				->end()
				->arrayNode("assets")
					->scalarPrototype()->end()
					->defaultValue([])
					->info("All the assets that need to be loaded in the previews.")
				->end()
				->booleanNode("enabled_in_production")
					->defaultValue(false)
					->info("Whether umbrella should be enabled in production.")
				->end()
			->end();

		return $tree;
	}
}
