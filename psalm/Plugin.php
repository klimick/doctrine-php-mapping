<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Klimick\PsalmDoctrinePhpMapping\Hook\Association\InverseSideAssociationAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Association\OwningSideAssociationAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Property\FieldsAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Property\ManyToManyAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Property\ManyToOneAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Property\OneToManyAnalysis;
use Klimick\PsalmDoctrinePhpMapping\Hook\Property\OneToOneAnalysis;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

final class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $stubs = glob(__DIR__ . '/stubs/Doctrine/DBAL/Types/*.phpstub') ?: [];

        foreach ($stubs as $stub) {
            $registration->addStubFile($stub);
        }

        $registerHook = function(string $hook) use ($registration): void {
            class_exists($hook);
            $registration->registerHooksFromClass($hook);
        };

        $registerHook(OwningSideAssociationAnalysis::class);
        $registerHook(InverseSideAssociationAnalysis::class);
        $registerHook(FieldsAnalysis::class);
        $registerHook(OneToOneAnalysis::class);
        $registerHook(ManyToOneAnalysis::class);
        $registerHook(OneToManyAnalysis::class);
        $registerHook(ManyToManyAnalysis::class);
    }
}
