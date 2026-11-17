<?php

namespace App\Exceptions;

use Exception;

class RolloverValidationException extends Exception
{
    /**
     * @var array Validation errors that caused the exception
     */
    public readonly array $validationErrors;

    /**
     * Create a new exception instance.
     *
     * @param string $message Main error message
     * @param array $validationErrors Array of specific validation errors (optional)
     * @return void
     */
    public function __construct(string $message, array $validationErrors = [])
    {
        $this->validationErrors = $validationErrors;

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
            $response = [
                'message' => $this->getMessage(),
                'error' => 'rollover_validation_failed'
            ];

            if (!empty($this->validationErrors)) {
                $response['errors'] = $this->validationErrors;
            }

            return response()->json($response, 422);
        }

        $errors = ['error' => $this->getMessage()];

        if (!empty($this->validationErrors)) {
            $errors = array_merge($errors, $this->validationErrors);
        }

        return back()->withErrors($errors);
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
