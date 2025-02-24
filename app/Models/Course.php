<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
  protected $guarded=[];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function socialLink(): HasOne
    {
        return $this->hasOne(SocialLink::class);
    }
}
