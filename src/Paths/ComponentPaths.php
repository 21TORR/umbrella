<?php
declare(strict_types=1);

namespace Torr\Umbrella\Paths;

use Torr\Umbrella\Data\ComponentData;

final class ComponentPaths
{
	private string $templatesDir;

	public function __construct (string $templatesDir)
	{
		$this->templatesDir = $templatesDir;
	}

	/**
	 * Builds a full template include path
	 */
	public function getTwigTemplatePath (ComponentData $component) : string
	{
		return "{$this->templatesDir}/{$component->toPath()}.html.twig";
	}


	/**
	 * Builds a full docs include path
	 */
	public function getTwigDocsPath (ComponentData $component) : string
	{
		return "{$this->templatesDir}/{$component->toPath()}.md";
	}
}
