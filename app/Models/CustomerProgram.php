<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProgram extends Model
{
    use HasFactory;

    protected $table = "customer_program";
    protected $primaryKey = "id";
    protected $guarded = [];
}
