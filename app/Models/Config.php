<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;
    protected $connection = 'user_mysql';
    public $table = 'configs';
    protected $primaryKey = 'id';

}
