<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publisher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo'];

    protected $casts = [
        'logo' => 'string',
    ];
    protected function initializeCasts()
    {
        if (!app()->runningUnitTests()) {
            $this->casts['logo'] = 'encrypted';
        }
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
