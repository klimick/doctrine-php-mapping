<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;

final class RaiseIssue
{
    public static function for(AfterMethodCallAnalysisEvent $event): IssueFactory
    {
        return new IssueFactory($event->getStatementsSource());
    }
}
