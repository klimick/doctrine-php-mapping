<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Association;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class InvalidAssociationAtInverseSideIssue extends CodeIssue
{
    public function __construct(CodeLocation $location, string $mapped_by_field, string $mapping)
    {
        parent::__construct(
            message: implode(' ', [
                "Inverse side association '{$mapped_by_field}'",
                "of mapping '{$mapping}' has no mappedBy field"
            ]),
            code_location: $location,
        );
    }
}
