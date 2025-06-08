<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    public function getHashidAttribute()
    {
        return Hashids::encode($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = Hashids::decode($value);
        if (count($id) === 0) {
            abort(404);
        }
        return $this->findOrFail($id[0]);
    }
} 