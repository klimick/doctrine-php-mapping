<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Factory\InheritanceFactory;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\Inheritance;

function singleTableInheritance(): InheritanceFactory
{
    return new InheritanceFactory(Inheritance::TYPE_SINGLE_TABLE);
}
