<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;

/**
 * @template TEntity of object
 *
 * @param class-string<EntityMapping<TEntity>> $mapping
 * @return ManyToManyField<TEntity>
 */
function manyToManyOf(string $mapping): ManyToManyField
{
    return new ManyToManyField($mapping);
}
