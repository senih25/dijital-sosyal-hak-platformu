const { Builder, By, until } = require('selenium-webdriver');

const BROWSERSTACK_USER     = process.env.BROWSERSTACK_USER     || '';
const BROWSERSTACK_KEY      = process.env.BROWSERSTACK_KEY      || '';
const SITE_URL              = process.env.SITE_URL              || 'https://localhost';

const capabilities = {
  'bstack:options': {
    userName:    BROWSERSTACK_USER,
    accessKey:   BROWSERSTACK_KEY,
    projectName: 'Dijital Sosyal Hak Platformu',
    buildName:   `Build-${Date.now()}`,
    sessionName: 'E2E Cross-Browser Tests',
    debug:        true,
    networkLogs:  true,
  },
};

/** Browser / device matrix */
const browsers = [
  // Desktop
  { browserName: 'Chrome',  browserVersion: 'latest', 'bstack:options': { os: 'Windows', osVersion: '11' } },
  { browserName: 'Firefox', browserVersion: 'latest', 'bstack:options': { os: 'Windows', osVersion: '11' } },
  { browserName: 'Safari',  browserVersion: 'latest', 'bstack:options': { os: 'OS X', osVersion: 'Ventura' } },
  { browserName: 'Edge',    browserVersion: 'latest', 'bstack:options': { os: 'Windows', osVersion: '11' } },
  // Mobile
  {
    browserName: 'Chrome',
    'bstack:options': {
      deviceName:    'Samsung Galaxy S23',
      osVersion:     '13.0',
      realMobile:    true,
    },
  },
  {
    browserName: 'Safari',
    'bstack:options': {
      deviceName: 'iPhone 14',
      osVersion:  '16',
      realMobile: true,
    },
  },
];

/**
 * Build a BrowserStack WebDriver for the given browser config.
 *
 * @param {object} browserCaps
 * @returns {Promise<import('selenium-webdriver').WebDriver>}
 */
async function buildDriver(browserCaps) {
  return new Builder()
    .usingServer(`https://${BROWSERSTACK_USER}:${BROWSERSTACK_KEY}@hub.browserstack.com/wd/hub`)
    .withCapabilities({ ...capabilities, ...browserCaps })
    .build();
}

module.exports = { browsers, buildDriver, SITE_URL };
