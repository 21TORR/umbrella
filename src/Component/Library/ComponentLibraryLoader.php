<?php declare(strict_types=1);

namespace Torr\Umbrella\Component\Library;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Stopwatch\Stopwatch;
use Torr\Umbrella\Cache\ProductionCache;
use Torr\Umbrella\Data\CategoryData;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Paths\UmbrellaPaths;
use Torr\Umbrella\Translator\UmbrellaTranslator;

final class ComponentLibraryLoader
{
	private const CACHE_KEY = "21torr.umbrella.components";
	private UmbrellaTranslator $translator;
	private ?Stopwatch $stopwatch;
	private ?ComponentLibrary $library = null;
	private UmbrellaPaths $paths;
	private ProductionCache $cache;

	public function __construct (
		UmbrellaTranslator $translator,
		UmbrellaPaths $paths,
		?Stopwatch $stopwatch,
		ProductionCache $cache
	)
	{
		$this->stopwatch = $stopwatch;
		$this->translator = $translator;
		$this->paths = $paths;
		$this->cache = $cache;
	}


	/**
	 */
	public function loadLibrary () : ComponentLibrary
	{
		if (null === $this->library)
		{
			$this->library = $this->cache->get(
				self::CACHE_KEY,
				function ()
				{
					return $this->regenerateLibrary();
				}
			);
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
				->in($this->paths->getLayoutsBaseDir())
				->files()
				->ignoreDotFiles(true)
				->ignoreUnreadableDirs()
				// this regex is doing a lot of heavy lifting here:
				// 1. specify which types of directory names are allowed
				// 2. specify which types of file names are allowed
				// 3. the templates must end in ".html.twig"
				// 4. Only templates exactly one level under the base dir are allowed (so in a single sub dir)
				->path('~^[a-z0-9][a-z0-9-_]*\\/[a-z0-9_][a-z0-9-_]*\\.html\\.twig$~i');

			$components = $this->fetchComponentsFromFinder($finder);
			$categories = $this->bundleCategoriesFromComponentList($components);

			if ($this->stopwatch)
			{
				$this->stopwatch->stop("Umbrella: load library");
			}

			return new ComponentLibrary($categories);
		}
		catch (DirectoryNotFoundException $exception)
		{
			return new ComponentLibrary([]);
		}
	}

	/**
	 * @return array<string, array<string,ComponentData>>
	 */
	private function fetchComponentsFromFinder (Finder $finder) : array
	{
		$components = [];

		foreach ($finder as $file)
		{
			$type = \dirname($file->getRelativePathname());
			$name = \basename($file->getRelativePathname(), ".html.twig");

			$components[$type][$name] = new ComponentData(
				$name,
				$this->translator->translateComponent($type, $name),
				("_" === $name[0])
			);
		}

		return $components;
	}


	/**
	 * @param array<string, array<string,ComponentData>> $components
	 *
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
