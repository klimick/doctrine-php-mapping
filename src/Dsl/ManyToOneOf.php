<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\ManyToOneField;

/**
 * @template TEntity of object
 *
 * @param class-string<EntityMapping<TEntity>> $mapping
 * @return ManyToOneField<TEntity, false>
 */
function manyToOneOf(string $mapping): ManyToOneField
{
    return new ManyToOneField($mapping);
}
