<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Psalm\StatementsSource;
use Klimick\PsalmDoctrinePhpMapping\Issue\Field\PropertiesIssueFactory;
use Klimick\PsalmDoctrinePhpMapping\Issue\Association\AssociationIssuesFactory;

final class IssueFactory
{
    public function __construct(private StatementsSource $source)
    {
    }

    public function associations(): AssociationIssuesFactory
    {
        return new AssociationIssuesFactory($this->source);
    }

    public function properties(): PropertiesIssueFactory
    {
        return new PropertiesIssueFactory($this->source);
    }
}
