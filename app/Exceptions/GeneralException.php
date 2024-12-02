<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class GeneralException extends BaseException
{
    protected $message,$code;
    public function __construct($message = "General Error.",$code = 400)
    {
        // parent::__construct($message,$code);
        $this->message = $message;
        $this->code = $code;
    }
    public function report() {
        Log::info($this->message);

    }
    public function render()
    {
        return response()->json([
            'message' => $this->message,
            'errors' => [$this->message],
        ], $this->code);
    }
}
