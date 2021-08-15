<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Property;

use Doctrine\DBAL;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Fp\Functional\Option\Option;
use Klimick\PsalmDoctrinePhpMapping\RaiseIssue;
use PhpParser\Node;
use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Type;
use function Fp\Collection\at;

final class IndexByPropertyTypeFetcher
{
    private const DATABASE_TYPE_INDEX = 1;

    /**
     * @param string $property_name
     * @param array<string, mixed> $method_data
     * @return Option<Type\Union>
     */
    public static function fetchFor(
        AfterFunctionLikeAnalysisEvent $event,
        Node $property_node,
        string $property_name,
        array $method_data,
    ): Option
    {
        return Option::do(function() use ($event, $property_node, $property_name, $method_data) {
            $association = yield at($method_data, $property_name)->filter(
                fn($association) => $association instanceof ManyToManyField ||
                    $association instanceof OwningSide\ManyToManyField ||
                    $association instanceof InverseSide\ManyToManyField ||
                    $association instanceof InverseSide\OneToManyField
            );

            if (null === $association->indexBy) {
                return Type::getInt();
            }

            $fields = yield Option::try(fn() => $association->mapping::fields());

            if (!array_key_exists($association->indexBy, $fields)) {
                RaiseIssue::for($event)
                    ->properties()
                    ->indexByPropertyDoesNotExists(
                        node: $property_node,
                        class: $association->mapping::forClass(),
                        property: $association->indexBy,
                    );

                return Type::getEmpty();
            }

            $codebase = ProjectAnalyzer::$instance->getCodebase();
            $field_type = $fields[$association->indexBy]->type;

            $dbal_type = yield Option::try(
                fn() => $codebase->classlike_storage_provider->get($field_type)
            );

            $php_type = yield Option::fromNullable(
                $dbal_type->template_extended_offsets[DBAL\Types\Type::class][self::DATABASE_TYPE_INDEX] ?? null
            );

            $is_allowed_index_by_type = UnionTypeComparator::isContainedBy($codebase, $php_type, Type::getInt()) ||
                UnionTypeComparator::isContainedBy($codebase, $php_type, Type::getString());

            if (!$is_allowed_index_by_type) {
                RaiseIssue::for($event)
                    ->properties()
                    ->indexByPropertyTypeIsNotAllowed(
                        node: $property_node,
                        class: $association->mapping::forClass(),
                        property: $association->indexBy,
                        actual_type: $php_type,
                    );

                return Type::getEmpty();
            }

            return $php_type;
        });
    }
}
