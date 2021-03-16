<?php declare(strict_types=1);

namespace Torr\Umbrella;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\Assets\Container\RegisterAssetNamespaceCompilerPass;
use Torr\BundleHelpers\Bundle\ConfigurableBundleExtension;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Config\UmbrellaConfig;
use Torr\Umbrella\DependencyInjection\UmbrellaBundleConfiguration;
use Torr\Umbrella\Paths\UmbrellaPaths;
use Torr\Umbrella\Preview\PreviewManager;

final class UmbrellaBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
	public function getContainerExtension () : ExtensionInterface
	{
		return new ConfigurableBundleExtension(
			$this,
			new UmbrellaBundleConfiguration(),
			static function (array $config, ContainerBuilder $container) : void
			{
				$container->getDefinition(PreviewManager::class)
					->setArgument('$previewAssets', $config["assets"]);

				$container->getDefinition(UmbrellaPaths::class)
					->setArgument('$layoutsDir', $config["templates_directory"])
					->setArgument('$docsDir', $config["docs_directory"]);

				$container->getDefinition(UmbrellaConfig::class)
					->setArgument('$enabledInProduction', $config["enabled_in_production"]);
			}
		);
	}

	/**
	 * @inheritDoc
	 */
	public function build(ContainerBuilder $container) : void
	{
		$container->addCompilerPass(new RegisterAssetNamespaceCompilerPass(
			"umbrella",
			\dirname(__DIR__) . "/build"
		));
	}


	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
