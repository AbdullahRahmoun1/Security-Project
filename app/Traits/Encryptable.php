<?php

namespace App\Traits;
use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    /**
     * Automatically encrypt attributes before saving to the database.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encrypted??[])) {
            $value = $value !== null ? Crypt::encryptString($value) : null;
        }
        parent::setAttribute($key, $value);
    }

    /**
     * Automatically decrypt attributes after retrieving from the database.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encrypted??[]) && $value !== null) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Handle decryption error, if necessary
            }
        }
        return $value;
    }
}
