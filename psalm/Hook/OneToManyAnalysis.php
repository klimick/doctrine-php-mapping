<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook;

use Klimick\PsalmDoctrinePhpMapping\Helper\GetMappingClassStorage;
use Klimick\PsalmDoctrinePhpMapping\Helper\Property\ExtractEntityProperties;
use Klimick\PsalmDoctrinePhpMapping\Helper\Property\ValidateEntityProperties;
use PhpParser\Node;
use Fp\Functional\Option\Option;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use function Fp\Evidence\proveOf;

final class OneToManyAnalysis implements AfterFunctionLikeAnalysisInterface
{
    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $entity_properties = yield self::getOneToManyMethod($event)
                ->flatMap(fn($class_method) => ExtractEntityProperties::by($class_method, $event));

            $entity_class_storage = yield GetMappingClassStorage::for($event);

            ValidateEntityProperties::againstMappingProperties(
                event: $event,
                entity: $entity_class_storage,
                properties_from_mapping: $entity_properties,
            );
        });

        return null;
    }

    /**
     * @return Option<Node\Stmt\ClassMethod>
     */
    private static function getOneToManyMethod(AfterFunctionLikeAnalysisEvent $event): Option
    {
        return proveOf($event->getStmt(), Node\Stmt\ClassMethod::class)->filter(
            fn($class_method) => proveOf($class_method->name, Node\Identifier::class)
                ->map(fn($id) => 'oneToMany' === $id->name)
                ->getOrElse(false)
        );
    }
}
