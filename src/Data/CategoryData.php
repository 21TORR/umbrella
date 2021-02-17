<?php declare(strict_types=1);

namespace Torr\Umbrella\Data;

use Torr\Umbrella\Exception\MissingComponentException;

final class CategoryData
{
	private string $key;
	private string $label;
	/** @var ComponentData[] */
	private array $components;

	/**
	 * @param ComponentData[] $components
	 */
	public function __construct (string $key, string $label, array $components)
	{
		$this->key = $key;
		$this->label = $label;
		$this->components = $components;
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
	 * @return ComponentData[]
	 */
	public function getComponents () : array
	{
		return $this->components;
	}


	/**
	 */
	public function getComponent (string $key) : ComponentData
	{
		$component = $this->components[$key] ?? null;

		if (null === $component)
		{
			throw new MissingComponentException(\sprintf(
				"Can't find component '%s' in category '%s'",
				$key,
				$this->key
			));
		}

		return $component;
	}


	/**
	 * @return ComponentData[]
	 */
	public function getVisibleComponents () : array
	{
		return \array_filter(
			$this->components,
			static fn (ComponentData $componentData) => !$componentData->isHidden()
		);
	}
}
