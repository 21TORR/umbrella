<?php
declare(strict_types=1);

namespace Torr\Umbrella\Config;

use Torr\Hosting\Tier\HostingTier;

final class UmbrellaConfig
{
	private bool $enabledInProduction;
	private HostingTier $hostingTier;

	/**
	 */
	public function __construct (bool $enabledInProduction, HostingTier $hostingTier)
	{
		$this->enabledInProduction = $enabledInProduction;
		$this->hostingTier = $hostingTier;
	}

	/**
	 */
	public function isEnabled () : bool
	{
		return $this->enabledInProduction || !$this->hostingTier->isProduction();
	}
}
