<?php
declare(strict_types=1);

namespace Torr\Umbrella\Translator;

final class LabelGenerator
{
	/**
	 * Generates the label
	 */
	public function generate (string $key) : string
	{
		$key = \strtr($key, ["-" => " ", "_" => " "]);
		$key = (string) \preg_replace('~\\s+~', ' ', $key);
		return \ucwords($key);
	}
}
