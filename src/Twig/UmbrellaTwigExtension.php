<?php declare(strict_types=1);

namespace Torr\Umbrella\Twig;

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
	public function renderUmbrellaVariations (string $category, string $component, array $variations) : string
	{
		$contexts = (new ContextVariations())->generateVariationsContexts($variations);
		$element = new HtmlElement("div", ["class" => "umbrella-variations"]);

		foreach ($contexts as $context)
		{
			$element->append(
				new HtmlElement(
					"div",
					["class" => "umbrella-variation"],
					[new SafeMarkup($this->componentRenderer->renderEmbedded($category, $component, $context))]
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
		$safeHtml = ["is_safe" => ["html"]];

		return [
			new TwigFunction("umbrella", [$this->componentRenderer, "renderEmbedded"], $safeHtml),
			new TwigFunction("umbrella_template", [$this, "getUmbrellaTemplate"]),
			new TwigFunction("umbrella_variations", [$this, "renderUmbrellaVariations"], $safeHtml),
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
