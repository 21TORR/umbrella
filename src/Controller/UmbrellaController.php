<?php declare(strict_types=1);

namespace Torr\Umbrella\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Torr\Rad\Controller\BaseController;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Config\UmbrellaConfig;
use Torr\Umbrella\CustomPage\CustomPagesRegistry;
use Torr\Umbrella\CustomPage\CustomUmbrellaPageInterface;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Data\Docs\DocsPage;
use Torr\Umbrella\Docs\ComponentMetadata;
use Torr\Umbrella\Docs\GlobalDocsLoader;
use Torr\Umbrella\Exception\UmbrellaDisabledException;
use Torr\Umbrella\Preview\PreviewManager;
use Torr\Umbrella\Renderer\ComponentRenderer;

final class UmbrellaController extends BaseController
{
	public const REQUEST_ATTRIBUTE_HIDE_CUSTOM_PAGES = "_umbrella_hide_custom_section";

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
		Request $request,
		string $category,
		string $key
	) : Response
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		if (null !== $profiler && $request->query->has("embedded"))
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
		CustomPagesRegistry $customPages,
		?ComponentData $currentComponent,
		?DocsPage $currentDocsPage,
		?CustomUmbrellaPageInterface $currentCustomPage,
		RequestStack $requestStack
	) : Response
	{
		$library = $libraryLoader->loadLibrary();

		$masterRequest = $requestStack->getMasterRequest();
		$custom = null === $masterRequest || !$masterRequest->attributes->get(self::REQUEST_ATTRIBUTE_HIDE_CUSTOM_PAGES)
			? $customPages->getAll()
			: [];

		return $this->render("@Umbrella/navigation/navigation.html.twig", [
			"categories" => $library->getCategories(),
			"docs" => $docsLoader->load()->getAll(),
			"customPages" => $custom,
			"currentComponent" => $currentComponent,
			"currentDocsPage" => $currentDocsPage,
			"currentCustomPage" => $currentCustomPage,
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


	public function customPage (
		UmbrellaConfig $config,
		CustomPagesRegistry $pagesRegistry,
		Request $request,
		string $key
	)
	{
		if (!$config->isEnabled())
		{
			throw new UmbrellaDisabledException();
		}

		$page = $pagesRegistry->get($key);


		return $this->render("@Umbrella/custom-page/custom-page.html.twig", [
			"customPage" => $page,
			"content" => $page->render($request),
		]);
	}
}
