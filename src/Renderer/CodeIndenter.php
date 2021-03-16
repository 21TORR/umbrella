<?php
declare(strict_types=1);

namespace Torr\Umbrella\Renderer;

use Gajus\Dindent\Indenter;

final class CodeIndenter
{
	private ?Indenter $indenter = null;

	private const SVG_ELEMENTS = [
		"circle",
		"clipPath",
		"defs",
		"foreignObject",
		"g",
		"line",
		"linearGradient",
		"mask",
		"path",
		"path",
		"polygon",
		"rect",
		"svg",
		"text",
		"textPath",
		"tspan",
		"use",
	];


	/**
	 * Prepares the indention library
	 */
	private function getIndenter () : Indenter
	{
		if (null === $this->indenter)
		{
			$this->indenter = new Indenter([
				"indentation_character" => "    ",
			]);

			foreach (self::SVG_ELEMENTS as $svgElement)
			{
				$this->indenter->setElementType($svgElement, Indenter::ELEMENT_TYPE_INLINE);
			}
		}

		dump($this->indenter);
		return $this->indenter;
	}


	/**
	 * Indents the code
	 */
	public function indent (string $html) : string
	{
		return $this->getIndenter()->indent($html);
	}
}
