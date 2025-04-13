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

### ✅ Commands

Commands represent executable actions, handled by separate handler classes.

You can generate a new command using the following Artisan command:

```bash
php artisan basepack:command {name}
```

This will create a new command class and its corresponding handler within the `App\Commands` namespace.

If the `{name}` argument includes subpaths (e.g. `User/CreateUser`), these will be reflected in the namespace structure:

```bash
php artisan basepack:command User/CreateUser
```

This will generate:

- `App\Commands\User\CreateUser.php` (the command)
- `App\Commands\User\CreateUserHandler.php` (the handler)

Each command class encapsulates the data needed to perform an action, while the handler contains the business logic to
process the command.

#### Executing Commands

You can execute a command from a controller or service using the executeCommand method:

```php
$this->executeCommand(new CreateUser($data));
```

This approach allows you to keep your controllers thin and delegate logic to dedicated handlers.

### 🔄 Events

You can generate a new event using the following Artisan command:

```bash
php artisan basepack:event {name}
```

This will create a new event class under the `App\Events` namespace.

If the `{name}` argument includes subpaths (e.g. `User/UserCreated`), the namespace will reflect that structure:

```bash
php artisan basepack:event User/UserCreated
```

This will generate:

- `App\Events\User\UserCreated.php` (the event)
- `App\Events\User\UserCreatedHandler.php` (the handler)

#### Publishing Events

Events are typically published from within a command handler, using the publish method:

```php
$this->publish(new UserCreated($data));
```

This allows you to decouple domain events from command execution logic and react to them in other parts of the system,
such as listeners or subscribers.

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
