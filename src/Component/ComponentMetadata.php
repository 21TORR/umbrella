<?php
declare(strict_types=1);

namespace Torr\Umbrella\Component;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Exception\Component\InvalidComponentConfigException;
use Torr\Umbrella\Paths\ComponentPaths;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;

final class ComponentMetadata
{
	private ?MarkdownConverterInterface $converter = null;
	private ?FrontMatterExtension $frontMatterExtension = null;
	private ComponentPaths $paths;
	private Twig $twig;

	/**
	 */
	public function __construct (
		ComponentPaths $paths,
		Twig $twig
	)
	{
		$this->paths = $paths;
		$this->twig = $twig;
	}


	/**
	 * Creates and returns the markdown converter
	 */
	private function getConverter () : MarkdownConverterInterface
	{
		if (null === $this->converter)
		{
			$environment = new Environment([]);
			$environment->addExtension(new CommonMarkCoreExtension());
			$environment->addExtension(new GithubFlavoredMarkdownExtension());

			$environment->addExtension($this->getFrontMatterExtension());

			$this->converter = new MarkdownConverter($environment);
		}

		return $this->converter;
	}

	/**
	 * Returns the front matter markdown extension
	 */
	private function getFrontMatterExtension () : FrontMatterExtension
	{
		if (null === $this->frontMatterExtension)
		{
			$this->frontMatterExtension = new FrontMatterExtension();
		}

		return $this->frontMatterExtension;
	}

	/**
	 * Renders the docs for the given file
	 */
	public function renderDocs (ComponentData $component) : ?string
	{
		$docs = $this->loadDocs($component);

		return null !== $docs
			? $this->getConverter()->convertToHtml($docs)->getContent()
			: null;
	}

	/**
	 * Returns the config of the component
	 */
	public function getComponentConfig (ComponentData $component) : array
	{
		$frontMatterParser = $this->getFrontMatterExtension()->getFrontMatterParser();
		$docs = $this->loadDocs($component);

		if (null === $docs)
		{
			return [];
		}

		$config = $frontMatterParser->parse($docs)->getFrontMatter();

		if (!\is_array($config))
		{
			return [];
		}

		$validator = Validation::createValidator();
		$violations = $validator->validate($config, new Collection([
			"body" => [
				new Type("string"),
			]
		]));

		if (\count($violations) > 0)
		{
			throw new InvalidComponentConfigException(\sprintf(
				"The config for component '%s' is invalid: %s",
				$component->toPath(),
				(string) $violations
			));
		}

		return $config;
	}


	/**
	 * Loads the docs file as string
	 */
	private function loadDocs (ComponentData $component) : ?string
	{
		try {
			$filePath = $this->paths->getTwigDocsPath($component);
			$source = $this->twig->getLoader()->getSourceContext($filePath);

			return $source->getCode();
		}
		catch (LoaderError $error)
		{
			return null;
		}

	}
}
