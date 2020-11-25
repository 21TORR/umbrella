<?php declare(strict_types=1);

namespace Tests\Torr\Umbrella\Component\Library;

use PHPUnit\Framework\TestCase;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;

final class ComponentLibraryLoaderTest extends TestCase
{
	/**
	 *
	 */
	public function testLoader () : void
	{
		$fixturesDir = \dirname(__DIR__, 2) . "/fixtures";
		$loader = new ComponentLibraryLoader($fixturesDir, "_layouts");
		$library = $loader->loadLibrary();

		self::assertEqualsCanonicalizing(
			[
				"atom" => ["test"],
				"test" => ["example", "example2"],
			],
			$library->getComponents()
		);
	}
}
