<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Klimick\PsalmDoctrinePhpMapping\Issue\InvalidAssociationAtOwningSideIssue;
use Klimick\PsalmDoctrinePhpMapping\Issue\NoMappedByFieldAtOwningSideIssue;
use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\StatementsSource;

final class IssueFactory
{
    public function __construct(private StatementsSource $source)
    {
    }

    public function noMappedByFieldAtOwningSide(Node $node, string $field, string $mapping_class, string $association_type): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new NoMappedByFieldAtOwningSideIssue($location, $field, $mapping_class, $association_type);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function invalidAssociationAtOwningSide(Node $node, string $field, string $mapping_class): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new InvalidAssociationAtOwningSideIssue($location, $field, $mapping_class);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }
}
