<?php

namespace Nimp\LinkLoom\exceptions;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class NotFountConfigContainerException extends InvalidArgumentException
    implements NotFoundExceptionInterface
{

}