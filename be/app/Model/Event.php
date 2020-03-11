<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable
    = [
        'event_name',
        'from',
        'to',
        'days',
    ];
}
