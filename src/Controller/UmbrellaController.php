<?php declare(strict_types=1);

namespace Torr\Umbrella\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Torr\Rad\Controller\BaseController;
use Torr\Umbrella\Data\Docs\DocsPage;
use Torr\Umbrella\Docs\ComponentMetadata;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Config\UmbrellaConfig;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Docs\GlobalDocsLoader;
use Torr\Umbrella\Exception\UmbrellaDisabledException;
use Torr\Umbrella\Preview\PreviewManager;
use Torr\Umbrella\Renderer\ComponentRenderer;

final class UmbrellaController extends BaseController
{
	/**
	 */
	public function index (
		ComponentLibraryLoader $libraryLoader,
		UmbrellaConfig $config
	) : Response
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		$library = $libraryLoader->loadLibrary();
		$categories = $library->getCategories();

		return $this->render("@Umbrella/index.html.twig", [
			"categories" => $categories,
		]);
	}

	/**
	 */
	public function component (
		ComponentLibraryLoader $libraryLoader,
		UmbrellaConfig $config,
		ComponentMetadata $metadata,
		ComponentRenderer $componentRenderer,
		string $category,
		string $key
	) : Response
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		$library = $libraryLoader->loadLibrary();
		$categoryData = $library->getCategory($category);
		$component = $categoryData->getComponent($key);

		if ($component->isHidden())
		{
			throw $this->createNotFoundException("Component is hidden");
		}

		return $this->render("@Umbrella/component/component.html.twig", [
			"category" => $categoryData,
			"component" => $component,
			"categories" => $library->getCategories(),
			"docs" => $metadata->renderDocs($component),
			"code" => $componentRenderer->renderForCodeView($category, $key),
		]);
	}


	/**
	 */
	public function preview (
		ComponentLibraryLoader $libraryLoader,
		ComponentRenderer $componentRenderer,
		PreviewManager $previewManager,
		UmbrellaConfig $config,
		ComponentMetadata $metadata,
		?Profiler $profiler,
		string $category,
		string $key
	) : Response
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		if (null !== $profiler)
		{
			$profiler->disable();
		}

		$library = $libraryLoader->loadLibrary();
		$component = $library->getCategory($category)->getComponent($key);

		if ($component->isHidden())
		{
			throw $this->createNotFoundException("Component is hidden");
		}

		$componentConfig = $metadata->getComponentConfig($component);

		return $this->render("@Umbrella/component/preview.html.twig", [
			"component" => $component,
			"html" => $componentRenderer->renderStandalone($category, $key),
			"previewAssets" => $previewManager->getPreviewAssets(),
			"bodyClass" => $componentConfig["body"] ?? null,
		]);
	}

	/**
	 * Renders the navigation
	 */
	public function navigation (
		ComponentLibraryLoader $libraryLoader,
		GlobalDocsLoader $docsLoader,
		?ComponentData $currentComponent,
		?DocsPage $currentDocsPage
	) : Response
	{
		$library = $libraryLoader->loadLibrary();

		return $this->render("@Umbrella/navigation/navigation.html.twig", [
			"categories" => $library->getCategories(),
			"currentComponent" => $currentComponent,
			"currentDocsPage" => $currentDocsPage,
			"docs" => $docsLoader->load()->getAll(),
		]);
	}

	/**
	 */
	public function globalDocs (
		UmbrellaConfig $config,
		GlobalDocsLoader $docsLoader,
		string $key
	) : Response
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		$docs = $docsLoader->load();
		$page = $docs->get($key);

		if (null === $page)
		{
			throw $this->createNotFoundException(\sprintf(
				"Docs page does not exist with key '%s'",
				$key
			));
		}

		return $this->render("@Umbrella/docs/docs.html.twig", [
			"docsPage" => $page,
			"content" => $docsLoader->getRenderedDocsPageContent($page),
		]);
	}
}
