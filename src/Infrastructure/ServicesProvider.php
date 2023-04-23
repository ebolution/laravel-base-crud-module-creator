<?php
/**
 * @category  Ebolution
 * @package   Ebolution/BaseCrudModuleScaffold
 * @author    Manuel GARCÍA SOLIPA <manuel.garcia@ebolution.com>
 * @copyright 2023 Avanzed Cloud Develop S.L
 * @license   MIT - https://www.ebolution.com/
 */

namespace Ebolution\BaseCrudModuleCreator\Infrastructure;

use Ebolution\BaseCrudModuleCreator\Application\Console\Commands\CreateModule;
use Ebolution\ModuleManager\Infrastructure\ServicesProvider as ModuleManagerServiceProviders;

final class ServicesProvider extends ModuleManagerServiceProviders
{
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $this->commands([CreateModule::class]);
        }
    }
}
