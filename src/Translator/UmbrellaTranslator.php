<?php declare(strict_types=1);

namespace Torr\Umbrella\Translator;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @final
 */
class UmbrellaTranslator
{
	private TranslatorInterface $translator;
	private LabelGenerator $labelGenerator;

	public function __construct (
		TranslatorInterface $translator,
		LabelGenerator $labelGenerator
	)
	{
		$this->translator = $translator;
		$this->labelGenerator = $labelGenerator;
	}


	/**
	 * Translates a category
	 */
	public function translateCategory (string $category) : string
	{
		return $this->translate("category.%s", $category);
	}


	/**
	 * Translates a component name
	 */
	public function translateComponent (string $category, string $component) : string
	{
		return $this->translate("component.{$category}.%s", $component);
	}


	/**
	 */
	private function translate (string $pattern, string $key) : string
	{
		$id = \sprintf($pattern, $key);
		$translated = $this->translator->trans($id, [], "umbrella");

		return $translated !== $id
			? $translated
			: $this->labelGenerator->generate($key);
	}
}
