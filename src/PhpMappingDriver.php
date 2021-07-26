<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Klimick\DoctrinePhpMapping\Mapping\EmbeddedMapping;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumn;
use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\Inheritance;
use Klimick\DoctrinePhpMapping\Mapping\MappedSuperclassMapping;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use RuntimeException;

/**
 * @see Type
 *
 * @psalm-type DbalType = Type<mixed, mixed, array<string, mixed>>
 * @psalm-type EntityClass = class-string
 *
 * @psalm-type MappingClass
 *     = class-string<EntityMapping<object>>
 *     | class-string<EmbeddedMapping<object>>
 *     | class-string<MappedSuperclassMapping<object>>
 */
final class PhpMappingDriver implements MappingDriver
{
    /**
     * @param array<EntityClass, MappingClass> $mappings
     * @param array<class-string<DbalType>, non-empty-literal-string> $types
     */
    public function __construct(private array $mappings, private array $types)
    {
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        assert($metadata instanceof ClassMetadataInfo);

        $mapping = $this->mappings[$className]
            ?? throw new RuntimeException("No mapping for '{$className}' class");

        self::configureFields($mapping, $metadata, $this->types);
        self::configureInheritance($mapping, $metadata, $this->types);
        self::configureMappingType($mapping, $metadata);
        self::configureEmbedded($mapping, $metadata);
        self::configureOneToOne($mapping, $metadata);
        self::configureOneToMany($mapping, $metadata);
        self::configureManyToOne($mapping, $metadata);
        self::configureManyToMany($mapping, $metadata);
    }

    public function getAllClassNames(): array
    {
        return array_keys($this->mappings);
    }

    public function isTransient($className): bool
    {
        return !array_key_exists($className, $this->mappings) || $this->mappings[$className]::isTransient();
    }

    /**
     * @param MappingClass $mapping
     * @param array<class-string<DbalType>, non-empty-literal-string> $types
     */
    private static function configureInheritance(string $mapping, ClassMetadataInfo $metadata, array $types): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        $inheritance = $mapping::inheritance();

        if (!($inheritance instanceof Inheritance)) {
            return;
        }

        $type = $types[$inheritance->discriminator->type]
            ?? throw new RuntimeException("Type '{$inheritance->discriminator->type}' is not registered");

        $metadata->setInheritanceType(match ($inheritance->inheritanceType) {
            Inheritance::TYPE_JOINED => ClassMetadataInfo::INHERITANCE_TYPE_JOINED,
            Inheritance::TYPE_SINGLE_TABLE => ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE,
        });

        $metadata->setDiscriminatorColumn([
            'type' => $type,
            'name' => $inheritance->discriminatorName,
            'length' => $inheritance->discriminator->options['length'] ?? 255,
            'columnDefinition' => $inheritance->discriminator->options['columnDefinition'] ?? null,
        ]);

