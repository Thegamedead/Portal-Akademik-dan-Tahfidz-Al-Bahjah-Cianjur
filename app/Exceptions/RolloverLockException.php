<?php

namespace App\Exceptions;

use Exception;

class RolloverLockException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message Custom error message (optional)
     * @return void
     */
    public function __construct(string $message = "Rollover sedang diproses oleh admin lain, silakan tunggu")
    {
        parent::__construct($message, 409);
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'rollover_lock_conflict'
            ], 409);
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
