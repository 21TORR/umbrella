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

		// expected types and components with their "hidden" value
		$expected = [
            "atom" => [
                "test" => false,
                "_hidden" => true,
            ],
            "test" => [
                "example" => false,
                "example2" => false,
            ],
        ];

		self::assertEqualsCanonicalizing(
		    \array_map(
		        static fn ($items) => \array_keys($items),
                $expected
            ),
			$simplifiedResult
		);

        foreach ($components as $typeKey => $type)
        {
            foreach ($type->getComponents() as $component)
            {
                self::assertSame($expected[$typeKey][$component->getKey()], $component->isHidden());
            }
        }
	}
}
