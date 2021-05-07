<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    public $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'firstName',
        'lastName',
        'email',
        'birthDate',
        'role',
        'study'
    ];

}
