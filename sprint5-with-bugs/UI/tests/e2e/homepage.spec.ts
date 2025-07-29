import { expect, test } from '@playwright/test';

test.describe('Homepage Tests', () => {
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/');

    // Wait for the page to load
    await page.waitForLoadState('networkidle');

    // Check if the page has loaded properly
    await expect(page).toHaveTitle(/Practice Software Testing/i);
  });

  test('should display main navigation', async ({ page }) => {
    await page.goto('/');

    // Check for common navigation elements
    await expect(page.locator('text=Home')).toBeVisible();
  });
});
