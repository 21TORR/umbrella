<?php declare(strict_types=1);

namespace Torr\Umbrella\Component\Library;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Stopwatch\Stopwatch;
use Torr\Umbrella\Data\CategoryData;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Translator\UmbrellaTranslator;

final class ComponentLibraryLoader
{
	private string $templatesDir;
	private string $baseDir;
	private UmbrellaTranslator $translator;
	private ?Stopwatch $stopwatch;
	private ?ComponentLibrary $library = null;

	public function __construct (
		UmbrellaTranslator $translator,
		?Stopwatch $stopwatch,
		string $templatesDir,
		string $subDir
	)
	{
		$this->translator = $translator;
		$this->templatesDir = \trim($subDir, "/");
		$this->baseDir = \rtrim($templatesDir, "/") . "/{$this->templatesDir}";
		$this->stopwatch = $stopwatch;
	}


	/**
	 */
	public function loadLibrary () : ComponentLibrary
	{
		if (null === $this->library)
		{
			$this->library = $this->regenerateLibrary();
		}

		return $this->library;
	}


	/**
	 */
	public function regenerateLibrary () : ComponentLibrary
	{
		try
		{
			if ($this->stopwatch)
			{
				$this->stopwatch->start("Umbrella: load library");
			}

			$finder = Finder::create()
				->in($this->baseDir)
				->files()
				->ignoreDotFiles(true)
				->ignoreUnreadableDirs()
				// this regex is doing a lot of heavy lifting here:
				// 1. specify which types of directory names are allowed
				// 2. specify which types of file names are allowed
				// 3. the templates must end in ".html.twig"
				// 4. Only templates exactly one level under the base dir are allowed (so in a single sub dir)
				->path('~^[a-z0-9][a-z0-9-_]*\/[a-z0-9_][a-z0-9-_]*\.html\.twig$~i')
			;

			$components = $this->fetchComponentsFromFinder($finder);
			$categories = $this->bundleCategoriesFromComponentList($components);

			if ($this->stopwatch)
			{
				$this->stopwatch->stop("Umbrella: load library");
			}

			return new ComponentLibrary(
				$this->templatesDir,
				$categories
			);
		}
		catch (DirectoryNotFoundException $exception)
		{
			return new ComponentLibrary(
				$this->templatesDir,
				[]
			);
		}
	}

	/**
	 * @param Finder $finder
	 * @return array<string, array<string,ComponentData>>
	 */
	private function fetchComponentsFromFinder (Finder $finder) : array
	{
		$components = [];

		foreach ($finder as $file)
		{
			$type = \dirname($file->getRelativePathname());
			$name = \basename($file->getRelativePathname(), ".html.twig");
			$hidden = false;

			if ($name[0] === "_")
            {
                $name = \substr($name, 1);
                $hidden = true;
            }

			$components[$type][$name] = new ComponentData(
				$name,
				$this->translator->translateComponent($type, $name),
                $hidden
			);
		}

		return $components;
	}


	/**
	 * @param array<string, array<string,ComponentData>> $components
	 * @return array<string, CategoryData>
	 */
	private function bundleCategoriesFromComponentList (array $components) : array
	{
		$categories = [];

		foreach ($components as $categoryKey => $componentsData)
		{
			\uasort(
				$componentsData,
				static fn (ComponentData $left, ComponentData $right) => \strnatcasecmp($left->getLabel(), $right->getLabel())
			);

			$categories[$categoryKey] = new CategoryData(
				$categoryKey,
				$this->translator->translateCategory($categoryKey),
				$componentsData
			);
		}

		\uasort(
			$categories,
			static fn (CategoryData $left, CategoryData $right) => \strnatcasecmp($left->getLabel(), $right->getLabel())
		);

		return $categories;
	}
}
