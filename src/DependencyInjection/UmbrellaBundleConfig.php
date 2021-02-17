<?php declare(strict_types=1);

namespace Torr\Umbrella\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class UmbrellaBundleConfig implements ConfigurationInterface
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
			->end();

		return $tree;
	}
}
