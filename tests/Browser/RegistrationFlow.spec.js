// Playwright E2E tests for user registration flow
// Run with: npx playwright test tests/Browser/RegistrationFlow.spec.js

import { test, expect } from '@playwright/test';

test.describe('Registration Flow', () => {
  test('should complete registration successfully', async ({ page }) => {
    // Generate unique email to avoid conflicts
    const timestamp = Date.now();
    const uniqueEmail = `playwright-${timestamp}@example.com`;
    
    // Navigate to registration page with timeout
    await page.goto('/register', { waitUntil: 'domcontentloaded', timeout: 10000 });
    
    // Wait for the form to be visible
    await expect(page.locator('input[name="name"]')).toBeVisible({ timeout: 5000 });
    
    // Fill in the registration form
    await page.fill('input[name="name"]', 'Playwright Test User');
    await page.fill('input[name="email"]', uniqueEmail);
    await page.fill('input[name="password"]', 'password123');
    await page.fill('input[name="password_confirmation"]', 'password123');
    
    // Submit the form and wait for navigation
    // Livewire uses AJAX, so we need to wait for the navigation to complete
    const navigationPromise = page.waitForURL('**/dashboard', { timeout: 15000 });
    await page.click('button[type="submit"]');
    
    // Wait for navigation with timeout
    await navigationPromise;
    
    // Verify we're on the dashboard
    await expect(page).toHaveURL(/.*dashboard/, { timeout: 5000 });
    
    // Verify dashboard content - check for "DASHBOARD" text (uppercase in terminal UI)
    await expect(page.locator('body')).toContainText('DASHBOARD', { timeout: 5000 });
  });

  test('should show validation errors for invalid data', async ({ page }) => {
    await page.goto('/register');
    
    // Try to submit empty form
    await page.click('button[type="submit"]');
    
    // Wait for validation errors
    await page.waitForTimeout(500);
    
    // Verify validation errors are shown
    // (Adjust selectors based on your actual form structure)
    const errors = await page.locator('.error, .text-red-500, [role="alert"]').count();
    expect(errors).toBeGreaterThan(0);
  });
});

