<?php

namespace App\Exceptions;

use Exception;

class InvalidLevelProgressionException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message Custom error message
     * @return void
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message, 422);
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
                'error' => 'invalid_level_progression'
            ], 422);
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
