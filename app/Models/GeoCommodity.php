<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoCommodity extends Model
{
    protected $table = 'geo_commodities';

    protected $guarded = ['id'];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
}
