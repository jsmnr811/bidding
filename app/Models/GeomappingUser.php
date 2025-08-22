<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class GeomappingUser extends Authenticatable
{
    protected $table = 'geomapping_users';

    protected $fillable = [
        'image',
        'name',
        'firstname',
        'middlename',
        'lastname',
        'ext_name',
        'gender',

        'institution',
        'office',
        'designation',
        'region_id',
        'province_id',

        'email',
        'contact_number',

        'food_restriction',

        'login_code',
        'group_number',
        'table_number',
        'lat_long',
        'role'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
