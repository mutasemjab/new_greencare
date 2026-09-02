<?php

namespace App\Traits;

trait Translatable
{
    public function getAttribute($key)
    {
        if (
            in_array($key, $this->translatable ?? []) &&
            request()->is('api/*') &&
            app()->getLocale() === 'en'
        ) {
            $enValue = parent::getAttribute($key . '_en');
            if (!empty($enValue)) {
                return $enValue;
            }
        }

        return parent::getAttribute($key);
    }
}
