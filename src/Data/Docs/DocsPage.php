<?php
declare(strict_types=1);

namespace Torr\Umbrella\Data\Docs;

final class DocsPage
{
	private string $key;
	private string $label;

	public function __construct (string $key, string $label)
	{
		$this->key = $key;
		$this->label = $label;
	}

	/**
	 */
	public function getKey() : string
	{
		return $this->key;
	}

	/**
	 */
	public function getLabel() : string
	{
		return $this->label;
	}
}
