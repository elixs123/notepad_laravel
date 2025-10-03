<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notes extends Model
{
    protected $table = 'notes';
    
    protected $fillable = [
        'url', 'share_url', 'raw_url', 'markdown_url', 'code_url', 'content'
    ];

    public function user(){
        return $this->belongsTo(UserAuthorization::class, 'id', 'document_id');
    }
}
