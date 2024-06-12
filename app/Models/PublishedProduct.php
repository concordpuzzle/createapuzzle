<?php
// app/Models/PublishedProduct.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishedProduct extends Model
{
    use HasFactory;

    public function image()
    {
        return $this->belongsTo(CPImageGeneration::class, 'image_id');
    }


    protected $fillable = ['image_id', 'user_id', 'title', 'description', 'product_id', 'product_url', 'cropped_image', 'likes_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'product_id');
    }
}
