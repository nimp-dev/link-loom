<?php

namespace Nimp\LinkLoom\DI\exeptions;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class NotFountConfigContainerException extends InvalidArgumentException
    implements NotFoundExceptionInterface
{

}