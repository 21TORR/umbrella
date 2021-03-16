<?php
declare(strict_types=1);

namespace Torr\Umbrella\Exception\Docs;

use Torr\Umbrella\Exception\UmbrellaException;

final class InvalidFrontMatterException extends \InvalidArgumentException implements UmbrellaException
{
}
