<?php

namespace Tests\Browser;

use Symfony\Component\Process\Process;

class PlaywrightHelper
{
    protected string $baseUrl;
    protected ?Process $process = null;

    public function __construct(string $baseUrl = 'http://localhost')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Execute a Playwright test script
     */
    public function run(string $script, array $env = []): array
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'playwright_test_') . '.js';
        file_put_contents($tempFile, $script);

        $command = [
            'npx',
            'playwright',
            'test',
            $tempFile,
            '--reporter=json',
        ];

        $process = new Process($command, base_path(), array_merge([
            'APP_URL' => $this->baseUrl,
        ], $env));

        $process->setTimeout(60);
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        
        unlink($tempFile);

        return [
            'success' => $process->isSuccessful(),
            'output' => $output,
            'error' => $errorOutput,
            'exit_code' => $process->getExitCode(),
        ];
    }

    /**
     * Create a simple Playwright test script
     */
    public function createTestScript(string $testName, callable $testFunction): string
    {
        $code = $testFunction();
        
        return <<<JS
const { test, expect } = require('@playwright/test');

test('{$testName}', async ({ page }) => {
    {$code}
});
JS;
    }
}

