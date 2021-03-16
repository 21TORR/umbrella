<?php
declare(strict_types=1);

namespace Torr\Umbrella\Cache;

use Symfony\Contracts\Cache\CacheInterface;

final class ProductionCache implements CacheInterface
{
	private CacheInterface $cache;
	private bool $isDebug;

	public function __construct (CacheInterface $cache, bool $isDebug)
	{
		$this->cache = $cache;
		$this->isDebug = $isDebug;
	}

	/**
	 * @inheritDoc
	 */
	public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null)
	{
		return !$this->isDebug
			? $this->cache->get($key, $callback, $beta, $metadata)
			: $callback();
	}

	/**
	 * @inheritDoc
	 */
	public function delete(string $key) : bool
	{
		return $this->cache->delete($key);
	}
}
