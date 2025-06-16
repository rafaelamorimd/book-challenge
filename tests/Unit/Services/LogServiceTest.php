<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LogService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Mockery;
use Mockery\MockInterface;

class LogServiceTest extends TestCase
{
    private LogService $logService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logService = new LogService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_info_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test info message';
        $context = ['key' => 'value'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->info($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_error_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test error message';
        $context = ['error' => 'Test error'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('error')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->error($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_warning_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test warning message';
        $context = ['warning' => 'Test warning'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->warning($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_debug_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test debug message';
        $context = ['debug' => 'Test debug'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('debug')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->debug($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_alert_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test alert message';
        $context = ['alert' => 'Test alert'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('alert')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->alert($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_emergency_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test emergency message';
        $context = ['emergency' => 'Test emergency'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('emergency')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->emergency($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_notice_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test notice message';
        $context = ['notice' => 'Test notice'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('notice')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->notice($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    public function test_critical_logs_message_with_context(): void
    {
        // Arrange
        $message = 'Test critical message';
        $context = ['critical' => 'Test critical'];
        $expectedContext = $this->getExpectedContext($context);

        Log::shouldReceive('channel')
            ->once()
            ->with('daily')
            ->andReturnSelf();

        Log::shouldReceive('critical')
            ->once()
            ->with($message, $expectedContext);

        // Act
        $this->logService->critical($message, $context);

        $this->assertTrue(true); // Verifica se o método foi chamado sem erros
    }

    private function getExpectedContext(array $context): array
    {
        $request = Request::instance();

        return array_merge([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ], $context);
    }
}
