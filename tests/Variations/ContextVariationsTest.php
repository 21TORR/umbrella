<?php declare(strict_types=1);

namespace Tests\Torr\Umbrella\Variations;

use PHPUnit\Framework\TestCase;
use Torr\Umbrella\Variations\ContextVariations;

final class ContextVariationsTest extends TestCase
{
	public function provideVariations () : iterable
	{
		yield [
			[
				"a" => ["A", "B"],
				"b" => [1, 2],
			],
			[
				["a" => "A", "b" => 1],
				["a" => "B", "b" => 1],
				["a" => "A", "b" => 2],
				["a" => "B", "b" => 2],
			],
		];

		yield [
			[
				"a" => ["A", "B"],
			],
			[
				["a" => "A"],
				["a" => "B"],
			],
		];

		yield [
			[],
			[],
		];
		yield [
			[
				"a" => ["A", "B"],
				"b" => [1, 2],
				"c" => [true, false, null]
			],
			[
				["a" => "A", "b" => 1, "c" => true],
				["a" => "B", "b" => 1, "c" => true],

				["a" => "A", "b" => 2, "c" => true],
				["a" => "B", "b" => 2, "c" => true],

				["a" => "A", "b" => 1, "c" => false],
				["a" => "B", "b" => 1, "c" => false],

				["a" => "A", "b" => 2, "c" => false],
				["a" => "B", "b" => 2, "c" => false],

				["a" => "A", "b" => 1, "c" => null],
				["a" => "B", "b" => 1, "c" => null],

				["a" => "A", "b" => 2, "c" => null],
				["a" => "B", "b" => 2, "c" => null],
			],
		];
	}

	/**
	 * @dataProvider provideVariations
	 */
	public function testVariations (array $config, array $expected) : void
	{
		$variations = new ContextVariations();
		self::assertEquals($expected, $variations->generateVariationsContexts($config));
	}
}
