# Laravel Base Pack

**Laravel Base Pack** is a Laravel package that provides a basic structure for managing:

- Commands with dedicated handlers
- Events and listeners
- Repositories with interfaces and implementations

It's designed to be integrated into an existing Laravel application, standardizing conventions and promoting code
organization.

## 📦 Installation

Install the package via Composer:

```bash
composer require alangiacomin/laravel-base-pack
```

After installing the package, you must run the following Artisan command to complete the setup:

```bash
php artisan basepack:install
```

This command will publish the configuration and perform the necessary setup for the package to work correctly in your
Laravel project.

## ⚙️ Structure & Usage

### 🗂️ Repositories

The package encourages a standard structure for working with repositories.

- Interfaces: `App\Domain\Repositories\`
- Implementations: `App\Infrastructure\Repositories\`
- Automatic binding via a dedicated service provider

Example:

```php
$user = $this->userRepository->findById($id);
```

To add a new repository:

1. Create the interface in the domain layer
2. Implement it in the infrastructure layer
3. Register the binding in the `RepositoryServiceProvider` (if not automatically handled)

## 🧪 Testing & Maintainability

The separation encouraged by this package helps writing unit tests focused on commands and domain logic, keeping the
codebase maintainable and clean.

## 📁 Examples

The package includes basic examples of:

- Command + Handler
- Event + Listener
- Repository + Interface

You can use them as a starting point for defining your own components.

## 📝 Contributing

Pull requests and issues are welcome!
If you want to propose new features or improvements, feel free to open an issue.
