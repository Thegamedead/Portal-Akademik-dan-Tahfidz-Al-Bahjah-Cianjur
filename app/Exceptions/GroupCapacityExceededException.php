<?php

namespace App\Exceptions;

use Exception;

class GroupCapacityExceededException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $kelompokName Group name
     * @param int $currentCapacity Current number of students
     * @param int $maxCapacity Maximum capacity
     * @return void
     */
    public function __construct(string $kelompokName, int $currentCapacity, int $maxCapacity)
    {
        $message = "Kelompok {$kelompokName} sudah penuh ({$currentCapacity}/{$maxCapacity} siswa).";

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
                'error' => 'group_capacity_exceeded'
            ], 422);
        }

        return back()->withErrors(['error' => $this->getMessage()]);
    }
}
