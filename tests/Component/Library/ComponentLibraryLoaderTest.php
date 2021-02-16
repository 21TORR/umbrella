<?php declare(strict_types=1);

namespace Tests\Torr\Umbrella\Component\Library;

use PHPUnit\Framework\TestCase;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Translator\UmbrellaTranslator;

final class ComponentLibraryLoaderTest extends TestCase
{
	/**
	 *
	 */
	public function testLoader () : void
	{
		$fixturesDir = \dirname(__DIR__, 2) . "/fixtures";
		$translator = $this->getMockBuilder(UmbrellaTranslator::class)
			->disableOriginalConstructor()
			->getMock();

		$translator
			->method("translateCategory")
			->willReturnArgument(0);

		$translator
			->method("translateComponent")
			->willReturnArgument(1);

		$loader = new ComponentLibraryLoader(
			$translator,
			null,
			$fixturesDir,
			"_layouts"
		);
		$components = $loader->loadLibrary()->getCategories();

		$simplifiedResult = [];

		foreach ($components as $typeKey => $type)
		{
			$simplifiedResult[$typeKey] = \array_keys($type->getComponents());
		}

		self::assertEqualsCanonicalizing(
			[
				"atom" => ["test"],
				"test" => ["example", "example2"],
			],
			$simplifiedResult
		);
	}
}
