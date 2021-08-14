<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper;

use Fp\Functional\Option\Option;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Storage\ClassLikeStorage;

final class GetMappingClassStorage
{
    /**
     * @return Option<ClassLikeStorage>
     */
    public static function for(AfterFunctionLikeAnalysisEvent $event): Option
    {
        $codebase = $event->getCodebase();

        return Option::fromNullable($event->getContext()->self)
            ->filter(fn($self) => is_subclass_of($self, EntityMapping::class))
            ->flatMap(fn($self) => Option::try(fn() => $self::forClass()))
            ->flatMap(fn($entity_class) => Option::try(fn() => $codebase->classlike_storage_provider->get($entity_class)));
    }
}
