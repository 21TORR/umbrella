<?php declare(strict_types=1);

namespace Torr\Umbrella\Component\Library;

use Torr\Umbrella\Data\CategoryData;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Exception\MissingCategoryException;

/**
 */
final class ComponentLibrary
{
	/** @var array<string, CategoryData> */
	private array $categories;
	private string $templateDir;

	/**
	 * @param array<string, CategoryData> $categories
	 */
	public function __construct (
		string $baseDir,
		array $categories
	)
	{
		$this->templateDir = $baseDir;
		$this->categories = $categories;
	}

	/**
	 * @return CategoryData[]
	 */
	public function getCategories () : array
	{
		return $this->categories;
	}

	/**
	 */
	public function getCategory (string $category) : CategoryData
	{
		$categoryData = $this->categories[$category] ?? null;

		if (null === $categoryData)
		{
			throw new MissingCategoryException(\sprintf("Can't find category '%s'", $category));
		}

		return $categoryData;
	}

	/**
	 * Builds a full template include path
	 */
	public function getTemplatePath (CategoryData $categoryData, ComponentData $componentData) : string
	{
		return "{$this->templateDir}/{$categoryData->getKey()}/{$componentData->getKey()}.html.twig";
	}
}
