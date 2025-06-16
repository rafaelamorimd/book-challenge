<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ReportService;
use App\Services\LogService;
use App\Repository\ReportRepository;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Mockery;
use Mockery\MockInterface;

class ReportServiceTest extends TestCase
{
    private ReportService $reportService;
    private ReportRepository|MockInterface $reportRepository;
    private LogService|MockInterface $logService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportRepository = Mockery::mock(ReportRepository::class);
        $this->logService = Mockery::mock(LogService::class);
        $this->reportService = new ReportService($this->reportRepository, $this->logService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_by_authors_returns_collection(): void
    {
        $this->logService
            ->shouldReceive('info')
            ->twice()
            ->withArgs(function ($message) {
                return in_array($message, [
                    'Buscando autores com livros e assuntos',
                    'Autores encontrados'
                ]);
            });

        $this->reportRepository
            ->shouldReceive('getAuthorsWithBooksAndSubjects')
            ->once()
            ->andReturn(collect([
                [
                    'CodAu' => 1,
                    'Nome' => 'Autor 1',
                    'books' => [
                        [
                            'Codl' => 1,
                            'Titulo' => 'Livro 1',
                            'subjects' => [
                                ['CodAs' => 1, 'Descricao' => 'Assunto 1']
                            ]
                        ]
                    ]
                ]
            ]));

        $result = $this->reportService->byAuthors();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result->first()['CodAu']);
        $this->assertEquals('Autor 1', $result->first()['Nome']);
        $this->assertCount(1, $result->first()['books']);
        $this->assertEquals(1, $result->first()['books'][0]['Codl']);
        $this->assertEquals('Livro 1', $result->first()['books'][0]['Titulo']);
        $this->assertCount(1, $result->first()['books'][0]['subjects']);
        $this->assertEquals(1, $result->first()['books'][0]['subjects'][0]['CodAs']);
        $this->assertEquals('Assunto 1', $result->first()['books'][0]['subjects'][0]['Descricao']);
    }

    public function test_download_generates_pdf_successfully(): void
    {
        $authors = new Collection([
            [
                'CodAu' => 1,
                'Nome' => 'Autor 1',
                'books' => [
                    [
                        'Codl' => 1,
                        'Titulo' => 'Livro 1',
                        'subjects' => [
                            ['CodAs' => 1, 'Descricao' => 'Assunto 1']
                        ]
                    ]
                ]
            ]
        ]);

        $pdfMock = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('setPaper')
            ->once()
            ->with('a4')
            ->andReturnSelf();

        $pdfMock->shouldReceive('setOption')
            ->twice()
            ->withArgs(function ($option, $value) {
                return in_array($option, ['isHtml5ParserEnabled', 'isRemoteEnabled']) &&
                    $value === true;
            })
            ->andReturnSelf();

        $pdfMock->shouldReceive('download')
            ->once()
            ->with(Mockery::pattern('/relatorio_autores_\d{14}\.pdf/'))
            ->andReturn(new \Illuminate\Http\Response('pdf-content'));

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Gerando relatório de autores', ['authors' => $authors]);

        $this->logService
            ->shouldReceive('error')
            ->never();

        PDF::shouldReceive('loadView')
            ->once()
            ->with('reports.author-pdf', ['authors' => $authors])
            ->andReturn($pdfMock);

        $result = $this->reportService->download($authors);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $result);
        $this->assertEquals('pdf-content', $result->getContent());
    }

    public function test_download_throws_exception_when_pdf_generation_fails(): void
    {
        $authors = new Collection([
            [
                'CodAu' => 1,
                'Nome' => 'Autor 1'
            ]
        ]);

        $this->logService
            ->shouldReceive('info')
            ->once()
            ->with('Gerando relatório de autores', ['authors' => $authors]);

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->with('Erro ao gerar relatório de autores', Mockery::type('array'));

        PDF::shouldReceive('loadView')
            ->once()
            ->with('reports.author-pdf', ['authors' => $authors])
            ->andThrow(new \Exception('PDF generation failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('PDF generation failed');

        $this->reportService->download($authors);
    }
}
