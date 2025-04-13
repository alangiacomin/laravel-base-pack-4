# Events

## Create event

You can generate a new event using the following Artisan command:

```bash
php artisan basepack:event {name}
```

This will create a new event class under the `App\Events` namespace.

If the `{name}` argument includes subpaths (e.g. `User/UserCreated`), the namespace will reflect that:

```bash
php artisan basepack:event User/UserCreated
```

This will generate:

- `App\Events\User\UserCreated.php` (the event)
- `App\Events\User\UserCreatedHandler.php` (the handler)

## Publishing Events

Events are typically published from within a command handler using the `publish` method:

```php
class CreateUserHandler extends CommandHandler
{
    public CreateUser $command;

    public function execute()
    {
        $this->publish(new UserCreated($data));
    }
}
```

This allows you to decouple domain events from command execution logic and react to them in other parts of the system,
such as listeners or subscribers.
