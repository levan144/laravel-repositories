# Laravel Repositories by Levan144

Laravel Repositories is a powerful tool designed to automatically generate repositories and their corresponding interfaces for your Laravel application's models. By doing so, it aims to streamline and simplify the adoption of the Repository Pattern in your projects.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
  - [Generate Repositories](#generate-repositories)
  - [Refreshing Repositories](#refreshing-repositories)
- [Support](#support)
- [License](#license)

## Installation

1. **Install via Composer**:
   ```bash
   composer require levan144/laravel-repositories
   ```
2. Register the Service Provider (only if you are using Laravel versions below 5.5)**:
   Open **config/app.php** and add the following line to the **'providers'** array:
   ```bash
   Levan144\LaravelRepositories\Providers\LaravelRepositoriesServiceProvider::class,
   ```
   Note: If you're using Laravel 5.5 or higher, this step can be skipped due to package auto-discovery.


