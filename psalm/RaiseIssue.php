<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;

final class RaiseIssue
{
    public static function for(AfterMethodCallAnalysisEvent|AfterFunctionLikeAnalysisEvent $event): IssueFactory
    {
        return new IssueFactory($event->getStatementsSource());
    }
}
