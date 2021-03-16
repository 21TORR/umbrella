<?php
declare(strict_types=1);

namespace Torr\Umbrella\Docs;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Torr\Umbrella\Paths\UmbrellaPaths;

final class DocsPages
{
	private UmbrellaPaths $paths;

	/**
	 */
	public function __construct (UmbrellaPaths $paths)
	{
		$this->paths = $paths;
	}

	/**
	 */
	public function fetchDocsPages () : array
	{
		$docsPath = $this->paths->getGlobalDocsDir();

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

				dump($key, $file);
			}

			return [];
		}
		catch (DirectoryNotFoundException $exception)
		{
			return [];
		}
	}


}
