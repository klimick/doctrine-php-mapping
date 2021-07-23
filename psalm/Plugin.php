<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

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

    }
}
