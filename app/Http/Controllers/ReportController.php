<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use HandlesApiResponses;

    public function __construct(
        private readonly ReportService $reportService
    ) {
    }

    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $authors = $this->reportService->byAuthors();

        return $this->handleResponse(
            request: $request,
            jsonResponse: fn () => ['data' => $authors],
            inertiaResponse: fn () => ['authors' => $authors],
            view: 'Reports/Author'
        );
    }

    public function download(): Response|JsonResponse
    {
        try {
            $authors = $this->reportService->byAuthors();

            $pdf = $this->reportService->download($authors);

            return $pdf;
        } catch (\DomainException $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                400
            );
        } catch (\Throwable $throwable) {
            return response()->json(
                ['message' => 'Ocorreu um erro não mapeado na aplicação.'],
                500
            );
        }
    }
}
