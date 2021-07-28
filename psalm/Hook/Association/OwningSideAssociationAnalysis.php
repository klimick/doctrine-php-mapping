<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook\Association;

use Klimick\PsalmDoctrinePhpMapping\Helper\Association\AssociationMethodCall;
use Klimick\PsalmDoctrinePhpMapping\Helper\Association\GetAssociationClass;
use Klimick\PsalmDoctrinePhpMapping\Helper\Association\GetAssociationField;
use Klimick\PsalmDoctrinePhpMapping\Helper\Association\GetAssociationFields;
use Klimick\PsalmDoctrinePhpMapping\Helper\Association\IsAssociationValid;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Klimick\PsalmDoctrinePhpMapping\RaiseIssue;
use Fp\Functional\Option\Option;

final class OwningSideAssociationAnalysis implements AfterMethodCallAnalysisInterface
{
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        Option::do(function() use ($event) {
            $method_call = yield AssociationMethodCall::from(
                event: $event,
                association_function: 'Klimick\DoctrinePhpMapping\Dsl\owningSide',
                supported_methods: [
                    GetAssociationFields::OWNING_SIDE_MANY_TO_MANY,
                    GetAssociationFields::OWNING_SIDE_MANY_TO_ONE,
                    GetAssociationFields::OWNING_SIDE_ONE_TO_ONE,
                ],
            );

            $association_field = yield GetAssociationField::from($event);
            $association_mapping = yield GetAssociationClass::from($method_call->node);
            $inverse_side_associations = GetAssociationFields::atInverseSide($method_call->name, $association_mapping);

            if (!array_key_exists($association_field, $inverse_side_associations)) {
                RaiseIssue::for($event)
                    ->associations()
                    ->noAssociationFieldAtInverseSide(
                        node: $method_call->node,
                        field: $association_field,
                        mapping: $association_mapping,
                    );
            } elseif (!IsAssociationValid::atInverseSide($inverse_side_associations[$association_field], $method_call->name)) {
                RaiseIssue::for($event)
                    ->associations()
                    ->invalidAssociationAtInverseSide(
                        node: $method_call->node,
                        field: $association_field,
                        mapping: $association_mapping,
                    );
            }
        });
    }
}
