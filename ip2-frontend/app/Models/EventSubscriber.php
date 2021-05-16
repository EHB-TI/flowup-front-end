<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSubscriber extends Model
{
    use HasFactory;

    protected $table = 'event_subscribers';

    public $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'event_id',
    ];

  
}
