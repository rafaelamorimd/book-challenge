<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait ApiResponse
{
    protected function handleResponse(
        Request $request,
        mixed $data,
        string $message,
        int $status = 200,
        ?string $redirectRoute = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $data
            ], $status);
        }

        return redirect()
            ->route($redirectRoute ?? 'books.index')
            ->with('success', $message);
    }

    protected function handleError(
        Request $request,
        string $message,
        int $status = 400,
        ?string $redirectRoute = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'error' => true
            ], $status);
        }

        return redirect()
            ->route($redirectRoute ?? 'books.index')
            ->with('error', $message);
    }
}
