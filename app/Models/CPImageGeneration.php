<?php
// app/Models/CPImageGeneration.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPImageGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'prompt', 'generated_image', 'midjourney_message_id', 'image_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


