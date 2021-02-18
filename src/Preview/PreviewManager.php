<?php declare(strict_types=1);

namespace Torr\Umbrella\Preview;

final class PreviewManager
{
	private array $previewAssets;

	/**
	 */
	public function __construct (array $previewAssets)
	{
		$this->previewAssets = $previewAssets;
	}

	/**
	 */
	public function getPreviewAssets () : array
	{
		return $this->previewAssets;
	}
}
