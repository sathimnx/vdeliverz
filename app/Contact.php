<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'email', 'mobile', 'fb', 'insta', 'wp'
    ];
}