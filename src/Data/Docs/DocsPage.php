<?php
declare(strict_types=1);

namespace Torr\Umbrella\Data\Docs;

final class DocsPage
{
	private string $key;
	private string $title;

	public function __construct (string $key, string $title)
	{
		$this->key = $key;
		$this->title = $title;
	}

	/**
	 */
	public function getKey() : string
	{
		return $this->key;
	}

	/**
	 */
	public function getTitle() : string
	{
		return $this->title;
	}
}
