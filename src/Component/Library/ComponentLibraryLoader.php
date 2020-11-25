<?php declare(strict_types=1);

namespace Torr\Umbrella\Component\Library;

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

final class ComponentLibraryLoader
{
	private string $baseDir;

	public function __construct (
		string $templatesDir,
		string $subDir
	)
	{
		$this->baseDir = \rtrim($templatesDir, "/") . "/" . \trim($subDir, "/");
	}

	/**
	 */
	public function loadLibrary () : ComponentLibrary
	{
		try
		{
			$finder = Finder::create()
				->in($this->baseDir)
				->files()
				->ignoreDotFiles(true)
				->ignoreUnreadableDirs()
				// this check serves different purposes:
				// 1. specify which types of directory names are allowed
				// 2. specify which types of file names are allowed
				// 3. the templates must end in ".html.twig"
				// 4. Only templates exactly one level under the base dir are allowed (so in a single sub dir)
				->path('~^[a-z0-9][a-z0-9-_]*\/[a-z0-9][a-z0-9-_]*\.html\.twig$~i')
			;

			$library = [];

			foreach ($finder as $key => $file)
			{
				$type = \dirname($file->getRelativePathname());
				$name = \basename($file->getRelativePathname(), ".html.twig");

				$library[$type][] = $name;
			}

			return new ComponentLibrary($this->baseDir, $library);
		}
		catch (DirectoryNotFoundException $exception)
		{
			return new ComponentLibrary($this->baseDir, []);
		}
	}
}
