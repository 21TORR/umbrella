<?php declare(strict_types=1);

namespace Torr\Umbrella\Renderer;

use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Twig\Environment;

final class ComponentRenderer
{
	private ComponentLibraryLoader $libraryLoader;
	private Environment $twig;

	/**
	 */
	public function __construct (ComponentLibraryLoader $libraryLoader, Environment $twig)
	{
		$this->libraryLoader = $libraryLoader;
		$this->twig = $twig;
	}


	/**
	 */
	public function renderStandalone (
		string $category,
		string $component,
		array $variables = []
	) : string
	{
		return $this->render($category, $component, true, $variables);
	}


	/**
	 */
	public function renderEmbedded (
		string $category,
		string $component,
		array $variables = []
	) : string
	{
		return $this->render($category, $component, false, $variables);
	}


	/**
	 */
	private function render (
		string $category,
		string $component,
		bool $standalone,
		array $variables = []
	) : string
	{
		$library = $this->libraryLoader->loadLibrary();
		$categoryData = $library->getCategory($category);
		$componentData = $categoryData->getComponent($component);

		return $this->twig->render(
			$library->getTemplatePath($categoryData, $componentData),
			\array_replace($variables, [
				"standalone" => $standalone,
			])
		);
	}
}
