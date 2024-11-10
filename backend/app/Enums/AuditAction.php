<?php
namespace App\Enums;

enum AuditAction: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case INSERT = 'insert';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
