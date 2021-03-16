<?php
declare(strict_types=1);

namespace Torr\Umbrella\Docs\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use Torr\Umbrella\Exception\Docs\InvalidFrontMatterException;

final class MarkdownParser
{
	private ?MarkdownConverterInterface $converter = null;
	private ?FrontMatterExtension $frontMatterExtension = null;

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
	 * Fetches the front matter
	 */
	public function fetchFrontMatter (?string $content, ?Collection $assertion = null) : array
	{
		$frontMatterParser = $this->getFrontMatterExtension()->getFrontMatterParser();

		if (null === $content)
		{
			return [];
		}

		$config = $frontMatterParser->parse($content)->getFrontMatter();

		if (!\is_array($config))
		{
			return [];
		}

		if (null !== $assertion)
		{
			$validator = Validation::createValidator();
			$violations = $validator->validate($config, $assertion);

			if (\count($violations) > 0)
			{
				throw new InvalidFrontMatterException(\sprintf(
					"Validation of front matter failed: %s",
					(string) $violations
				));
			}
		}

		return $config;
	}


	/**
	 * Renders the content as markdown
	 */
	public function render (?string $content) : ?string
	{
		return null !== $content
			? $this->getConverter()->convertToHtml($content)->getContent()
			: null;
	}
}
