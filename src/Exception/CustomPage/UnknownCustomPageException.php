<?php
declare(strict_types=1);

namespace Torr\Umbrella\Exception\CustomPage;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Torr\Umbrella\Exception\UmbrellaException;

final class UnknownCustomPageException extends \InvalidArgumentException implements UmbrellaException, HttpExceptionInterface
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
