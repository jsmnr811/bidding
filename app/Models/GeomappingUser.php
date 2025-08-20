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
        'firstname',
        'middlename',
        'lastname',
        'region_id',
        'province_id',
        'affiliation',
        'designation',
        'gender',
        'phone',
        'email',
        'vulnerability',
        'food_restriction',
        'image',
    ];

    protected $hidden = [];

    public $timestamps = true;
}
