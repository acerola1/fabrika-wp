import { defineConfig } from '@playwright/test';

const PORT = Number(process.env.PLAYWRIGHT_PORT || 4173);
const HOST = process.env.PLAYWRIGHT_HOST || '127.0.0.1';

export default defineConfig({
  testDir: 'tests/e2e',
  timeout: 30_000,
  expect: { timeout: 10_000 },
  retries: process.env.CI ? 1 : 0,
  workers: process.env.CI ? 2 : undefined,
  use: {
    baseURL: `http://${HOST}:${PORT}`,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  webServer: {
    command: `npm run dev -- --host ${HOST} --port ${PORT} --strictPort`,
    url: `http://${HOST}:${PORT}`,
    reuseExistingServer: !process.env.CI,
    timeout: 120_000,
  },
  projects: [
    {
      name: 'chromium',
      use: { browserName: 'chromium' },
    },
  ],
});

