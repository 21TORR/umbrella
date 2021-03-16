<?php
declare(strict_types=1);

namespace Torr\Umbrella\Paths;

use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Data\Docs\DocsPage;

final class UmbrellaPaths
{
	private string $templatesPath;
	private string $layoutsDir;
	private string $docsDir;

	public function __construct (
		string $templatesPath,
		string $layoutsDir,
		string $docsDir
	)
	{
		$this->templatesPath = $templatesPath;
		$this->layoutsDir = \trim($layoutsDir, "/");
		$this->docsDir = \trim($docsDir, "/");
	}

	/**
	 * Returns the path to the global docs dir
	 */
	public function getGlobalDocsDir () : string
	{
		return "{$this->getLayoutsBaseDir()}/{$this->docsDir}";
	}



	/**
	 * Builds a full template include path
	 */
	public function getTwigTemplatePath (ComponentData $component) : string
	{
		return "{$this->layoutsDir}/{$component->toPath()}.html.twig";
	}


	/**
	 * Builds a full docs include path
	 */
	public function getFullComponentDocsPath (ComponentData $component) : string
	{
		return "{$this->getLayoutsBaseDir()}/{$component->toPath()}.md";
	}

	/**
	 * Returns the base dir where all the layouts are placed
	 */
	public function getLayoutsBaseDir () : string
	{
		return "{$this->templatesPath}/{$this->layoutsDir}";
	}

	/**
	 */
	public function getGlobalDocsFilePath (DocsPage $page) : string
	{
		return "{$this->getGlobalDocsDir()}/{$page->getKey()}.md";
	}
}
