<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageImage extends Model
{
    protected $table = 'page_images';
    protected $fillable = [
    'image',
    'page_id',
    'default'
    ];
    //protected $hidden = ['_token'];
    
    
    public function pages(){
        return $this->belongsTo('App\Pages');
    }

    
}
