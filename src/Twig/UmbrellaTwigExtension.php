<?php declare(strict_types=1);

namespace Torr\Umbrella\Twig;

use Torr\Umbrella\Renderer\ComponentRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UmbrellaTwigExtension extends AbstractExtension
{
	private ComponentRenderer $componentRenderer;

	/**
	 */
	public function __construct (ComponentRenderer $componentRenderer)
	{
		$this->componentRenderer = $componentRenderer;
	}


	/**
	 * @inheritDoc
	 */
	public function getFunctions () : array
	{
		return [
			new TwigFunction("umbrella", [$this->componentRenderer, "renderEmbedded"], ["is_safe" => ["html"]]),
		];
	}
}
