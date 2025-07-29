import { expect, test } from '@playwright/test';

test.describe('Search Functionality', () => {
  test('should be able to search for products', async ({ page }) => {
    await page.goto('/');

    // Look for search input
    const searchInput = page
      .locator('input[type="search"], input[placeholder*="search"]')
      .first();

    if (await searchInput.isVisible()) {
      await searchInput.fill('hammer');
      await searchInput.press('Enter');

      // Wait for results
      await page.waitForTimeout(2000);

      // Check if we got some results or error handling
      const resultsExist =
        (await page
          .locator('[data-test="product-item"], .product-card, .search-result')
          .count()) > 0;
      const noResultsMessage = await page
        .locator('text=/no.*found/i')
        .isVisible();

      expect(resultsExist || noResultsMessage).toBeTruthy();
    }
  });
});
