<?php declare(strict_types=1);

namespace Torr\Umbrella\Twig;

use League\CommonMark\MarkdownConverter;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Torr\HtmlBuilder\Builder\HtmlBuilder;
use Torr\HtmlBuilder\Node\HtmlElement;
use Torr\HtmlBuilder\Text\SafeMarkup;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Paths\ComponentPaths;
use Torr\Umbrella\Renderer\ComponentRenderer;
use Torr\Umbrella\Variations\ContextVariations;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UmbrellaTwigExtension extends AbstractExtension implements ServiceSubscriberInterface
{
	private ContainerInterface $locator;
	private ComponentRenderer $componentRenderer;
	private ComponentLibraryLoader $libraryLoader;

	/**
	 */
	public function __construct (
		ContainerInterface $locator,
		ComponentRenderer $componentRenderer,
		ComponentLibraryLoader $libraryLoader
	)
	{
		$this->locator = $locator;
		$this->componentRenderer = $componentRenderer;
		$this->libraryLoader = $libraryLoader;
	}


	public function renderComponent (
		array $context,
		string $category,
		string $component,
		array $variables = []
	) : string
	{
		$isCodeView = true === ($context["__umbrella_code_view"] ?? false);

		return $this->componentRenderer->renderEmbedded(
			$category,
			$component,
			$variables,
			$isCodeView
		);
	}

	/**
	 */
	public function getUmbrellaTemplate (string $category, string $component) : string
	{
		/** @var ComponentPaths $paths */
		$paths = $this->locator->get(ComponentPaths::class);
		$library = $this->libraryLoader->loadLibrary();
		$componentData = $library->getCategory($category)->getComponent($component);

		return $paths->getTwigTemplatePath($componentData);
	}

	/**
	 * Renders all umbrella variations
	 */
	public function renderUmbrellaVariations (
		array $context,
		string $category,
		string $component,
		array $variations
	) : string
	{
		$isCodeView = true === ($context["__umbrella_code_view"] ?? false);
		$contexts = (new ContextVariations())->generateVariationsContexts($variations);
		$element = new HtmlElement("div", ["class" => "umbrella-variations"]);

		if ($isCodeView)
		{
			$lines = [];

			foreach ($contexts as $templateContext)
			{
				$lines[] = $this->componentRenderer->renderEmbedded(
					$category,
					$component,
					$templateContext,
					true
				);
			}

			return \implode("\n", $lines);
		}

		foreach ($contexts as $templateContext)
		{
			$element->append(
				new HtmlElement(
					"div",
					["class" => "umbrella-variation"],
					[new SafeMarkup($this->componentRenderer->renderEmbedded(
						$category,
						$component,
						$templateContext
					))]
				)
			);
		}

		return (new HtmlBuilder())->build($element);
	}


	/**
	 * @inheritDoc
	 */
	public function getFunctions () : array
	{
		$renderOptions = [
			"is_safe" => ["html"],
			"needs_context" => true,
		];

		return [
			new TwigFunction("umbrella", [$this, "renderComponent"], $renderOptions),
			new TwigFunction("umbrella_template", [$this, "getUmbrellaTemplate"]),
			new TwigFunction("umbrella_variations", [$this, "renderUmbrellaVariations"], $renderOptions),
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function getSubscribedServices () : array
	{
		return [
			ComponentPaths::class,
		];
	}
}
