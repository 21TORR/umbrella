<?php declare(strict_types=1);

namespace Torr\Umbrella\Data;

final class ComponentData
{
	private string $key;
	private string $label;
	private bool $hidden;
	private ?CategoryData $category = null;

	/**
	 */
	public function __construct (string $key, string $label, bool $hidden)
	{
		$this->key = $key;
		$this->label = $label;
		$this->hidden = $hidden;
	}

	/**
	 */
	public function getKey () : string
	{
		return $this->key;
	}

	/**
	 */
	public function getLabel () : string
	{
		return $this->label;
	}

	/**
	 */
	public function isHidden () : bool
	{
		return $this->hidden;
	}

	/**
	 */
	public function getCategory() : CategoryData
	{
		return $this->category;
	}

	/**
	 * @internal
	 */
	public function setCategory (CategoryData $category) : void
	{
		$this->category = $category;
	}

	/**
	 */
	public function toPath () : string
	{
		return "{$this->getCategory()->getKey()}/{$this->key}";
	}
}
