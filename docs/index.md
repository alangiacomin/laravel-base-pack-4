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

This package is organized around a few core components that help structure application logic in a modular and
maintainable way.

* **Commands**

  Custom Artisan commands that allow you to extend the CLI with domain-specific tasks. Useful for background jobs, setup
  scripts, or tooling.

  → [Learn more](https://alangiacomin.github.io/laravel-base-pack/commands.html)


* **Events**

  Represent something that has happened in the domain. Events help decouple components and trigger side effects such as
  listeners or jobs.

  → [Learn more](https://alangiacomin.github.io/laravel-base-pack/events.html)


* **Repositories**

  A pattern to abstract and encapsulate data access logic, improving testability and separation of concerns.

  → _Coming soon_

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
