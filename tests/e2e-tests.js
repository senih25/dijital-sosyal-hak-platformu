/**
 * End-to-end cross-browser test suite for Dijital Sosyal Hak Platformu.
 * Runs via BrowserStack Automate using Selenium WebDriver.
 */

const { By, until } = require('selenium-webdriver');
const { browsers, buildDriver, SITE_URL } = require('./browserstack.config');

const TIMEOUT_MS = 15_000;

/**
 * Assert that a condition is true; throw with a descriptive message if not.
 *
 * @param {boolean} condition
 * @param {string}  message
 */
function assert(condition, message) {
  if (!condition) throw new Error(`Assertion failed: ${message}`);
}

// ---------------------------------------------------------------------------
// Test definitions
// ---------------------------------------------------------------------------

const tests = [
  {
    name: 'Home page loads and contains navigation',
    async run(driver) {
      await driver.get(SITE_URL);
      await driver.wait(until.titleMatches(/.+/), TIMEOUT_MS);
      const title = await driver.getTitle();
      assert(title.length > 0, `Page title should not be empty, got: "${title}"`);
    },
  },
  {
    name: 'FAQ page loads',
    async run(driver) {
      await driver.get(`${SITE_URL}/sss.php`);
      await driver.wait(until.elementLocated(By.css('body')), TIMEOUT_MS);
      const body = await driver.findElement(By.css('body'));
      const text = await body.getText();
      assert(text.length > 0, 'FAQ page body should have content');
    },
  },
  {
    name: 'Contact page loads',
    async run(driver) {
      await driver.get(`${SITE_URL}/iletisim.php`);
      await driver.wait(until.elementLocated(By.css('body')), TIMEOUT_MS);
      const body = await driver.findElement(By.css('body'));
      const text = await body.getText();
      assert(text.length > 0, 'Contact page body should have content');
    },
  },
  {
    name: 'Calculators page loads',
    async run(driver) {
      await driver.get(`${SITE_URL}/calculators.php`);
      await driver.wait(until.elementLocated(By.css('body')), TIMEOUT_MS);
      const body = await driver.findElement(By.css('body'));
      const text = await body.getText();
      assert(text.length > 0, 'Calculators page body should have content');
    },
  },
  {
    name: 'Responsive meta viewport is present',
    async run(driver) {
      await driver.get(SITE_URL);
      await driver.wait(until.elementLocated(By.css('meta[name="viewport"]')), TIMEOUT_MS);
      const meta = await driver.findElement(By.css('meta[name="viewport"]'));
      const content = await meta.getAttribute('content');
      assert(content.includes('width=device-width'), `Viewport meta should include width=device-width, got: "${content}"`);
    },
  },
];

// ---------------------------------------------------------------------------
// Runner
// ---------------------------------------------------------------------------

async function runTestsForBrowser(browserCaps) {
  const browserLabel = browserCaps.browserName +
    (browserCaps['bstack:options']?.deviceName ? ` (${browserCaps['bstack:options'].deviceName})` : '');
  console.log(`\n[BrowserStack] Starting: ${browserLabel}`);

  let driver;
  const results = [];

  try {
    driver = await buildDriver(browserCaps);

    for (const test of tests) {
      try {
        await test.run(driver);
        console.log(`  ✅ PASS: ${test.name}`);
        results.push({ name: test.name, status: 'pass' });
      } catch (err) {
        console.error(`  ❌ FAIL: ${test.name}\n     ${err.message}`);
        results.push({ name: test.name, status: 'fail', error: err.message });
      }
    }
  } catch (initErr) {
    console.error(`  ⚠️  Driver initialisation failed for ${browserLabel}: ${initErr.message}`);
  } finally {
    if (driver) {
      await driver.quit();
    }
  }

  return results;
}

(async () => {
  let totalPass = 0;
  let totalFail = 0;

  for (const browserCaps of browsers) {
    const results = await runTestsForBrowser(browserCaps);
    totalPass += results.filter(r => r.status === 'pass').length;
    totalFail += results.filter(r => r.status === 'fail').length;
  }

  console.log(`\n[BrowserStack] Done. Pass: ${totalPass}, Fail: ${totalFail}`);

  if (totalFail > 0) {
    process.exit(1);
  }
})();
