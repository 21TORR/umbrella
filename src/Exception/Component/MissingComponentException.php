<?php declare(strict_types=1);

namespace Torr\Umbrella\Exception\Component;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Torr\Umbrella\Exception\UmbrellaException;

final class MissingComponentException extends \InvalidArgumentException implements UmbrellaException, HttpExceptionInterface
{
	/**
	 * @inheritDoc
	 */
	public function getStatusCode () : int
	{
		return 404;
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders () : array
	{
		return [];
	}
}
