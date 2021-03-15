<?php declare(strict_types=1);

namespace Torr\Umbrella\Renderer;

use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Paths\ComponentPaths;
use Twig\Environment;

final class ComponentRenderer
{
	private ComponentLibraryLoader $libraryLoader;
	private Environment $twig;
	private ComponentPaths $paths;

	/**
	 */
	public function __construct (
		ComponentLibraryLoader $libraryLoader,
		Environment $twig,
		ComponentPaths $paths
	)
	{
		$this->libraryLoader = $libraryLoader;
		$this->twig = $twig;
		$this->paths = $paths;
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
		$componentData = $library->getCategory($category)->getComponent($component);

		return $this->twig->render(
			$this->paths->getTwigTemplatePath($componentData),
			\array_replace($variables, [
				"standalone" => $standalone,
			])
		);
	}
}
