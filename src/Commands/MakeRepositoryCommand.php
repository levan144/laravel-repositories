<?php

namespace Levan144\LaravelRepositories\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {model?} {--refresh}';
    protected $description = 'Create a new repository and its interface. If no model is provided, repositories will be generated for all models.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('refresh')) {
            $this->refreshRepositories();
            return 0;
        }

        $modelName = $this->argument('model');
        if (!$modelName) {
            $modelFiles = File::allFiles(app_path('Models'));
            foreach ($modelFiles as $file) {
                $model = $file->getBasename('.php');
                if (!$this->repositoryExists($model)) { 
                    $this->generateRepository($model);
                    $this->info("Added repository for model $model");
                }
            }
        } else {
            $this->generateRepository($modelName);
            $this->info("Added repository for model $modelName");
        }

        return 0;
    }

    protected function refreshRepositories()
{
    $modelFiles = File::allFiles(app_path('Models'));
    $existingModels = array_map(function ($file) {
        return $file->getBasename('.php');
    }, $modelFiles);

    $directories = File::directories(app_path('Repositories'));
    foreach ($directories as $directory) {
        $modelName = basename($directory);
        if (!in_array($modelName, $existingModels)) {
            File::deleteDirectory($directory);
            $this->info("Deleted repository for model $modelName");
            $this->removeFromServiceProvider($modelName);
        }
    }
}

protected function removeFromServiceProvider($modelName)
{
    $providerPath = app_path('Providers/RepositoryServiceProvider.php');
    $content = file_get_contents($providerPath);

    // Prepare patterns to remove from the provider
    $bindingPattern = "/\s*\\\$this->app->bind\\({$modelName}RepositoryInterface::class, {$modelName}Repository::class\\);/";
    $useRepositoryPattern = "/\s*use App\\\\Repositories\\\\{$modelName}\\\\{$modelName}Repository;/";
    $useRepositoryInterfacePattern = "/\s*use App\\\\Repositories\\\\{$modelName}\\\\{$modelName}RepositoryInterface;/";

    // Check and remove the lines if they exist
    if (preg_match($bindingPattern, $content)) {
        $content = preg_replace($bindingPattern, "", $content);
    }

    if (preg_match($useRepositoryPattern, $content)) {
        $content = preg_replace($useRepositoryPattern, "", $content);
    }

    if (preg_match($useRepositoryInterfacePattern, $content)) {
        $content = preg_replace($useRepositoryInterfacePattern, "", $content);
    }

    // Save the updated content back to the service provider
    file_put_contents($providerPath, $content);

    $this->info("Removed bindings and use statements for $modelName from RepositoryServiceProvider.");
}

protected function generateRepository($model)
{
    if ($this->createRepository($model) && $this->createRepositoryInterface($model)) {
        $this->updateServiceProvider($model);
        $this->info("{$model}Repository and {$model}RepositoryInterface created successfully.");
    } else {
        $this->error("Failed to create {$model}Repository or {$model}RepositoryInterface. They might already exist.");
    }
}

protected function repositoryExists($model)
{
    $repositoryPath = app_path("Repositories/{$model}/{$model}Repository.php");
    $interfacePath = app_path("Repositories/{$model}/{$model}RepositoryInterface.php");
    return file_exists($repositoryPath) || file_exists($interfacePath);
}

protected function createRepository($model)
    {
        $repositoryTemplate = $this->getStub('Repository/Repository');
        $repositoryTemplate = str_replace('{{modelName}}', $model, $repositoryTemplate);

        $repositoryPath = app_path("Repositories/{$model}/{$model}Repository.php");
        $repositoryDirectory = app_path("Repositories/{$model}");

        if (!File::isDirectory($repositoryDirectory)) {
            File::makeDirectory($repositoryDirectory, 0777, true, true);
        }

        if (!file_exists($repositoryPath)) {
            file_put_contents($repositoryPath, $repositoryTemplate);
            return true;
        }

        return false;
    }

    protected function createRepositoryInterface($model)
    {
        $interfaceTemplate = $this->getStub('Repository/RepositoryInterface');
        $interfaceTemplate = str_replace('{{modelName}}', $model, $interfaceTemplate);

        $interfacePath = app_path("Repositories/{$model}/{$model}RepositoryInterface.php");

        if (!file_exists($interfacePath)) {
            file_put_contents($interfacePath, $interfaceTemplate);
            return true;
        }

        return false;
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../Stubs/$type.stub");
    }

    protected function updateServiceProvider($model)
    {
        $providerPath = __DIR__ . '/../Providers/LaravelRepositoriesServiceProvider.php';
        $content = file_get_contents($providerPath);

        // Create the binding string
        $binding = "\$this->app->bind({$model}RepositoryInterface::class, {$model}Repository::class);";
        $useRepository = "use App\Repositories\\{$model}\\{$model}Repository;";
        $useRepositoryInterface = "use App\Repositories\\{$model}\\{$model}RepositoryInterface;";

        // Check if bindings and use statements already exist
        if (strpos($content, $binding) === false) {
            // Insert the new binding between our placeholders
            $patternBinding = "/(\/\*\* Start of Repositories \*\/\n)/";
            $replacementBinding = "$1        $binding\n";
            $content = preg_replace($patternBinding, $replacementBinding, $content);
        }

        if (strpos($content, $useRepository) === false) {
            // Insert the use statement for the repository at the top
            $content = preg_replace("/(namespace App\\\\Providers;)/", "$1\n$useRepository", $content);
        }

        if (strpos($content, $useRepositoryInterface) === false) {
            // Insert the use statement for the repository interface at the top
            $content = preg_replace("/(namespace App\\\\Providers;)/", "$1\n$useRepositoryInterface", $content);
        }

        // Save the updated content back to the service provider
        file_put_contents($providerPath, $content);
    }
}
