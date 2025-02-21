<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    public function whenSet($attribute, $callback = null)
    {
        return $this->when(isset($attribute), is_callable($callback) ? $callback($attribute) : $attribute );
    }
}
