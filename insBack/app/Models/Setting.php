<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['user_id', 'type', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function source()
    {
        return $this->belongsTo(Source::class, 'name', 'id');
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'name', 'id');
    }
}
