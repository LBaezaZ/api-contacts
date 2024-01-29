<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contacts extends Model
{
    protected $table = 'contacts';
    protected $hidden = [
        'user_id',
    ];
    public function user(){
        return $this->hasMany('api\User');
    }
}
