<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case ADMIN_VIEW = 'admin_view';
    case ADMIN_PRIMA = 'admin_prima';
    case ADMIN_USERS = 'admin_users';
}
