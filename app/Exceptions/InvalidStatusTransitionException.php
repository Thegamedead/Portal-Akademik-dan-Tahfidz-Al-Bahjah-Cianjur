<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $currentStatus Current status of the usulan
     * @param string $attemptedAction Action that was attempted
     * @param string|null $requiredStatus Required status for the action (optional)
     * @return void
     */
    public function __construct(string $currentStatus, string $attemptedAction, ?string $requiredStatus = null)
    {
        if ($requiredStatus) {
            $message = "Tidak dapat {$attemptedAction} usulan dengan status '{$currentStatus}'. Status harus '{$requiredStatus}' untuk melakukan tindakan ini.";
        } else {
            $message = "Tidak dapat {$attemptedAction} usulan dengan status '{$currentStatus}'.";
        }

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
                'error' => 'invalid_status_transition'
            ], 409);
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
