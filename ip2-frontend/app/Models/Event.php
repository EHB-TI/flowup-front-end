<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    public $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'startsAt',
        'endsAt',
        'location'
    ];
}
