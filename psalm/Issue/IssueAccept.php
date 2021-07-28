<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue;

use PhpParser\Node\Expr\MethodCall;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\StatementsSource;

final class IssueAccept
{
    public function __construct(private StatementsSource $source)
    {
    }

    public function noMappedByFieldAtOwningSide(MethodCall $method_call, string $missing_field, string $mapping_class, string $association_type): void
    {
        $location = new CodeLocation($this->source, $method_call);
        $issue = new NoMappedByFieldAtOwningSideIssue($location, $missing_field, $mapping_class, $association_type);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }
}
