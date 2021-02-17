<?php declare(strict_types=1);

namespace Torr\Umbrella;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\BundleHelpers\Bundle\ConfigurableBundleExtension;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\DependencyInjection\UmbrellaBundleConfig;

final class UmbrellaBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
	public function getContainerExtension () : ExtensionInterface
	{
		return new ConfigurableBundleExtension(
			$this,
			new UmbrellaBundleConfig(),
			static function (array $config, ContainerBuilder $container) : void
			{
				$container->getDefinition(ComponentLibraryLoader::class)
					->setArgument('$subDir', $config["templates_directory"]);
			}
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
