<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class NoMappedByFieldAtOwningSideIssue extends CodeIssue
{
    public function __construct(CodeLocation $location, string $mapped_by_field, string $mapping, string $association_type)
    {
        parent::__construct(
            message: implode(' ', [
                "Entity mapping '{$mapping}'",
                "has no expected {$association_type} association",
                "'{$mapped_by_field}' at owning side",
            ]),
            code_location: $location,
        );
    }
}
