<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Klimick\PsalmDoctrinePhpMapping\Hook\InverseSideAssociationAnalysis;
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

        $registerHook(InverseSideAssociationAnalysis::class);
    }
}
