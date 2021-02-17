<?php declare(strict_types=1);

namespace Torr\Umbrella\Twig;

use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Renderer\ComponentRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UmbrellaTwigExtension extends AbstractExtension
{
	private ComponentRenderer $componentRenderer;
	private ComponentLibraryLoader $libraryLoader;

	/**
	 */
	public function __construct (
		ComponentRenderer $componentRenderer,
		ComponentLibraryLoader $libraryLoader
	)
	{
		$this->componentRenderer = $componentRenderer;
		$this->libraryLoader = $libraryLoader;
	}

	/**
	 */
	public function getUmbrellaTemplate (string $category, string $component) : string
	{
		$library = $this->libraryLoader->loadLibrary();
		$categoryData = $library->getCategory($category);
		$componentData = $categoryData->getComponent($component);

		return $library->getTemplatePath($categoryData, $componentData);
	}


	/**
	 * @inheritDoc
	 */
	public function getFunctions () : array
	{
		return [
			new TwigFunction("umbrella", [$this->componentRenderer, "renderEmbedded"], ["is_safe" => ["html"]]),
			new TwigFunction("umbrella_template", [$this, "getUmbrellaTemplate"]),
		];
	}
}
