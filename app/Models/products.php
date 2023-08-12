<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\sections;

class products extends Model
{
    use HasFactory;

    protected $fillable = [
        'Product_name',
        'description',
        'created_at',

        'section_id',
        'updated_at',
    ];

    public function section(){
        return $this->belongsTo('app\Models\sections');
    }
}
