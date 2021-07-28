<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Association;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class InvalidAssociationAtOwningSideIssue extends CodeIssue
{
    public function __construct(CodeLocation $location, string $mapped_by_field, string $mapping)
    {
        parent::__construct(
            message: implode(' ', [
                "Owning side association '{$mapped_by_field}'",
                "of mapping '{$mapping}' has no inversedBy field"
            ]),
            code_location: $location,
        );
    }
}
