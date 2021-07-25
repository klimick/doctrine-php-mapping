<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping;

/**
 * @template-covariant TEntity of object
 * @extends EntityMapping<TEntity>
 */
abstract class MappedSuperclassMapping extends EntityMapping
{
}
