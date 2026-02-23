(function () {
    'use strict';

    const analyticsState = {
        startTime: Date.now(),
        ga4MeasurementId: window.GA4_MEASUREMENT_ID || null,
        hotjarId: window.HOTJAR_SITE_ID || null,
        abTests: {},
        events: [],
        goals: {
            formSubmit: false,
            ctaClick: false,
            scrollDepth50: false,
            scrollDepth90: false,
            timeOnPage30s: false
        }
    };

    const REPORT_STORAGE_KEY = 'dshp_analytics_reports';

    function injectGA4(measurementId) {
        if (!measurementId) return;

        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`;
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag() { window.dataLayer.push(arguments); }
        window.gtag = window.gtag || gtag;

        window.gtag('js', new Date());
        window.gtag('config', measurementId, {
            send_page_view: true,
            anonymize_ip: true,
            allow_google_signals: true
        });

        trackEvent('ga4_initialized', { measurement_id: measurementId });
    }

    function injectHotjar(siteId) {
        if (!siteId) return;
        (function (h, o, t, j, a, r) {
            h.hj = h.hj || function () { (h.hj.q = h.hj.q || []).push(arguments); };
            h._hjSettings = { hjid: siteId, hjsv: 6 };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script'); r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');

        trackEvent('heatmap_initialized', { provider: 'hotjar', site_id: siteId });
    }

    function persistReport(eventName, payload) {
        const entry = {
            event: eventName,
            payload,
            pathname: window.location.pathname,
            timestamp: new Date().toISOString(),
            abTests: analyticsState.abTests
        };

        try {
            const previous = JSON.parse(localStorage.getItem(REPORT_STORAGE_KEY) || '[]');
            previous.push(entry);
            localStorage.setItem(REPORT_STORAGE_KEY, JSON.stringify(previous.slice(-500)));
        } catch (error) {
            // localStorage unavailable.
        }
    }

    function trackEvent(eventName, payload = {}) {
        const normalizedPayload = {
            ...payload,
            page_title: document.title,
            page_path: window.location.pathname
        };

        analyticsState.events.push({ eventName, normalizedPayload, ts: Date.now() });

        if (typeof window.gtag === 'function') {
            window.gtag('event', eventName, normalizedPayload);
        }

        persistReport(eventName, normalizedPayload);
    }

    function markGoal(goalName, payload = {}) {
        if (!Object.prototype.hasOwnProperty.call(analyticsState.goals, goalName)) {
            return;
        }

        if (analyticsState.goals[goalName]) return;
        analyticsState.goals[goalName] = true;

        trackEvent(`goal_${goalName}`, {
            goal_name: goalName,
            ...payload
        });
    }

    function setupBehaviorTracking() {
        document.addEventListener('click', function (event) {
            const target = event.target.closest('a, button, [data-track-click]');
            if (!target) return;

            const label = target.textContent ? target.textContent.trim().slice(0, 80) : 'unknown';
            const href = target.getAttribute('href') || null;
            const isCTA = target.matches('.btn, [data-cta], .cta, [data-track-click]');

            trackEvent('ui_click', {
                label,
                href,
                id: target.id || null,
                class_name: target.className || null,
                is_cta: isCTA
            });

            if (isCTA) {
                markGoal('ctaClick', { label, href });
            }
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            trackEvent('form_submit', {
                form_id: form.id || null,
                form_action: form.action || window.location.pathname,
                form_method: form.method || 'get'
            });

            markGoal('formSubmit', { form_id: form.id || null });
        }, true);

        let maxScroll = 0;
        window.addEventListener('scroll', function () {
            const documentHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
            const viewportHeight = window.innerHeight;
            const scrollTop = window.scrollY || window.pageYOffset;
            const scrolled = Math.min(100, ((scrollTop + viewportHeight) / Math.max(documentHeight, 1)) * 100);

            if (scrolled > maxScroll) {
                maxScroll = scrolled;
            }

            if (maxScroll >= 50) markGoal('scrollDepth50', { depth: Math.round(maxScroll) });
            if (maxScroll >= 90) markGoal('scrollDepth90', { depth: Math.round(maxScroll) });
        }, { passive: true });

        setTimeout(function () {
            markGoal('timeOnPage30s', { seconds: 30 });
        }, 30000);
    }

    function ecommerceTracking() {
        function parsePrice(value) {
            if (typeof value === 'number') return value;
            if (!value) return 0;
            return Number(String(value).replace(/[^0-9,.-]/g, '').replace(',', '.')) || 0;
        }

        window.AnalyticsTracker = window.AnalyticsTracker || {};

        window.AnalyticsTracker.trackProductView = function (item) {
            trackEvent('view_item', {
                currency: item.currency || 'TRY',
                value: parsePrice(item.price),
                items: [{
                    item_id: item.id,
                    item_name: item.name,
                    item_category: item.category || 'digital_product',
                    price: parsePrice(item.price)
                }]
            });
        };

        window.AnalyticsTracker.trackAddToCart = function (item) {
            trackEvent('add_to_cart', {
                currency: item.currency || 'TRY',
                value: parsePrice(item.price) * (item.quantity || 1),
                items: [{
                    item_id: item.id,
                    item_name: item.name,
                    item_category: item.category || 'digital_product',
                    quantity: item.quantity || 1,
                    price: parsePrice(item.price)
                }]
            });
        };

        window.AnalyticsTracker.trackPurchase = function (order) {
            trackEvent('purchase', {
                transaction_id: order.id,
                affiliation: order.affiliation || 'Dijital Sosyal Hak Platformu',
                currency: order.currency || 'TRY',
                value: parsePrice(order.value),
                tax: parsePrice(order.tax || 0),
                shipping: parsePrice(order.shipping || 0),
                coupon: order.coupon || null,
                items: Array.isArray(order.items) ? order.items : []
            });
        };
    }

    function assignABVariant(testName, variants) {
        const key = `dshp_ab_${testName}`;
        const existing = localStorage.getItem(key);
        if (existing && variants.includes(existing)) {
            analyticsState.abTests[testName] = existing;
            return existing;
        }

        const variant = variants[Math.floor(Math.random() * variants.length)];
        localStorage.setItem(key, variant);
        analyticsState.abTests[testName] = variant;
        trackEvent('ab_assigned', { test_name: testName, variant });
        return variant;
    }

    function setupABTests() {
        const tests = document.querySelectorAll('[data-ab-test]');

        tests.forEach(testContainer => {
            const testName = testContainer.getAttribute('data-ab-test');
            const variantNodes = testContainer.querySelectorAll('[data-ab-variant]');
            const variants = Array.from(variantNodes)
                .map(node => node.getAttribute('data-ab-variant'))
                .filter(Boolean);

            if (!testName || variants.length === 0) return;

            const chosenVariant = assignABVariant(testName, variants);

            variantNodes.forEach(node => {
                node.style.display = node.getAttribute('data-ab-variant') === chosenVariant ? '' : 'none';
            });

            trackEvent('ab_impression', { test_name: testName, variant: chosenVariant });
        });

        window.AnalyticsTracker = window.AnalyticsTracker || {};
        window.AnalyticsTracker.trackABConversion = function (testName, conversionName, value) {
            const variant = analyticsState.abTests[testName] || null;
            trackEvent('ab_conversion', {
                test_name: testName,
                variant,
                conversion_name: conversionName,
                value: typeof value === 'number' ? value : undefined
            });
        };
    }

    function exposeReportsAPI() {
        window.AnalyticsReports = {
            export: function () {
                try {
                    return JSON.parse(localStorage.getItem(REPORT_STORAGE_KEY) || '[]');
                } catch (error) {
                    return [];
                }
            },
            clear: function () {
                localStorage.removeItem(REPORT_STORAGE_KEY);
            },
            summarize: function () {
                const records = this.export();
                const byEvent = records.reduce((acc, entry) => {
                    acc[entry.event] = (acc[entry.event] || 0) + 1;
                    return acc;
                }, {});

                return {
                    total_events: records.length,
                    by_event: byEvent,
                    goals: analyticsState.goals,
                    ab_tests: analyticsState.abTests,
                    average_time_on_page_seconds: Math.round((Date.now() - analyticsState.startTime) / 1000)
                };
            }
        };

        trackEvent('analytics_ready', { capabilities: ['ga4', 'heatmap', 'ab_test', 'custom_reports'] });
    }

    injectGA4(analyticsState.ga4MeasurementId);
    injectHotjar(analyticsState.hotjarId);
    setupBehaviorTracking();
    ecommerceTracking();
    setupABTests();
    exposeReportsAPI();
})();
