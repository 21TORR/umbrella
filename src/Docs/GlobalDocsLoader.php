<?php
declare(strict_types=1);

namespace Torr\Umbrella\Docs;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Torr\Umbrella\Cache\ProductionCache;
use Torr\Umbrella\Data\Docs\DocsPage;
use Torr\Umbrella\Data\Docs\GlobalDocs;
use Torr\Umbrella\Docs\Markdown\MarkdownParser;
use Torr\Umbrella\Paths\UmbrellaPaths;
use Torr\Umbrella\Translator\LabelGenerator;

final class GlobalDocsLoader implements CacheClearerInterface
{
	private const CACHE_KEY = "21torr.umbrella.docs";
	private UmbrellaPaths $paths;
	private MarkdownParser $markdownParser;
	private LabelGenerator $labelGenerator;
	private ?GlobalDocs $globalDocs = null;
	private ProductionCache $cache;

	/**
	 */
	public function __construct (
		UmbrellaPaths $paths,
		MarkdownParser $markdownParser,
		LabelGenerator $labelGenerator,
		ProductionCache $cache
	)
	{
		$this->paths = $paths;
		$this->markdownParser = $markdownParser;
		$this->labelGenerator = $labelGenerator;
		$this->cache = $cache;
	}

	/**
	 */
	public function load () : GlobalDocs
	{
		if (null === $this->globalDocs)
		{
			$this->globalDocs = $this->cache->get(
				self::CACHE_KEY,
				function ()
				{
					return $this->fetch();
				}
			);
		}

		return $this->globalDocs;
	}

	/**
	 */
	private function fetch () : GlobalDocs
	{
		try {
			$finder = Finder::create()
				->in($this->paths->getGlobalDocsDir())
				->name("~^[a-z][a-z0-9_\\- ]*\\.md$~i")
				->depth("== 0")
				->files()
				->ignoreUnreadableDirs();

			$pages = [];

			foreach ($finder as $file)
			{
				$key = $file->getBasename(".md");
				$frontMatter = $this->markdownParser->fetchFrontMatter(
					\file_get_contents($file->getPathname()),
					$this->getDocsFrontMatterConstraint()
				);

				$pages[$key] = new DocsPage(
					$key,
					$frontMatter["title"] ?? $this->labelGenerator->generate($key)
				);
			}

			\uksort(
				$pages,
				static function (DocsPage $left, DocsPage $right)
				{
					return \strnatcasecmp($left->getTitle(), $right->getTitle());
				}
			);

			return new GlobalDocs($pages);
		}
		catch (DirectoryNotFoundException $exception)
		{
			return new GlobalDocs([]);
		}
	}

	/**
	 */
	private function getDocsFrontMatterConstraint () : Collection
	{
		return new Collection([
			"title" => [
				new Type("string"),
			],
		]);
	}

	/**
	 */
	public function getRenderedDocsPageContent (DocsPage $page) : string
	{
		$filePath = $this->paths->getGlobalDocsFilePath($page);

		if (!\is_file($filePath) || !\is_readable($filePath))
		{
			return "";
		}

		return $this->markdownParser->render(\file_get_contents($filePath));
	}

	/**
	 * @inheritDoc
	 */
	public function clear(string $cacheDir) : void
	{
		$this->globalDocs = null;
		$this->cache->delete(self::CACHE_KEY);
	}
}
