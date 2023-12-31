<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    public $primaryKey = 'student_id';
    protected $guarded = [];

    public function session(){
        return $this->belongsTo(Sessions::class,'session_id','session_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
