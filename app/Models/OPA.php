<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OPA extends Model
{
    use HasFactory;

    protected $table = 'opas';

    public function user(){
        return $this->belongsTo(User::class);
    }

}
