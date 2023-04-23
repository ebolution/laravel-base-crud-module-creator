# Laravel Base CRUD Module Creator

This Laravel Artisan command simplifies the process of creating new modules by automating the cloning, renaming, and customization of a base CRUD module scaffold. It allows you to easily configure the module's vendor, name, path, author, email, copyright, and license.

## Installation

To install the Laravel Base CRUD Module Creator, run the following command:

````
composer require ebolution/laravel-base-crud-module-creator
````

After installing the package, the command will be automatically registered, and you can use it without additional configuration.

## Usage

To create a new module, run the following command:

````
php artisan ebolution:base-crud-module-creator:create
````

You will be prompted to provide the following information:

1. Module vendor
2. Module name
3. Module path
4. Author name
5. Author email
6. Copyright info
7. License info

The command will then perform the following actions:

1. Create a new directory at the specified module path.
2. Clone the base CRUD module scaffold repository into the new directory.
3. Remove the .git folder in the cloned repository.
4. Replace occurrences of the default vendor name, module name, author, email, copyright, and license with the 
   provided values in .php, .json, and .md files.
5. Update the composer.json file with the new module vendor and name.

Upon successful completion, the new module will be ready for use and customization.

## License

This Laravel Base CRUD Module Creator command is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT)
.