<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuthorization extends Model
{
    protected $table = 'user_authorizations';

    public function users(){
        return $this->belongsToMany(User::class, 'user_authorizations', 'id', 'user_id');
    }

    public function authorizations(){
        return $this->hasMany(Authorization::class, 'id', 'authorization_id');
    }

    public function documents(){
        return $this->hasMany(Notes::class, 'id', 'document_id');
    }
}
