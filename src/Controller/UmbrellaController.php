<?php declare(strict_types=1);

namespace Torr\Umbrella\Controller;

use Symfony\Component\HttpFoundation\Response;
use Torr\Rad\Controller\BaseController;
use Torr\Umbrella\Component\Library\ComponentLibraryLoader;
use Torr\Umbrella\Data\ComponentData;
use Torr\Umbrella\Renderer\ComponentRenderer;

final class UmbrellaController extends BaseController
{
	/**
	 */
	public function index (ComponentLibraryLoader $libraryLoader) : Response
	{
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
		string $category,
		string $key
	) : Response
	{
		$library = $libraryLoader->loadLibrary();
		$categoryData = $library->getCategory($category);
		$component = $categoryData->getComponent($key);

		if ($component->isHidden())
		{
			throw $this->createNotFoundException("Component is hidden");
		}

		return $this->render("@Umbrella/component.html.twig", [
			"category" => $categoryData,
			"component" => $component,
			"categories" => $library->getCategories(),
		]);
	}


	/**
	 */
	public function preview (
		ComponentLibraryLoader $libraryLoader,
		ComponentRenderer $componentRenderer,
		string $category,
		string $key
	) : Response
	{
		$library = $libraryLoader->loadLibrary();
		$categoryData = $library->getCategory($category);
		$component = $categoryData->getComponent($key);

		if ($component->isHidden())
		{
			throw $this->createNotFoundException("Component is hidden");
		}

		return $this->render("@Umbrella/preview.html.twig", [
			"category" => $categoryData,
			"component" => $component,
			"html" => $componentRenderer->renderStandalone($category, $key),
		]);
	}

	/**
	 * Renders the navigation
	 */
	public function navigation (
		ComponentLibraryLoader $libraryLoader,
		?ComponentData $currentComponent
	) : Response
	{
		$library = $libraryLoader->loadLibrary();

		return $this->render("@Umbrella/navigation/navigation.html.twig", [
			"categories" => $library->getCategories(),
			"currentComponent" => $currentComponent,
		]);
	}
}
