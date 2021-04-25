<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class event extends Model
{
    use HasFactory;
    protected $table = 'events';

    public $primaryKey = 'id';

    public $timestamps = true;
    
    protected $fillable = [
        'name',
        'startEvent',
        'endEvent',
        'description',
        'location',
    ];
}
