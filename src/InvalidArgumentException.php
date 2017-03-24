<?php
namespace Sandbox;

use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentException;
use InvalidArgumentException as SplInvalidArgumentException;

class InvalidArgumentException extends SplInvalidArgumentException implements PsrInvalidArgumentException
{
}
