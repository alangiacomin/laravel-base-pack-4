<?php

namespace App\Models\User;

use AlanGiacomin\LaravelBasePack\Models\Contracts\IModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements IModel
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'email_verified_at',
        'updated_at',
    ];

    protected $appends = [
        'assigned_roles',
        'assigned_perms',
    ];

    public function assignedRoles(): Attribute
    {
        return Attribute::make(
            get: fn () => (clone $this)->roles()->pluck('name'),
        );
    }

    public function assignedPerms(): Attribute
    {
        return Attribute::make(
            get: fn () => (clone $this)->getAllPermissions()->pluck('name'),
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
