# BrowserStack Cross-Browser Testing – Setup Guide

## Overview

[BrowserStack Automate](https://www.browserstack.com/automate) provides access to 3500+ real devices and browsers for cross-browser and responsive design testing. This guide explains how to run automated E2E tests for Dijital Sosyal Hak Platformu.

---

## 1. Prerequisites

- Node.js 18+
- A BrowserStack account (free trial available)
- BrowserStack **Username** and **Access Key**

---

## 2. Installation

```bash
npm install selenium-webdriver
```

---

## 3. Configuration

Set the following environment variables:

| Variable             | Description                       |
|----------------------|-----------------------------------|
| `BROWSERSTACK_USER`  | Your BrowserStack username        |
| `BROWSERSTACK_KEY`   | Your BrowserStack access key      |
| `SITE_URL`           | Public URL of the site under test |

For local development, export them in your shell:

```bash
export BROWSERSTACK_USER=youruser
export BROWSERSTACK_KEY=yourkey
export SITE_URL=https://your-site.example.com
```

---

## 4. Running Tests

```bash
node tests/e2e-tests.js
```

### Test matrix

Tests run on:

| Browser  | Platform           |
|----------|--------------------|
| Chrome (latest)  | Windows 11        |
| Firefox (latest) | Windows 11        |
| Safari (latest)  | macOS Ventura     |
| Edge (latest)    | Windows 11        |
| Chrome           | Samsung Galaxy S23 |
| Safari           | iPhone 14          |

### Test coverage

| Test | Description |
|------|-------------|
| Home page loads | Verifies page title is non-empty |
| FAQ page loads | Verifies body content is present |
| Contact page loads | Verifies body content is present |
| Calculators page loads | Verifies body content is present |
| Responsive meta viewport | Checks `<meta name="viewport">` includes `width=device-width` |

---

## 5. Adding New Tests

Add a new test object to the `tests` array in `tests/e2e-tests.js`:

```js
{
  name: 'My new test',
  async run(driver) {
    await driver.get(`${SITE_URL}/my-page.php`);
    await driver.wait(until.elementLocated(By.css('h1')), 15_000);
    const heading = await driver.findElement(By.css('h1'));
    const text = await heading.getText();
    assert(text.length > 0, 'Heading should have text');
  },
},
```

---

## 6. GitHub Actions

Add two repository secrets:

| Secret name          | Description                  |
|----------------------|------------------------------|
| `BROWSERSTACK_USER`  | BrowserStack username        |
| `BROWSERSTACK_KEY`   | BrowserStack access key      |
| `SITE_URL`           | Public URL of the deployed site |

The workflow `.github/workflows/browserstack-tests.yml` runs the full E2E suite on every push to `main` / `master` and on pull requests.

---

## 7. Troubleshooting

| Problem | Solution |
|---|---|
| `Driver initialisation failed` | Verify `BROWSERSTACK_USER` and `BROWSERSTACK_KEY` are correct. |
| Tests fail with timeout | Increase `TIMEOUT_MS` in `e2e-tests.js` or check if the site is reachable. |
| `selenium-webdriver` not found | Run `npm install selenium-webdriver` in the project root. |
| All tests skipped | Ensure `SITE_URL` points to a publicly accessible URL. |
