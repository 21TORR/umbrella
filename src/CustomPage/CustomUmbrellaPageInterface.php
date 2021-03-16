<?php
declare(strict_types=1);

namespace Torr\Umbrella\CustomPage;

use Symfony\Component\HttpFoundation\Request;

/**
 * @final
 */
interface CustomUmbrellaPageInterface
{
	/**
	 * Returns the title of the section
	 */
	public function getTitle () : string;

	/**
	 * Returns the key for this section
	 */
	public static function getKey () : string;

	/**
	 * Renders the section
	 */
	public function render (Request $request) : string;
}
