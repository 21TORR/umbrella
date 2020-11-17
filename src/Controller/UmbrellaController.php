<?php declare(strict_types=1);

namespace Torr\Umbrella\Controller;

use Symfony\Component\HttpFoundation\Response;
use Torr\Rad\Controller\BaseController;

final class UmbrellaController extends BaseController
{
	public function index () : Response
	{
		return $this->render("@Umbrella/index.html.twig");
	}
}
