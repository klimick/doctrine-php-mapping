<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Factory;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\InverseSide;

/**
 * @template TMappedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class InverseSideFactory
{
    /**
     * @param TMappedBy $mappedBy
     */
    public function __construct(private string $mappedBy)
    {
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return InverseSide\ManyToManyField<TEntity, TMappedBy>
     */
    public function manyToMany(string $mapping): InverseSide\ManyToManyField
    {
        return new InverseSide\ManyToManyField($mapping, $this->mappedBy);
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return InverseSide\OneToManyField<TEntity, TMappedBy>
     */
    public function oneToMany(string $mapping): InverseSide\OneToManyField
    {
        return new InverseSide\OneToManyField($mapping, $this->mappedBy);
    }

    /**
     * @template TEntity of object
     *
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @return InverseSide\OneToOneField<TEntity, TMappedBy>
     */
    public function oneToOne(string $mapping): InverseSide\OneToOneField
    {
        return new InverseSide\OneToOneField($mapping, $this->mappedBy);
    }
}
