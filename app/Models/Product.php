<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    // db table
    protected $table = 'product';

    use HasFactory;

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
        // query
        return $this->belongsToMany(Client::class, 'fav', 'product_id', 'client_id')
            ->using(Fav::class)
            ->withPivot('review', 'id')
            ->withTimestamps();
    }
}
