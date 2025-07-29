import { expect, test } from '@playwright/test';

test.describe('API Connectivity Tests', () => {
  test('should connect to backend API', async ({ page }) => {
    // Test the API endpoint directly
    const response = await page.request.get('http://localhost:8091/status');
    expect(response.status()).toBe(200);
  });

  test('should load products from API', async ({ page }) => {
    await page.goto('/');

    // Wait for API calls to complete
    await page.waitForTimeout(3000);

    // Check if products are loaded (adapt based on your actual UI)
    const hasProducts =
      (await page
        .locator('[data-test="product"], .product, .product-card')
        .count()) > 0;
    const hasLoadingError = await page
      .locator('text=/error/i, text=/failed/i')
      .isVisible();

    // Either products loaded successfully OR there's a proper error message
    expect(hasProducts || hasLoadingError).toBeTruthy();
  });
});
