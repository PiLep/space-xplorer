// Playwright configuration for E2E tests
// This can be used with Pest or standalone Playwright tests
// Using .cjs extension because package.json has "type": "module"

module.exports = {
  testDir: './tests/Browser',
  timeout: 30000, // Timeout global par test
  globalTimeout: 120000, // Timeout global pour tous les tests (2 minutes max)
  expect: {
    timeout: 5000,
  },
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  // Use 'list' reporter by default to avoid blocking terminal
  // HTML report is generated separately via 'npm run test:e2e:report'
  reporter: process.env.PLAYWRIGHT_HTML_REPORT === 'true'
    ? [['list'], ['html', { outputFolder: 'playwright-report', open: 'never' }]]
    : [['list']],
  use: {
    // In Docker/Sail, the server is accessible via localhost from within the container
    baseURL: process.env.APP_URL || 'http://localhost',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    // Always use headless mode in Docker/CI environments
    headless: true,
    // Increase navigation timeout for Livewire AJAX requests
    navigationTimeout: 15000,
  },
  projects: [
    {
      name: 'chromium',
      use: {
        ...require('@playwright/test').devices['Desktop Chrome'],
      },
    },
  ],
  // Disable webServer since Sail manages the server
  // Make sure Sail is running before executing tests: ./vendor/bin/sail up -d
  // webServer: {
  //   command: 'php artisan serve',
  //   url: 'http://localhost:8000',
  //   reuseExistingServer: !process.env.CI,
  //   timeout: 120000,
  // },
};

