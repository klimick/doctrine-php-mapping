<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Association;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class NoAssociationFieldAtOwningSideIssue extends CodeIssue
{
    public function __construct(CodeLocation $location, string $association_field, string $mapping_class)
    {
        parent::__construct(
            message: implode(' ', [
                "Entity mapping '{$mapping_class}'",
                "has no expected association",
                "'{$association_field}' at owning side",
            ]),
            code_location: $location,
        );
    }
}
