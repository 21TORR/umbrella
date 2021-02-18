<?php declare(strict_types=1);

namespace Torr\Umbrella\Variations;

final class ContextVariations
{
	private array $variations = [];

	/**
	 * Generates the context for all variations
	 */
	public function generateVariationsContexts (array $config) : array
	{
		// reset
		$this->variations = [];

		// we have a depth first algorithm, but we want to keep the first keys first, so we reverse the array
		$config = \array_reverse($config, true);

		// start with empty trail
		$this->appendFromRest([], $config);

		return $this->variations;
	}

	/**
	 */
	private function appendFromRest (array $trail, array $rest) : void
	{
		if (empty($rest))
		{
			if (!empty($trail))
			{
				$this->variations[] = $trail;
			}

			return;
		}

		$key = \array_key_first($rest);
		$childRest = \array_slice($rest, 1);

		foreach ($rest[$key] as $value)
		{
			$trail[$key] = $value;
			$this->appendFromRest($trail, $this->sliceKey($childRest, $key));
		}
	}

	/**
	 * Slices the array from an array
	 */
	private function sliceKey (array $array, string $sliceKey) : array
	{
		$result = [];

		foreach ($array as $key => $value)
		{
			if ($sliceKey !== $key)
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}
}
