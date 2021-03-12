<?php
declare(strict_types=1);

namespace Torr\Umbrella\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class UmbrellaDisabledException extends \RuntimeException implements UmbrellaException, HttpExceptionInterface
{
	/**
	 * @inheritDoc
	 */
	public function getStatusCode()
	{
		return 404;
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders()
	{
		return [];
	}
}
