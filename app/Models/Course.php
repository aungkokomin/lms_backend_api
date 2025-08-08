<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'status',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'bundle_courses', 'course_id', 'bundle_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    // public function carts()
    // {
    //     return $this->belongsToMany(Cart::class, 'cart_items', 'course_id', 'cart_id');
    // }

    public function stripeProduct()
    {
        return $this->morphOne(StripeProduct::class, 'item');
    }
}
