<?php
namespace App\Integrations\IAM;

use App\Integrations\IAM\Auth;
use App\Integrations\IAM\User;
use App\Integrations\IAM\Permission;

class IAM
{
    public static function auth(): Auth
    {
        return new Auth();
    }

    public static function user(): User
    {
        return new User();
    }

    public static function permission(): Permission
    {
        return new Permission();
    }
}
