<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['name', 'type', 'ip_address', 'status'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
