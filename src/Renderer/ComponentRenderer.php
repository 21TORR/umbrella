<?php declare(strict_types=1);

namespace Torr\Umbrella\Renderer;

use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Paths\UmbrellaPaths;
use Twig\Environment;

final class ComponentRenderer
{
	private ComponentLibraryLoader $libraryLoader;
	private Environment $twig;
	private UmbrellaPaths $paths;
	private CodeFormatter $formatter;

	/**
	 */
	public function __construct (
		ComponentLibraryLoader $libraryLoader,
		Environment $twig,
		UmbrellaPaths $paths,
		CodeFormatter $formatter
	)
	{
		$this->libraryLoader = $libraryLoader;
		$this->twig = $twig;
		$this->paths = $paths;
		$this->formatter = $formatter;
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
		array $variables = [],
		bool $commentOutput = false
	) : string
	{
		return $this->render($category, $component, false, $variables, $commentOutput);
	}

	/**
	 * Renders the beautified code
	 */
	public function renderForCodeView (
		string $category,
		string $component
	) : string
	{
		$html = $this->render($category, $component, true, [], true);
		return $this->formatter->format($html);
	}


	/**
	 */
	private function render (
		string $category,
		string $component,
		bool $standalone,
		array $variables = [],
		bool $commentOutput = false
	) : string
	{
		$library = $this->libraryLoader->loadLibrary();
		$componentData = $library->getCategory($category)->getComponent($component);

		$html = \trim($this->twig->render(
			$this->paths->getTwigTemplatePath($componentData),
			\array_replace($variables, [
				"standalone" => $standalone,
				"__umbrella_code_view" => $commentOutput,
			])
		));

		if ($commentOutput && !$standalone)
		{
			return "<!-- Component {$category}/{$component} -->\n" . $html . "\n<!-- / End Component {$category}/{$component} -->";
		}

		return $html;
	}
}
