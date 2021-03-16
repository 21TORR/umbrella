<?php
declare(strict_types=1);

namespace Torr\Umbrella\Docs;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Torr\Umbrella\Data\Docs\DocsPage;
use Torr\Umbrella\Docs\Markdown\MarkdownParser;
use Torr\Umbrella\Paths\UmbrellaPaths;
use Torr\Umbrella\Translator\LabelGenerator;

final class GlobalDocs
{
	private UmbrellaPaths $paths;
	private MarkdownParser $markdownParser;
	private LabelGenerator $labelGenerator;

	/**
	 */
	public function __construct (
		UmbrellaPaths $paths,
		MarkdownParser $markdownParser,
		LabelGenerator $labelGenerator
	)
	{
		$this->paths = $paths;
		$this->markdownParser = $markdownParser;
		$this->labelGenerator = $labelGenerator;
	}

	/**
	 */
	public function fetchDocsPages () : array
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

				$pages[] = new DocsPage(
					$key,
					$frontMatter["title"] ?? $this->labelGenerator->generate($key)
				);
			}

			\usort(
				$pages,
				static function (DocsPage $left, DocsPage $right)
				{
					return \strnatcasecmp($left->getTitle(), $right->getTitle());
				}
			);

			dump($pages);
			return $pages;
		}
		catch (DirectoryNotFoundException $exception)
		{
			return [];
		}
	}

	private function getDocsFrontMatterConstraint () : Collection
	{
		return new Collection([
			"title" => [
				new Type("string"),
			],
		]);
	}

}
