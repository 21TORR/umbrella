<?php declare(strict_types=1);

namespace Torr\Umbrella\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
