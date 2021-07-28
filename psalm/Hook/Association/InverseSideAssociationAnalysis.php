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

final class InverseSideAssociationAnalysis implements AfterMethodCallAnalysisInterface
{
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        Option::do(function() use ($event) {
            $method_call = yield AssociationMethodCall::from(
                event: $event,
                association_function: 'Klimick\DoctrinePhpMapping\Dsl\inverseSide',
                supported_methods: [
                    GetAssociationFields::INVERSE_SIDE_MANY_TO_MANY,
                    GetAssociationFields::INVERSE_SIDE_ONE_TO_MANY,
                    GetAssociationFields::INVERSE_SIDE_ONE_TO_ONE,
                ],
            );

            $association_field = yield GetAssociationField::from($event);
            $association_mapping = yield GetAssociationClass::from($method_call->node);
            $owning_side_associations = GetAssociationFields::atOwningSide($method_call->name, $association_mapping);

            if (!array_key_exists($association_field, $owning_side_associations)) {
                RaiseIssue::for($event)
                    ->associations()
                    ->noAssociationFieldAtOwningSide(
                        node: $method_call->node,
                        field: $association_field,
                        mapping: $association_mapping,
                    );
            } elseif (!IsAssociationValid::atOwningSide($owning_side_associations[$association_field], $method_call->name)) {
                RaiseIssue::for($event)
                    ->associations()
                    ->invalidAssociationAtOwningSide(
                        node: $method_call->node,
                        field: $association_field,
                        mapping: $association_mapping,
                    );
            }
        });
    }
}
