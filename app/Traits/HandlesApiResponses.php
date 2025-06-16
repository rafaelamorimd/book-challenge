<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

trait HandlesApiResponses
{
    /**
     * Manipula a resposta da API de forma consistente.
     *
     * @param Request $request
     * @param callable $jsonResponse
     * @param callable $inertiaResponse
     * @param string|null $view
     * @param int $status
     * @return JsonResponse|RedirectResponse|InertiaResponse
     */
    protected function handleResponse(
        Request $request,
        callable $jsonResponse,
        callable $inertiaResponse,
        ?string $view = null,
        int $status = 200
    ): JsonResponse|RedirectResponse|InertiaResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($jsonResponse(), $status);
        }

        if ($view) {
            return Inertia::render($view, $inertiaResponse());
        }

        return $inertiaResponse();
    }

    /**
     * Manipula a resposta para listagem de recursos.
     */
    protected function handleIndexResponse(
        Request $request,
        mixed $paginatedData,
        array $filters,
        string $view,
        ?string $filterKey = null
    ): JsonResponse|InertiaResponse {
        return $this->handleResponse(
            request: $request,
            jsonResponse: fn () => [
                'data' => $paginatedData->items(),
                'meta' => [
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'per_page' => $paginatedData->perPage(),
                    'total' => $paginatedData->total(),
                ],
            ],
            inertiaResponse: fn () => [
                $filterKey ?? 'items' => $paginatedData,
                'filters' => $filters,
            ],
            view: $view
        );
    }

    /**
     * Manipula a resposta para criação de recursos.
     */
    protected function handleStoreResponse(
        Request $request,
        mixed $data,
        string $message = 'Recurso criado com sucesso!',
        ?string $redirectRoute = null,
        int $status = 201
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $data
            ], $status);
        }

        return redirect()
            ->route($redirectRoute ?? 'books.index')
            ->with('success', $message);
    }

    /**
     * Manipula a resposta para edição/visualização de recursos.
     */
    protected function handleEditResponse(
        Request $request,
        mixed $data,
        array $additionalData,
        string $view,
        ?string $dataKey = null
    ): JsonResponse|InertiaResponse {
        return $this->handleResponse(
            request: $request,
            jsonResponse: fn () => $data,
            inertiaResponse: fn () => array_merge(
                [$dataKey ?? 'item' => $data],
                $additionalData
            ),
            view: $view
        );
    }

    /**
     * Manipula a resposta para atualização de recursos.
     */
    protected function handleUpdateResponse(
        Request $request,
        mixed $data,
        string $message = 'Recurso atualizado com sucesso!',
        ?string $redirectRoute = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $data
            ]);
        }

        return redirect()
            ->route($redirectRoute ?? 'books.index')
            ->with('success', $message);
    }

    /**
     * Manipula a resposta para exclusão de recursos.
     */
    protected function handleDestroyResponse(
        Request $request,
        string $message = 'Recurso excluído com sucesso!',
        ?string $redirectRoute = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
            ]);
        }

        return redirect()
            ->route($redirectRoute ?? 'books.index')
            ->with('success', $message);
    }
}
