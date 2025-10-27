<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'external_id',
        'name'
    ];

    public function getApiResponseAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name
        ];
    }
}
