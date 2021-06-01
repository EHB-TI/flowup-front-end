<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    public $primaryKey = 'id';

    protected $fillable = [
        'firstName',
        'name',
        'email',
        'password',
        'birthday',
        'role',
        'study'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
