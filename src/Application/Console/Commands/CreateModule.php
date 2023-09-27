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
        $vendor = $this->ask('What is the module vendor?', 'Ebolution');
        $name = $this->ask('What is the module name?', 'LaravelCustomerSample');
        $path = $this->ask('What is the module path?', 'local-modules/' . strtolower($vendor) .  '/' . strtolower(preg_replace('/[A-Z]/', '-$0', lcfirst($name))));
        $model = $this->ask('What is the model name?', 'Customer');
        $table = $this->ask('What is the table name?', 'customers');

        $authorName = $this->ask('What is the author name?', 'Avanzed Cloud Develop, S.L.');
        $authorEmail = $this->ask('What is the author email?', 'desarrollo@ebolution.com');
        $copyright = $this->ask('What is the copyright info?', '© '. date("Y") . ' '. $authorName. ' - All rights reserved.');
        $license = $this->ask('What is the license info?', 'Proprietary https://ebolution.com');

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
            $output_path = $file->getRealPath();
            if ($file->getFilename() === 'create_table.php') {
                $output_path = $file->getPath() . DIRECTORY_SEPARATOR . date("Y_m_d_His") . "_create_" . $table . "_table.php";
            } elseif ($file->getFilename() === 'BaseCrudModuleScaffoldEntity.php') {
                $output_path = $file->getPath() . DIRECTORY_SEPARATOR . $model . ".php";
            }

            $content = file_get_contents($file->getRealPath());
            $content = str_replace('Ebolution', $vendor, $content);
            $content = str_replace('BaseCrudModuleScaffoldEntity', $model, $content);
            $content = str_replace('BaseCrudModuleScaffold', $name, $content);
            $content = str_replace('@table_name', $table, $content);
            $content = str_replace('@module.author.name', $authorName, $content);
            $content = str_replace('@module.author.email', $authorEmail, $content);
            $content = str_replace('@module.copyright', $copyright, $content);
            $content = str_replace('@module.license', $license, $content);

            file_put_contents($output_path, $content);

            if ($output_path !== $file->getRealPath()) {
                unlink($file->getRealPath());
            }
        }

        $composerJsonPath = "{$path}/composer.json";
        $composerJsonContent = json_decode(file_get_contents($composerJsonPath), true);

        $composerJsonContent['name'] = strtolower($vendor) . '/' . strtolower(preg_replace('/[A-Z]/', '-$0', lcfirst($name)));
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
