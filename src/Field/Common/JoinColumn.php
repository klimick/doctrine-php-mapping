<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
final class JoinColumn
{
    public const ON_DELETE_CASCADE = 'CASCADE';
    public const ON_DELETE_NO_ACTION = 'NO ACTION';
    public const ON_DELETE_SET_NULL = 'SET NULL';

    /**
     * @psalm-param non-empty-literal-string $name
     * @psalm-param non-empty-literal-string $referencedColumnName
     * @psalm-param JoinColumn::ON_DELETE_* $onDelete
     */
    public function __construct(
        public string $name,
        public string  $referencedColumnName = 'id',
        public bool    $unique = false,
        public string  $onDelete = self::ON_DELETE_NO_ACTION,
    ) {}
}
