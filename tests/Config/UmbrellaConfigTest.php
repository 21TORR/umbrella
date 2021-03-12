<?php
declare(strict_types=1);

namespace Tests\Torr\Umbrella\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Torr\Hosting\Tier\HostingTier;
use Torr\Umbrella\Config\UmbrellaConfig;
use Torr\Umbrella\Exception\UmbrellaDisabledException;

final class UmbrellaConfigTest extends TestCase
{
	/**
	 */
	public function provideIsEnabled () : iterable
	{
		yield [true, true, 'development'];
		yield [true, true, 'staging'];
		yield [true, true, 'production'];

		yield [true, true, 'development'];
		yield [true, true, 'staging'];
		yield [false, false, 'production'];
	}


	/**
	 * @dataProvider provideIsEnabled
	 */
	public function testIsEnabled (bool $shouldBeEnabled, bool $enabledInProd, string $hostingTier)
	{
		$tier = new HostingTier($hostingTier);
		$config = new UmbrellaConfig($enabledInProd, $tier);

		self::assertSame($shouldBeEnabled, $config->isEnabled());
	}


	/**
	 *
	 */
	public function ensureProperReturnStatus () : void
	{
		$exception = new UmbrellaDisabledException();

		self::assertInstanceOf(HttpExceptionInterface::class, $exception);
		self::assertSame(404, $exception->getStatusCode());
	}

}
