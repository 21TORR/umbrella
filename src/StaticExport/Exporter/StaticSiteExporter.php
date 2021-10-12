<?php declare(strict_types=1);

namespace Torr\Umbrella\StaticExport\Exporter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Torr\Assets\Asset\Asset;
use Torr\Assets\Namespaces\NamespaceRegistry;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Controller\UmbrellaController;
use Torr\Umbrella\Docs\GlobalDocsLoader;
use Torr\Umbrella\Preview\PreviewManager;
use Torr\Umbrella\StaticExport\Exception\ExportFailedException;

final class StaticSiteExporter
{
	private const OUTPUT_DIR = "var/umbrella/export";

	private RouterInterface $router;
	private HttpKernelInterface $httpKernel;
	private Filesystem $filesystem;
	private ComponentLibraryLoader $libraryLoader;
	private GlobalDocsLoader $docsLoader;
	private string $projectDir;
	private PreviewManager $previewManager;
	private NamespaceRegistry $assetNamespaces;

	/**
	 */
	public function __construct (
		RouterInterface $router,
		HttpKernelInterface $httpKernel,
		Filesystem $filesystem,
		ComponentLibraryLoader $libraryLoader,
		GlobalDocsLoader $docsLoader,
		PreviewManager $previewManager,
		NamespaceRegistry $assetNamespaces,
		string $projectDir
	)
	{
		$this->router = $router;
		$this->httpKernel = $httpKernel;
		$this->filesystem = $filesystem;
		$this->libraryLoader = $libraryLoader;
		$this->docsLoader = $docsLoader;
		$this->previewManager = $previewManager;
		$this->assetNamespaces = $assetNamespaces;
		$this->projectDir = $projectDir;
	}

	/**
	 * Exports the whole umbrella project as static site
	 *
	 * @throws ExportFailedException
	 */
	public function export (SymfonyStyle $io) : void
	{
		$outputDir = "{$this->projectDir}/" . self::OUTPUT_DIR;
		$library = $this->libraryLoader->loadLibrary();

		$io->comment(\sprintf(
			"Generating static site export to <fg=blue>%s</>",
			self::OUTPUT_DIR
		));


		// region Remove (pre-existing) Export Dir
		$io->writeln("• removing previous export");
		$this->filesystem->remove($outputDir);
		// endregion


		// region Dump Index
		$io->writeln("• Exporting <fg=yellow>index</>");
		$this->filesystem->dumpFile(
			"{$outputDir}/index.html",
			$this->renderPage("/", UmbrellaController::class . "::index")
		);
		// endregion


		// region Dump All Components Pages
		foreach ($library->getCategories() as $category)
		{
			foreach ($category->getVisibleComponents() as $component)
			{
				$io->writeln(\sprintf(
					"• Exporting umbrella page <fg=yellow>%s</>",
					$component->toPath()
				));

				$url = \trim($this->router->generate("umbrella.component", [
					"category" => $category->getKey(),
					"key" => $component->getKey(),
				]), "/");

				$this->filesystem->dumpFile(
					"{$outputDir}/{$url}/index.html",
					$this->renderPage("/{$url}", UmbrellaController::class . "::component", [
						"category" => $category->getKey(),
						"key" => $component->getKey(),
					])
				);
			}
		}
		// endregion


		// region Dump Component Previews
		foreach ($library->getCategories() as $category)
		{
			foreach ($category->getVisibleComponents() as $component)
			{
				$io->writeln(\sprintf(
					"• Exporting preview page <fg=yellow>%s</>",
					$component->toPath()
				));

				$url = \trim($this->router->generate("umbrella.preview", [
					"category" => $category->getKey(),
					"key" => $component->getKey(),
				]), "/");

				$this->filesystem->dumpFile(
					"{$outputDir}/{$url}/index.html",
					$this->renderPage("/{$url}", UmbrellaController::class . "::preview", [
						"category" => $category->getKey(),
						"key" => $component->getKey(),
					])
				);
			}
		}
		// endregion


		// region Export Docs Pages
		foreach ($this->docsLoader->load()->getAll() as $docsPage)
		{
			$io->writeln(\sprintf(
				"• Exporting docs page %s (<fg=yellow>%s</>)",
				$docsPage->getTitle(),
				$docsPage->getKey()
			));

			$url = \trim($this->router->generate("umbrella.docs", [
				"key" => $docsPage->getKey(),
			]), "/");

			$this->filesystem->dumpFile(
				"{$outputDir}/{$url}/index.html",
				$this->renderPage("/{$url}", UmbrellaController::class . "::globalDocs", [
					"key" => $docsPage->getKey(),
				])
			);
		}
		// endregion


		// region Dump Umbrella Assets
		foreach ($this->collectUsedAssetNamespaces() as $namespace)
		{
			$io->writeln(\sprintf(
				"• Copy assets for namespace <fg=yellow>%s</>",
				$namespace
			));

			$this->filesystem->mirror(
				$this->assetNamespaces->getNamespacePath($namespace),
				$outputDir . "/assets/{$namespace}"
			);
		}
		// endregion

		$io->writeln("<fg=green>✓</> done");
		$io->comment(\sprintf(
			"Export files were written to <fg=blue>%s</>",
			self::OUTPUT_DIR
		));
	}

	/**
	 * Returns the used asset namespaces
	 *
	 * @return string[]
	 */
	private function collectUsedAssetNamespaces () : array
	{
		$namespaces = ["umbrella" => true];

		foreach ($this->previewManager->getPreviewAssets() as $previewAsset)
		{
			$asset = Asset::create($previewAsset);
			$namespaces[$asset->getNamespace()] = true;
		}

		return \array_keys($namespaces);
	}


	/**
	 * @throws ExportFailedException
	 */
	private function renderPage (string $url, string $controller, array $parameters = []) : string
	{
		try
		{
			$parameters['_controller'] = $controller;
			$request = Request::create($url, "GET", $parameters);
			$request->attributes->set(UmbrellaController::REQUEST_ATTRIBUTE_HIDE_CUSTOM_PAGES, true);
			$response = $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST);

			return (string) $response->getContent();
		}
		catch (\Exception $exception)
		{
			throw new ExportFailedException("Static site export failed: {$exception->getMessage()}", 0, $exception);
		}
	}
}