        $metadata->setDiscriminatorMap(array_map(fn($mapping) => $mapping::forClass(), $inheritance->discriminatorMap));
    }

    /**
     * @param MappingClass $mapping
     */
    private static function configureManyToOne(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        foreach ($mapping::manyToOne() as $name => $field) {
            $manyToOneMapping = [
                'fieldName' => $name,
                'targetEntity' => $field->mapping::forClass(),
                'fetch' => $field->fetch,
                'joinColumns' => [self::joinColumnToArray($field->joinColumn, $field->nullable)],
            ];

            if ($field instanceof OwningSide\ManyToOneField) {
                $manyToOneMapping['inversedBy'] = $field->inversedBy;
            }

            $metadata->mapManyToOne($manyToOneMapping);
        }
    }

    /**
     * @param MappingClass $mapping
     */
    private static function configureManyToMany(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        foreach ($mapping::manyToMany() as $name => $field) {
            $manyToManyMapping = [
                'fieldName' => $name,
                'targetEntity' => $field->mapping::forClass(),
            ];

            if ($field instanceof InverseSide\ManyToManyField) {
                $manyToManyMapping['mappedBy'] = $field->mappedBy;
            }

            if ($field instanceof OwningSide\ManyToManyField) {
                $manyToManyMapping['inversedBy'] = $field->inversedBy;
            }

            if ($field instanceof ManyToManyField) {
                $manyToManyMapping['fetch'] = $field->fetch;
                $manyToManyMapping['orphanRemoval'] = $field->orphanRemoval;

                if (null !== $field->indexBy) {
                    $manyToManyMapping['indexBy'] = $field->indexBy;
                }

                if (null !== $field->orderBy) {
                    $manyToManyMapping['orderBy'] = $field->orderBy;
                }
            }

            if ($field instanceof ManyToManyField || $field instanceof OwningSide\ManyToManyField) {
                $manyToManyMapping['cascade'] = $field->cascade;

                if (null !== $field->joinTable) {
                    $manyToManyMapping['joinTable'] = [
                        'name' => $field->joinTable,
                        'joinColumns' => array_map(fn($c) => self::joinColumnToArray($c), $field->joinColumns),
                        'inverseJoinColumns' => array_map(fn($c) => self::joinColumnToArray($c), $field->inverseJoinColumns),
                    ];
                }
            }

            $metadata->mapManyToMany($manyToManyMapping);
        }
    }

    /**
     * @param MappingClass $mapping
     */
    private static function configureOneToMany(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        foreach ($mapping::oneToMany() as $name => $field) {
            $oneToManyMapping = [
                'fieldName' => $name,
                'targetEntity' => $field->mapping::forClass(),
                'mappedBy' => $field->mappedBy,
                'fetch' => $field->fetch,
                'orphanRemoval' => $field->orphanRemoval,
                'cascade' => $field->cascade,
                'orderBy' => $field->orderBy,
            ];

            if (null !== $field->indexBy) {
                $oneToManyMapping['indexBy'] = $field->indexBy;
            }

            $metadata->mapOneToMany($oneToManyMapping);
        }
    }

    /**
     * @param MappingClass $mapping
     */
    private static function configureOneToOne(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        foreach ($mapping::oneToOne() as $name => $field) {
            $oneToOneMapping = [
                'fieldName' => $name,
                'targetEntity' => $field->mapping::forClass(),
            ];

            if ($field instanceof OneToOneField || $field instanceof OwningSide\OneToOneField) {
                $oneToOneMapping['fetch'] = $field->fetch;
                $oneToOneMapping['cascade'] = $field->cascade;
                $oneToOneMapping['orphanRemoval'] = $field->orphanRemoval;
                $oneToOneMapping['joinColumns'] = [self::joinColumnToArray($field->joinColumn, $field->nullable)];
            }

            if ($field instanceof OwningSide\OneToOneField) {
                $oneToOneMapping['inversedBy'] = $field->inversedBy;
            }

            if ($field instanceof InverseSide\OneToOneField) {
                $oneToOneMapping['mappedBy'] = $field->mappedBy;
            }

            $metadata->mapOneToOne($oneToOneMapping);
        }
    }

    /**
     * @param MappingClass $mapping
     */
    private static function configureMappingType(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (is_subclass_of($mapping, MappedSuperclassMapping::class)) {
            $metadata->isMappedSuperclass = true;
        } elseif (is_subclass_of($mapping, EmbeddedMapping::class)) {
            $metadata->isEmbeddedClass = true;
        }
    }

    /**
     * @param MappingClass $mapping
     * @param array<class-string<DbalType>, non-empty-literal-string> $types
     */
    private static function configureFields(string $mapping, ClassMetadataInfo $metadata, array $types): void
    {
        /** @var list<mixed> $identifier */
        $identifier = $metadata->getIdentifier();

        foreach ($mapping::fields() as $name => $field) {
            $type = $types[$field->type]
                ?? throw new RuntimeException("Type '{$field->type}' is not registered");

            if ($field instanceof Field) {
                $fieldMapping = [
                    'fieldName' => $name,
                    'type' => $type,
                    'unique' => $field->unique,
                    'nullable' => $field->nullable,
                ];
            } else {
                $fieldMapping = [
                    'id' => true,
                    'fieldName' => $name,
                    'type' => $type,
                ];

                $identifier[] = $name;
            }

            if (null !== $field->options) {
                $fieldMapping['options'] = $field->options;
            }

            if (null !== $field->column) {
                $fieldMapping['columnName'] = $field->column;
            }

            $metadata->mapField($fieldMapping);
        }

        $metadata->setIdentifier($identifier);
    }

    /**
     * @psalm-param MappingClass $mapping
     */
    private static function configureEmbedded(string $mapping, ClassMetadataInfo $metadata): void
    {
        if (!is_subclass_of($mapping, EntityMapping::class)) {
            return;
        }

        foreach ($mapping::embedded() as $name => $embed) {
            $metadata->mapEmbedded([
                'fieldName' => $name,
                'class' => $embed->embedded::forClass(),
                'columnPrefix' => null !== $embed->prefix ? $embed->prefix : false,
            ]);
        }
    }

    private static function joinColumnToArray(?JoinColumn $joinColumn, bool $nullable = false): array
    {
        $joinColumnData = ['nullable' => $nullable];

        if (null !== $joinColumn) {
            $joinColumnData['name'] = $joinColumn->name;
            $joinColumnData['onDelete'] = $joinColumn->onDelete;
            $joinColumnData['unique'] = $joinColumn->unique;
            $joinColumnData['referencedColumnName'] = $joinColumn->referencedColumnName;
        }

        return $joinColumnData;
    }
}
