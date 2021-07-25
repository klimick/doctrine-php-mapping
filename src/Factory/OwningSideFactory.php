<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Factory;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\OwningSide;

/**
 * @template TInversedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class OwningSideFactory
{
    /**
     * @param TInversedBy $inversedBy
     */
    public function __construct(private string $inversedBy)
    {
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return OwningSide\ManyToManyField<TEntity, TInversedBy>
     */
    public function manyToManyOf(string $mapping): OwningSide\ManyToManyField
    {
        return new OwningSide\ManyToManyField($mapping, $this->inversedBy);
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return OwningSide\ManyToOneField<TEntity, TInversedBy, false>
     */
    public function manyToOneOf(string $mapping): OwningSide\ManyToOneField
    {
        return new OwningSide\ManyToOneField($mapping, $this->inversedBy);
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return OwningSide\OneToOneField<TEntity, TInversedBy, false>
     */
    public function oneToOneOf(string $mapping): OwningSide\OneToOneField
    {
        return new OwningSide\OneToOneField($mapping, $this->inversedBy);
    }
}
