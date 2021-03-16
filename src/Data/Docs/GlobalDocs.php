<?php
declare(strict_types=1);

namespace Torr\Umbrella\Data\Docs;

final class GlobalDocs
{
	/** @var array<string, DocsPage>  */
	private array $docs;

	/**
	 * @param array<string, DocsPage> $docs
	 */
	public function __construct (array $docs)
	{
		$this->docs = $docs;
	}

	/**
	 * @return DocsPage[]
	 */
	public function getAll () : array
	{
		return $this->docs;
	}

	/**
	 * Returns a single doc file
	 */
	public function get(string $key) : ?DocsPage
	{
		return $this->docs[$key] ?? null;
	}
}
