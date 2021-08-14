<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook;

use PhpParser\Node;
use Fp\Functional\Option\Option;
use Klimick\PsalmDoctrinePhpMapping\Helper\GetMappingClassStorage;
use Klimick\PsalmDoctrinePhpMapping\Helper\Property\ExtractEntityProperties;
use Klimick\PsalmDoctrinePhpMapping\Helper\Property\ValidateEntityProperties;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use function Fp\Evidence\proveOf;

final class FieldsAnalysis implements AfterFunctionLikeAnalysisInterface
{
    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $entity_properties = yield self::getFieldsMethod($event)
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
    private static function getFieldsMethod(AfterFunctionLikeAnalysisEvent $event): Option
    {
        return proveOf($event->getStmt(), Node\Stmt\ClassMethod::class)->filter(
            fn($class_method) => proveOf($class_method->name, Node\Identifier::class)
                ->map(fn($id) => 'fields' === $id->name)
                ->getOrElse(false)
        );
    }
}
