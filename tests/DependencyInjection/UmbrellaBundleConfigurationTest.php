<?php
declare(strict_types=1);

namespace Tests\Torr\Umbrella\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Torr\Umbrella\DependencyInjection\UmbrellaBundleConfiguration;

final class UmbrellaBundleConfigurationTest extends TestCase
{
	/**
	 * Ensures that the default config is valid and doesn't change
	 */
	public function testDefaultConfig () : void
	{
		$processor = new Processor();
		$configTree = (new UmbrellaBundleConfiguration())->getConfigTreeBuilder()->buildTree();

		$config = $processor->process($configTree, []);

		self::assertSame("_layout", $config["templates_directory"]);
		self::assertSame([], $config["assets"]);
		self::assertFalse($config["enabled_in_production"]);
	}
}
