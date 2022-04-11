<?php

namespace App\Models;

use App\Models\boards;
use App\Models\cards;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function cards()
    {
        return $this->hasMany(cards::class);
    }
    public function boards()
    {
        return $this->hasMany(boards::class);
    }
}
