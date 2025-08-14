<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class GeomappingUser extends Authenticatable
{
    protected $table = 'geomapping_users';

    protected $fillable = [
        'name',
        'login_code',
        'group',
        'lat_long',
    ];

    protected $hidden = [
    ];

    public $timestamps = true;
}
