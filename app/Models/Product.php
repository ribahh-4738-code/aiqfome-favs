<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'title',
        'price',
        'description',
        'category',
        'image',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'favs', 'product_id', 'client_id')
            ->using(Fav::class) 
            ->withTimestamps();
    }
}
