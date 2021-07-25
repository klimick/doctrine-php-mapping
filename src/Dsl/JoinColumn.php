<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Field\Common\JoinColumn;

/**
 * @psalm-param non-empty-literal-string $name
 * @psalm-param non-empty-literal-string $referencedColumnName
 * @psalm-param JoinColumn::ON_DELETE_* $onDelete
 */
function joinColumn(
    string $name,
    string $referencedColumnName = 'id',
    bool   $unique = false,
    string $onDelete = JoinColumn::ON_DELETE_NO_ACTION,
): JoinColumn
{
    return new JoinColumn($name, $referencedColumnName, $unique, $onDelete);
}
