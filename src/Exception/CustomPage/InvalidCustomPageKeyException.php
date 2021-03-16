<?php
declare(strict_types=1);

namespace Torr\Umbrella\Exception\CustomPage;

use Torr\Umbrella\Exception\UmbrellaException;

final class InvalidCustomPageKeyException extends \InvalidArgumentException implements UmbrellaException
{
}
