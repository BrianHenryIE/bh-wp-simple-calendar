import { defineConfig, devices } from '@playwright/test';

const baseURL = 'http://localhost:8888';

export default defineConfig({
  testDir: './tests/e2e-pw',
  outputDir: './test-results',
  globalSetup: './tests/e2e-pw/global-setup.ts',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL,
    storageState: './tests/e2e-pw/.auth/storage-state.json',
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
  ],

  webServer: {
    command: 'npx wp-env start',
    url: 'http://localhost:8888',
    reuseExistingServer: true,
  },
});
