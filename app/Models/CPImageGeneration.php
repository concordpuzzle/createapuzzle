<?php
// database/migrations/xxxx_xx_xx_create_cp_image_generations_table.php

// app/Models/CPImageGeneration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPImageGeneration extends Model
{
    use HasFactory;

    protected $fillable = ['prompt', 'generated_image'];
}


