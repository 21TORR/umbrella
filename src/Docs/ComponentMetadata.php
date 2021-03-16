<?php
declare(strict_types=1);

namespace Torr\Umbrella\Docs;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Docs\Markdown\MarkdownParser;
use Torr\Umbrella\Exception\Component\InvalidComponentConfigException;
use Torr\Umbrella\Exception\Docs\InvalidFrontMatterException;
use Torr\Umbrella\Paths\UmbrellaPaths;

final class ComponentMetadata
{
	private UmbrellaPaths $paths;
	private MarkdownParser $markdownParser;

	/**
	 */
	public function __construct (
		MarkdownParser $markdownParser,
		UmbrellaPaths $paths
	)
	{
		$this->paths = $paths;
		$this->markdownParser = $markdownParser;
	}




	/**
	 * Renders the docs for the given file
	 */
	public function renderDocs (ComponentData $component) : ?string
	{
		return $this->markdownParser->render($this->loadDocs($component));
	}


	/**
	 * Returns the config of the component
	 */
	public function getComponentConfig (ComponentData $component) : array
	{
		try {
			$assertions = new Collection([
				"body" => [
					new Type("string"),
				],
			]);

			return $this->markdownParser->fetchFrontMatter(
				$this->loadDocs($component),
				$assertions
			);
		}
		catch (InvalidFrontMatterException $exception)
		{
			throw new InvalidComponentConfigException(\sprintf(
				"The config for component '%s' is invalid: %s",
				$component->toPath(),
				$exception->getMessage()
			), 0, $exception);
		}
	}


	/**
	 * Loads the docs file as string
	 */
	private function loadDocs (ComponentData $component) : ?string
	{
		$filePath = $this->paths->getFullComponentDocsPath($component);

		if (!\is_file($filePath) || !\is_readable($filePath))
		{
			return null;
		}

		return (string) \file_get_contents($filePath);
	}
}
