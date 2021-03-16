<?php
declare(strict_types=1);

namespace Torr\Umbrella\CustomPage;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Torr\Umbrella\Exception\CustomPage\InvalidCustomPageKeyException;
use Torr\Umbrella\Exception\CustomPage\UnknownCustomPageException;

final class CustomPagesRegistry
{
	private ServiceLocator $pages;

	/**
	 */
	public function __construct (ServiceLocator $pages)
	{
		$this->pages = $pages;

		foreach ($this->pages->getProvidedServices() as $key => $class)
		{
			if (!$this->isValidKey($key))
			{
				throw new InvalidCustomPageKeyException(\sprintf(
					"Invalid custom page key: '%s'",
					$key
				));
			}
		}
	}

	/**
	 * @return CustomUmbrellaPageInterface
	 */
	public function getAll () : array
	{
		$sections = [];

		foreach ($this->pages->getProvidedServices() as $key => $service)
		{
			$sections[] = $this->get($key);
		}

		\usort(
			$sections,
			static function (CustomUmbrellaPageInterface $left, CustomUmbrellaPageInterface $right)
			{
				return \strnatcasecmp($left->getTitle(), $right->getTitle());
			}
		);

		return $sections;
	}

	/**
	 */
	public function get (string $key) : CustomUmbrellaPageInterface
	{
		try {
			return $this->pages->get($key);
		}
		catch (ServiceNotFoundException $exception)
		{
			throw new UnknownCustomPageException(\sprintf(
				"No custom page found with key '%s'",
				$key
			), 0, $exception);
		}
	}

	/**
	 * Returns whether the page key is valid
	 */
	private function isValidKey (string $key) : bool
	{
		return 0 !== \preg_match('~^[a-z0-9][a-z0-9-_]*$~', $key);
	}
}
