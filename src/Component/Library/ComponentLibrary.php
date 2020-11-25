<?php declare(strict_types=1);

namespace Torr\Umbrella\Component\Library;

/**
 */
final class ComponentLibrary
{
	private array $components;
	private string $baseDir;

	/**
	 */
	public function __construct (string $baseDir, array $components)
	{
		$this->baseDir = $baseDir;
		$this->components = $components;
	}

	/**
	 */
	public function getComponents () : array
	{
		return $this->components;
	}
}
