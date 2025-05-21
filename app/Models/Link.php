<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    //
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'url', 'title', 'task_id', 'description', 'type'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
