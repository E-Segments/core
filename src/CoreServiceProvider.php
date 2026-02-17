<?php

declare(strict_types=1);

namespace Esegments\Core;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('esegments-core')
            ->hasConfigFile('esegments-core');
    }

    public function packageBooted(): void
    {
        // Register any package bindings or boot logic here
    }
}
