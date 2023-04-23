<?php
/**
 * @category  Ebolution
 * @package   Ebolution/BaseCrudModuleCreator
 * @author    Manuel GARCÍA SOLIPA <manuel.garcia@ebolution.com>
 * @copyright 2023 Avanzed Cloud Develop S.L
 * @license   Private - https://www.ebolution.com/
 */

namespace Ebolution\BaseCrudModuleCreator\Application\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CreateModule extends Command
{
    protected $signature = 'ebolution:base-crud-module-creator:create';
    protected $description = 'Create a new module based on ebolution/base-crud-module';

    public function handle(): void
    {
        $vendor = $this->ask('What is the module vendor?');
        $name = $this->ask('What is the module name?');
        $path = $this->ask('What is the module path?');

        $authorName = $this->ask('What is the author name?');
        $authorEmail = $this->ask('What is the author email?');
        $copyright = $this->ask('What is the copyright info?');
        $license = $this->ask('What is the license info?');

        if (file_exists($path)) {
            $this->error('The directory already exists.');
            return;
        }

        $gitCloneResult = shell_exec(
            "git clone https://github.com/ebolution/laravel-base-crud-module-scaffold.git {$path}"
        );
        if (str_contains($gitCloneResult, 'fatal')) {
            $this->error('Failed to clone the repository.');
            return;
        }

        $fs = new Filesystem();
        $fs->deleteDirectory("{$path}/.git");

        $finder = new Finder();
        $finder->files()->in($path)->name('*.php')->name('*.json')->name('*.md');

        foreach ($finder as $file) {
            $content = file_get_contents($file->getRealPath());
            $content = str_replace('Ebolution', $vendor, $content);
            $content = str_replace('BaseCrudModuleScaffold', $name, $content);
            $content = str_replace('@module.author.name', $authorName, $content);
            $content = str_replace('@module.author.email', $authorEmail, $content);
            $content = str_replace('@module.copyright', $copyright, $content);
            $content = str_replace('@module.license', $license, $content);
            file_put_contents($file->getRealPath(), $content);
        }

        $composerJsonPath = "{$path}/composer.json";
        $composerJsonContent = json_decode(file_get_contents($composerJsonPath), true);

        $composerJsonContent['name'] = strtolower($vendor) . '/laravel-' . strtolower(preg_replace('/[A-Z]/', '-$0', lcfirst($name)));
        $composerJsonContent['authors'] = [
            [
                'name' => $authorName,
                'email' => $authorEmail
            ]
        ];
        $composerJsonContent['license'] = $license;
        $composerJsonContent['autoload']['psr-4']["$vendor\\"] = 'src/';

        file_put_contents($composerJsonPath, json_encode($composerJsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Module created successfully.");
    }
}