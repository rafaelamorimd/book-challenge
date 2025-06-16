<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

class ReportService
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly LogService $logService
    ) {
    }

    public function byAuthors(): Collection
    {
        $this->logService->info('Buscando autores com livros e assuntos');
        $authors = $this->reportRepository->getAuthorsWithBooksAndSubjects();

        $this->logService->info('Autores encontrados', [
            'authors' => $authors
        ]);
        return $authors;
    }

    public function download(Collection $authors)
    {
        try {
            $this->logService->info('Gerando relatório de autores', [
                'authors' => $authors
            ]);

            $pdf = PDF::loadView('reports.author-pdf', [
                'authors' => $authors
            ]);

            $pdf->setPaper('a4');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf->download(sprintf('relatorio_autores_%s.pdf', date('YmdHis')));

        } catch (\Throwable $th) {
            $this->logService->error('Erro ao gerar relatório de autores', [
                'error' => $th->getMessage()
            ]);
            throw $th;
        }
    }
}
