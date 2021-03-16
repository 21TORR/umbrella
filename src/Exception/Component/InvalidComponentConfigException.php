<?php
declare(strict_types=1);

namespace Torr\Umbrella\Exception\Component;

use Torr\Umbrella\Exception\UmbrellaException;

final class InvalidComponentConfigException extends \InvalidArgumentException implements UmbrellaException
{
}
