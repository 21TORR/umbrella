<?php
declare(strict_types=1);

namespace Torr\Umbrella\Paths;

final class UmbrellaPaths
{
	private string $templatesPath;
	private string $layoutsDir;
	private string $docsDir;

	public function __construct (
		string $projectDir,
		string $layoutsDir,
		string $docsDir
	)
	{
		$this->templatesPath = "{$projectDir}/templates";
		$this->layoutsDir = \trim($layoutsDir, "/");
		$this->docsDir = \trim($docsDir, "/");
	}

	/**
	 * Returns the path to the global docs dir
	 */
	public function getGlobalDocsDir () : string
	{
		return "{$this->templatesPath}/{$this->layoutsDir}/{$this->docsDir}";
	}
}
