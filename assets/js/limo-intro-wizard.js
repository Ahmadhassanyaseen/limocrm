/**
 * … → Integration → Leads → Add lead → back to Leads.
 * Powered by Driver.js
 * @see https://driverjs.com/
 */
(function () {
  'use strict';

  if (!window.LIMO_INTRO || !window.LIMO_INTRO.userId) {
    return;
  }

  var createDriver =
    window.driver && window.driver.js && window.driver.js.driver
      ? window.driver.js.driver
      : null;

  if (typeof createDriver !== 'function') {
    console.warn('limo-intro-wizard: driver.js not loaded.');
    return;
  }

  var intro = window.LIMO_INTRO;

  var userId = String(intro.userId);

  var STORAGE_KEY = 'limocrm_intro_dashboard_v2_' + userId;
  var STEP_KEY = 'limocrm_intro_dash_step_v2_' + userId;
  var PENDING_KEY = 'limocrm_intro_pending_vehicles_v2_' + userId;
  var FULL_DONE_KEY = 'limocrm_intro_full_done_v2_' + userId;
  var VEHICLE_FORM_LAUNCH_KEY =
    'limocrm_intro_vehicle_form_launch_v2_' + userId;

  /** Set after vehicle-form intro “Finish”; cleared when fleet wrap-up tour ends. */
  var POST_VEHICLE_FLEET_KEY =
    'limocrm_intro_post_vehicle_fleet_v2_' + userId;

  /** Set when navigating from fleet to email-templates intro; cleared when tour completes. */
  var EMAIL_INTRO_PENDING_KEY =
    'limocrm_intro_email_pending_v2_' + userId;

  /** Set when leaving email leg for integration; cleared in markFullyDone. */
  var INTEGRATION_INTRO_PENDING_KEY =
    'limocrm_intro_integration_pending_v2_' + userId;

  /** Set when leaving integration for leads list intro. */
  var LEADS_INTRO_PENDING_KEY =
    'limocrm_intro_leads_pending_v2_' + userId;

  /** Set when leaving leads for add-lead form intro. */
  var ADD_LEAD_INTRO_PENDING_KEY =
    'limocrm_intro_add_lead_pending_v2_' + userId;

  /** Total steps (tour completes after add-lead, then user returns to Leads). */
  var TOTAL_INTRO_STEPS = 27;

  var vehiclesHref = intro.vehiclesHref || './vehicles.php';
  var indexHref = intro.indexHref || './index.php';
  var emailTemplatesHref =
    intro.emailTemplatesHref || './email_templates.php';
  var integrationHref =
    intro.integrationHref || './integration.php';
  var leadsHref = intro.leadsHref || './leads.php';
  var addLeadHref = intro.addLeadHref || './add_lead.php';

  var stepMeta = {
    totalHumanSteps: 0,
    globalOffset: 0,
    isVehiclesTail: false,
  };

  var navigatingAway = false;
  var ignoreDriverEvents = false;
  var driverInstance = null;

  var leadsResizeObserver = null;
  var leadsReflowTimers = [];

  // =========================================================
  // Storage Helpers
  // =========================================================

  function storageGet(k) {
    try {
      return window.localStorage.getItem(k);
    } catch (e) {
      return null;
    }
  }

  function storageSet(k, v) {
    try {
      window.localStorage.setItem(k, v);
    } catch (e) {}
  }

  function storageRemove(k) {
    try {
      window.localStorage.removeItem(k);
    } catch (e) {}
  }

  function clearAllIntroKeys() {
    storageRemove(STORAGE_KEY);
    storageRemove(STEP_KEY);
    storageRemove(PENDING_KEY);
    storageRemove(FULL_DONE_KEY);
    storageRemove(VEHICLE_FORM_LAUNCH_KEY);
    storageRemove(POST_VEHICLE_FLEET_KEY);
    storageRemove(EMAIL_INTRO_PENDING_KEY);
    storageRemove(INTEGRATION_INTRO_PENDING_KEY);
    storageRemove(LEADS_INTRO_PENDING_KEY);
    storageRemove(ADD_LEAD_INTRO_PENDING_KEY);
  }

  // =========================================================
  // Flags
  // =========================================================

  function getFlags() {
    var p = new URLSearchParams(window.location.search || '');

    return {
      introResume: p.get('intro') === 'resume',
      introRestart: p.get('intro') === 'restart',

      limoIntro:
        p.get('limo_intro') === '1' ||
        p.get('limo_intro') === 'resume',

      vehicleForm:
        p.get('limo_intro') === 'vehicle_form',

      /** Header “Intro” replay on vehicle form (bypasses one-shot launch lock). */
      forceVehicleIntro:
        p.get('force_vehicle_intro') === '1',

      /** Return to vehicles list after saving the vehicle-form intro leg. */
      fleetOverview:
        p.get('limo_intro') === 'fleet_overview',

      emailTemplates:
        p.get('limo_intro') === 'email_templates',

      /** Re-run only the email-templates intro leg (e.g. from header). */
      forceEmailIntro: p.get('force_email_intro') === '1',

      integrationPage:
        p.get('limo_intro') === 'integration',

      forceIntegrationIntro:
        p.get('force_integration_intro') === '1',

      leadsIntro:
        p.get('limo_intro') === 'leads',

      forceLeadsIntro: p.get('force_leads_intro') === '1',

      addLeadIntro:
        p.get('limo_intro') === 'add_lead',

      forceAddLeadIntro: p.get('force_add_lead_intro') === '1',
    };
  }

  function appendQuery(url, query) {
    var sep = url.indexOf('?') >= 0 ? '&' : '?';
    return url + sep + query;
  }

  function stripVehicleFormQueryParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'vehicle_form') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function stripFleetOverviewParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'fleet_overview') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function stripEmailTemplatesParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'email_templates') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function stripIntegrationParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'integration') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function stripLeadsIntroParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'leads') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function stripAddLeadIntroParam() {
    if (!window.history || !window.history.replaceState) {
      return;
    }
    try {
      var u = new URL(window.location.href);
      if (u.searchParams.get('limo_intro') !== 'add_lead') {
        return;
      }
      u.searchParams.delete('limo_intro');
      var q = u.searchParams.toString();
      window.history.replaceState(
        {},
        '',
        u.pathname + (q ? '?' + q : '') + u.hash
      );
    } catch (e) {}
  }

  function shouldAutoRunIntro() {
    var flags = getFlags();

    if (flags.introRestart) {
      return true;
    }

    if (storageGet(STORAGE_KEY) === '1') {
      return false;
    }

    if (
      flags.vehicleForm &&
      isVehicleFormPage() &&
      !flags.forceVehicleIntro
    ) {
      if (storageGet(VEHICLE_FORM_LAUNCH_KEY) === '1') {
        stripVehicleFormQueryParam();
        return false;
      }
    }

    if (
      flags.fleetOverview &&
      isVehiclesListPage() &&
      storageGet(POST_VEHICLE_FLEET_KEY) !== '1'
    ) {
      stripFleetOverviewParam();
      return false;
    }

    if (
      flags.emailTemplates &&
      isEmailTemplatesPage() &&
      !flags.forceEmailIntro
    ) {
      if (storageGet(EMAIL_INTRO_PENDING_KEY) !== '1') {
        stripEmailTemplatesParam();
        return false;
      }
    }

    if (
      flags.integrationPage &&
      isIntegrationPage() &&
      !flags.forceIntegrationIntro
    ) {
      if (storageGet(INTEGRATION_INTRO_PENDING_KEY) !== '1') {
        stripIntegrationParam();
        return false;
      }
    }

    if (
      flags.leadsIntro &&
      isLeadsListPage() &&
      !flags.forceLeadsIntro
    ) {
      if (storageGet(LEADS_INTRO_PENDING_KEY) !== '1') {
        stripLeadsIntroParam();
        return false;
      }
    }

    if (
      flags.addLeadIntro &&
      isAddLeadPage() &&
      !flags.forceAddLeadIntro
    ) {
      if (storageGet(ADD_LEAD_INTRO_PENDING_KEY) !== '1') {
        stripAddLeadIntroParam();
        return false;
      }
    }

    return true;
  }

  // =========================================================
  // Page Detection
  // =========================================================

  function isDashboardPage() {
    return !!(
      document.getElementById('intro-wizard-stats') ||
      document.getElementById('intro-wizard-sales') ||
      document.getElementById('intro-wizard-leads-report')
    );
  }

  function isVehicleFormPage() {
    return !!document.getElementById('intro-vehicle-basic');
  }

  function isVehiclesListPage() {
    return !!(
      document.getElementById('intro-fleet-stats') ||
      document.getElementById('intro-fleet-table') ||
      document.getElementById('add-vehicle-btn')
    );
  }

  function shouldShowFleetOverviewTour(flags) {
    if (!isVehiclesListPage() || isVehicleFormPage()) {
      return false;
    }

    if (storageGet(FULL_DONE_KEY) === '1') {
      return false;
    }

    if (storageGet(POST_VEHICLE_FLEET_KEY) !== '1') {
      return false;
    }

    var s = readSavedGlobalStep();

    if (flags.fleetOverview) {
      return true;
    }

    return s >= 11 && s <= 12;
  }

  function isEmailTemplatesPage() {
    return !!(
      document.getElementById('et-stats') ||
      document.getElementById('intro-email-table') ||
      document.getElementById('add-email-template-btn')
    );
  }

  function shouldShowEmailTemplatesTour(flags) {
    if (!isEmailTemplatesPage()) {
      return false;
    }

    if (storageGet(FULL_DONE_KEY) === '1') {
      return false;
    }

    if (flags.forceEmailIntro) {
      return true;
    }

    if (storageGet(EMAIL_INTRO_PENDING_KEY) !== '1') {
      return false;
    }

    var s = readSavedGlobalStep();

    if (flags.emailTemplates) {
      return true;
    }

    return s >= 13 && s <= 15;
  }

  function isIntegrationPage() {
    return !!(
      document.getElementById('intro-widget-preview') ||
      document.getElementById('intro-widget-code') ||
      document.getElementById('intro-widget-domains')
    );
  }

  function shouldShowIntegrationTour(flags) {
    if (!isIntegrationPage()) {
      return false;
    }

    if (storageGet(FULL_DONE_KEY) === '1') {
      return false;
    }

    if (flags.forceIntegrationIntro) {
      return true;
    }

    if (storageGet(INTEGRATION_INTRO_PENDING_KEY) !== '1') {
      return false;
    }

    var s = readSavedGlobalStep();

    if (flags.integrationPage) {
      return true;
    }

    return s >= 16 && s <= 18;
  }

  function isLeadsListPage() {
    return !!(
      document.getElementById('intro-leads-stats') ||
      document.getElementById('intro-leads-table') ||
      document.getElementById('add-lead-btn')
    );
  }

  function shouldShowLeadsListTour(flags) {
    if (!isLeadsListPage()) {
      return false;
    }

    if (storageGet(FULL_DONE_KEY) === '1') {
      return false;
    }

    if (flags.forceLeadsIntro) {
      return true;
    }

    if (storageGet(LEADS_INTRO_PENDING_KEY) !== '1') {
      return false;
    }

    var s = readSavedGlobalStep();

    if (flags.leadsIntro) {
      return true;
    }

    return s >= 19 && s <= 21;
  }

  function isAddLeadPage() {
    return !!(
      document.getElementById('intro-lead-information') ||
      document.getElementById('intro-lead-trip-details') ||
      document.getElementById('intro-lead-select-vehicle') ||
      document.getElementById('intro-lead-live-summary') ||
      document.getElementById('al-save-btn')
    );
  }

  function shouldShowAddLeadTour(flags) {
    if (!isAddLeadPage()) {
      return false;
    }

    if (storageGet(FULL_DONE_KEY) === '1') {
      return false;
    }

    if (flags.forceAddLeadIntro) {
      return true;
    }

    if (storageGet(ADD_LEAD_INTRO_PENDING_KEY) !== '1') {
      return false;
    }

    var s = readSavedGlobalStep();

    if (flags.addLeadIntro) {
      return true;
    }

    return s >= 22 && s <= 26;
  }

  function readSavedGlobalStep() {
    var raw = storageGet(STEP_KEY);

    if (raw === null || raw === '') {
      return 0;
    }

    var n = parseInt(raw, 10);

    return isNaN(n) ? 0 : n;
  }

  function currentGlobalStepIndex(activeIndex) {
    var i = typeof activeIndex === 'number' ? activeIndex : 0;
    return stepMeta.globalOffset + i;
  }

  function persistProgressFromDriver(driver) {
    var drv = driver || driverInstance;

    if (!drv || typeof drv.getActiveIndex !== 'function') {
      return;
    }

    var ix = drv.getActiveIndex();

    if (typeof ix !== 'number') {
      return;
    }

    storageSet(
      STEP_KEY,
      String(currentGlobalStepIndex(ix))
    );
  }

  function markDismissed() {
    storageSet(STORAGE_KEY, '1');
  }

  function markFullyDone() {
    markDismissed();

    storageSet(FULL_DONE_KEY, '1');

    storageRemove(PENDING_KEY);

    storageRemove(POST_VEHICLE_FLEET_KEY);

    storageRemove(EMAIL_INTRO_PENDING_KEY);

    storageRemove(INTEGRATION_INTRO_PENDING_KEY);

    storageRemove(LEADS_INTRO_PENDING_KEY);

    storageRemove(ADD_LEAD_INTRO_PENDING_KEY);

    storageSet(STEP_KEY, String(TOTAL_INTRO_STEPS));

    storageSet(VEHICLE_FORM_LAUNCH_KEY, '1');
  }

  // =========================================================
  // Leads Reflow
  // =========================================================

  function clearLeadsReflow() {
    if (leadsResizeObserver) {
      leadsResizeObserver.disconnect();
      leadsResizeObserver = null;
    }

    leadsReflowTimers.forEach(function (tid) {
      clearTimeout(tid);
    });

    leadsReflowTimers = [];
  }

  function attachLeadsReflowIfNeeded() {
    clearLeadsReflow();

    var el = document.getElementById(
      'intro-wizard-leads-report'
    );

    if (
      !el ||
      typeof ResizeObserver === 'undefined' ||
      !driverInstance
    ) {
      return;
    }

    leadsResizeObserver = new ResizeObserver(function () {
      if (
        driverInstance &&
        typeof driverInstance.refresh === 'function'
      ) {
        driverInstance.refresh();
      }
    });

    leadsResizeObserver.observe(el);

    [0, 150, 400, 900, 1800].forEach(function (ms) {
      leadsReflowTimers.push(
        setTimeout(function () {
          if (
            driverInstance &&
            typeof driverInstance.refresh === 'function'
          ) {
            driverInstance.refresh();
          }
        }, ms)
      );
    });
  }

  // =========================================================
  // Tour Builder
  // =========================================================

  function buildTourConfig() {
    var flags = getFlags();

    // =====================================================
    // VEHICLE FORM PAGE
    // =====================================================

    if (
      (flags.vehicleForm || flags.forceVehicleIntro) &&
      isVehicleFormPage()
    ) {
      stepMeta = {
        totalHumanSteps: TOTAL_INTRO_STEPS,
        globalOffset: 5,
        isVehiclesTail: false,
      };

      return {
        steps: [
          {
            element: '#intro-vehicle-basic',
            popover: {
              title: 'Basic Information',
              description:
                'Enter the vehicle name, category, make, model, and core information.',
              showProgress: true,
              progressText: 'Step 6 of ' + TOTAL_INTRO_STEPS,
              disableButtons: ['previous'],
            },
          },

          {
            element: '#intro-vehicle-image',
            popover: {
              title: 'Vehicle Image',
              description:
                'Upload high-quality vehicle images.',
              showProgress: true,
              progressText: 'Step 7 of ' + TOTAL_INTRO_STEPS,
            },
          },

          {
            element: '#intro-vehicle-pricing',
            popover: {
              title: 'Pricing',
              description:
                'Configure pricing and rates.',
              showProgress: true,
              progressText: 'Step 8 of ' + TOTAL_INTRO_STEPS,
            },
          },

          {
            element: '#intro-vehicle-details',
            popover: {
              title: 'Details & Features',
              description:
                'Add amenities, passenger count, luggage, and features.',
              showProgress: true,
              progressText: 'Step 9 of ' + TOTAL_INTRO_STEPS,
            },
          },

          // {
          //   element: '#intro-vehicle-preview',
          //   popover: {
          //     title: 'Live Preview',
          //     description:
          //       'Preview how the vehicle appears before saving.',
          //     showProgress: true,
          //     progressText: 'Step 10 of ' + TOTAL_INTRO_STEPS,
          //   },
          // },

          {
            element: '#save-btn',
            popover: {
              title: 'Save Vehicle',
              description:
                'Save the vehicle, then continue through fleet, templates, integration, and leads.',
              showProgress: true,
              progressText: 'Step 11 of ' + TOTAL_INTRO_STEPS,
              nextBtnText: 'Finish',
              doneBtnText: 'Finish',

              onNextClick: function () {
                navigatingAway = true;

                persistProgressFromDriver();

                storageSet(POST_VEHICLE_FLEET_KEY, '1');

                storageSet(STEP_KEY, '11');

                storageRemove(PENDING_KEY);

                if (
                  driverInstance &&
                  typeof driverInstance.destroy === 'function'
                ) {
                  driverInstance.destroy();
                }

                setTimeout(function () {
                  window.location.href = appendQuery(
                    vehiclesHref,
                    'limo_intro=fleet_overview'
                  );
                }, 0);
              },
            },
          },
        ],
      };
    }

    // =====================================================
    // VEHICLES LIST — post form (fleet stats + table)
    // =====================================================

    if (shouldShowFleetOverviewTour(flags)) {
      var fleetStats = document.getElementById(
        'intro-fleet-stats'
      );
      var fleetTable = document.getElementById(
        'intro-fleet-table'
      );

      var fleetSteps = [];

      if (fleetStats) {
        fleetSteps.push({
          element: '#intro-fleet-stats',

          popover: {
            title: 'Fleet overview',

            description:
              'See totals for vehicles, capacity, and fleet status at a glance.',

            showProgress: true,
          },
        });
      }

      if (fleetTable) {
        fleetSteps.push({
          element: '#intro-fleet-table',

          popover: {
            title: 'Vehicle list',

            description:
              'Review and manage saved vehicles, rates, and status from this table.',

            showProgress: true,
          },
        });
      }

      if (fleetSteps.length === 0) {
        storageRemove(POST_VEHICLE_FLEET_KEY);
      } else {
        stepMeta = {
          totalHumanSteps: TOTAL_INTRO_STEPS,
          globalOffset: 11,
          isVehiclesTail: false,
        };

        fleetSteps.forEach(function (s, i) {
          s.popover = s.popover || {};

          s.popover.showProgress = true;

          s.popover.progressText =
            'Step ' +
            (12 + i) +
            ' of ' +
            TOTAL_INTRO_STEPS;

          if (i === 0) {
            s.popover.disableButtons = ['previous'];
          }

          if (i === fleetSteps.length - 1) {
            s.popover.nextBtnText = 'Continue';

            s.popover.doneBtnText = 'Continue';

            s.popover.onNextClick = function () {
              navigatingAway = true;

              persistProgressFromDriver();

              storageRemove(POST_VEHICLE_FLEET_KEY);

              storageSet(EMAIL_INTRO_PENDING_KEY, '1');

              storageSet(STEP_KEY, '13');

              if (
                driverInstance &&
                typeof driverInstance.destroy === 'function'
              ) {
                driverInstance.destroy();
              }

              setTimeout(function () {
                window.location.href = appendQuery(
                  emailTemplatesHref,
                  'limo_intro=email_templates'
                );
              }, 0);
            };
          }
        });

        return { steps: fleetSteps };
      }
    }

    // =====================================================
    // EMAIL TEMPLATES (after fleet)
    // =====================================================

    if (shouldShowEmailTemplatesTour(flags)) {
      var etStats = document.getElementById('et-stats');
      var etTable = document.getElementById('intro-email-table');
      var etAdd = document.getElementById(
        'add-email-template-btn'
      );

      var emailSteps = [];

      if (etStats) {
        emailSteps.push({
          element: '#et-stats',

          popover: {
            title: 'Template stats',

            description:
              'See how many templates you have by type at a glance.',

            showProgress: true,
          },
        });
      }

      if (etTable) {
        emailSteps.push({
          element: '#intro-email-table',

          popover: {
            title: 'Your templates',

            description:
              'Search, filter, preview, edit, or delete templates from this list.',

            showProgress: true,
          },
        });
      }

      if (etAdd) {
        emailSteps.push({
          element: '#add-email-template-btn',

          popover: {
            title: 'New template',

            description:
              'Create a new email template for campaigns and customer messages.',

            showProgress: true,
          },
        });
      }

      if (emailSteps.length === 0) {
        storageRemove(EMAIL_INTRO_PENDING_KEY);
        markFullyDone();
      } else {
        stepMeta = {
          totalHumanSteps: TOTAL_INTRO_STEPS,
          globalOffset: 13,
          isVehiclesTail: false,
        };

        emailSteps.forEach(function (s, i) {
          s.popover = s.popover || {};

          s.popover.showProgress = true;

          s.popover.progressText =
            'Step ' +
            (14 + i) +
            ' of ' +
            TOTAL_INTRO_STEPS;

          if (i === 0) {
            s.popover.disableButtons = ['previous'];
          }

          if (i === emailSteps.length - 1) {
            s.popover.nextBtnText = 'Continue';

            s.popover.doneBtnText = 'Continue';

            s.popover.onNextClick = function () {
              navigatingAway = true;

              persistProgressFromDriver();

              storageRemove(EMAIL_INTRO_PENDING_KEY);

              storageSet(
                INTEGRATION_INTRO_PENDING_KEY,
                '1'
              );

              storageSet(STEP_KEY, '16');

              if (
                driverInstance &&
                typeof driverInstance.destroy === 'function'
              ) {
                driverInstance.destroy();
              }

              setTimeout(function () {
                window.location.href = appendQuery(
                  integrationHref,
                  'limo_intro=integration'
                );
              }, 0);
            };
          }
        });

        return { steps: emailSteps };
      }
    }

    // =====================================================
    // INTEGRATION (after email templates)
    // =====================================================

    if (shouldShowIntegrationTour(flags)) {
      var igPreview = document.getElementById(
        'intro-widget-preview'
      );
      var igCode = document.getElementById('intro-widget-code');
      var igDomains = document.getElementById(
        'intro-widget-domains'
      );

      var integrationSteps = [];

      if (igPreview) {
        integrationSteps.push({
          element: '#intro-widget-preview',

          popover: {
            title: 'Widget preview',

            description:
              'See how your booking widget will look before you embed it.',

            showProgress: true,
          },
        });
      }

      if (igCode) {
        integrationSteps.push({
          element: '#intro-widget-code',

          popover: {
            title: 'Embed code',

            description:
              'Copy this snippet into your website to add the widget.',

            showProgress: true,
          },
        });
      }

      if (igDomains) {
        integrationSteps.push({
          element: '#intro-widget-domains',

          popover: {
            title: 'Allowed domains',

            description:
              'Restrict where the widget can load for security.',

            showProgress: true,
          },
        });
      }

      if (integrationSteps.length === 0) {
        storageRemove(INTEGRATION_INTRO_PENDING_KEY);
        markFullyDone();
      } else {
        stepMeta = {
          totalHumanSteps: TOTAL_INTRO_STEPS,
          globalOffset: 16,
          isVehiclesTail: false,
        };

        integrationSteps.forEach(function (s, i) {
          s.popover = s.popover || {};

          s.popover.showProgress = true;

          s.popover.progressText =
            'Step ' +
            (17 + i) +
            ' of ' +
            TOTAL_INTRO_STEPS;

          if (i === 0) {
            s.popover.disableButtons = ['previous'];
          }

          if (i === integrationSteps.length - 1) {
            s.popover.nextBtnText = 'Continue';

            s.popover.doneBtnText = 'Continue';

            s.popover.onNextClick = function () {
              navigatingAway = true;

              persistProgressFromDriver();

              storageRemove(INTEGRATION_INTRO_PENDING_KEY);

              storageSet(
                LEADS_INTRO_PENDING_KEY,
                '1'
              );

              storageSet(STEP_KEY, '19');

              if (
                driverInstance &&
                typeof driverInstance.destroy === 'function'
              ) {
                driverInstance.destroy();
              }

              setTimeout(function () {
                window.location.href = appendQuery(
                  leadsHref,
                  'limo_intro=leads'
                );
              }, 0);
            };
          }
        });

        return { steps: integrationSteps };
      }
    }

    // =====================================================
    // LEADS LIST (after integration)
    // =====================================================

    if (shouldShowLeadsListTour(flags)) {
      var lsStats = document.getElementById(
        'intro-leads-stats'
      );
      var lsTable = document.getElementById(
        'intro-leads-table'
      );
      var lsAddWrap = document.getElementById('add-lead-btn');

      var leadsSteps = [];

      if (lsStats) {
        leadsSteps.push({
          element: '#intro-leads-stats',

          popover: {
            title: 'Lead stats',

            description:
              'Monitor totals, pipeline mix, and conversion at a glance.',

            showProgress: true,
          },
        });
      }

      if (lsTable) {
        leadsSteps.push({
          element: '#intro-leads-table',

          popover: {
            title: 'Leads table',

            description:
              'Search, filter by status and event date, and open any lead.',

            showProgress: true,
          },
        });
      }

      if (lsAddWrap) {
        leadsSteps.push({
          element: '#add-lead-btn',

          popover: {
            title: 'New lead',

            description:
              'When you are ready, open the form to capture a new lead.',

            showProgress: true,
          },
        });
      }

      if (leadsSteps.length === 0) {
        storageRemove(LEADS_INTRO_PENDING_KEY);

        markFullyDone();
      } else {
        stepMeta = {
          totalHumanSteps: TOTAL_INTRO_STEPS,
          globalOffset: 19,
          isVehiclesTail: false,
        };

        leadsSteps.forEach(function (s, i) {
          s.popover = s.popover || {};

          s.popover.showProgress = true;

          s.popover.progressText =
            'Step ' +
            (20 + i) +
            ' of ' +
            TOTAL_INTRO_STEPS;

          if (i === 0) {
            s.popover.disableButtons = ['previous'];
          }

          if (i === leadsSteps.length - 1) {
            s.popover.nextBtnText = 'Continue';

            s.popover.doneBtnText = 'Continue';

            s.popover.onNextClick = function () {
              navigatingAway = true;

              persistProgressFromDriver();

              storageRemove(LEADS_INTRO_PENDING_KEY);

              storageSet(
                ADD_LEAD_INTRO_PENDING_KEY,
                '1'
              );

              storageSet(STEP_KEY, '22');

              if (
                driverInstance &&
                typeof driverInstance.destroy === 'function'
              ) {
                driverInstance.destroy();
              }

              setTimeout(function () {
                window.location.href = appendQuery(
                  addLeadHref,
                  'limo_intro=add_lead'
                );
              }, 0);
            };
          }
        });

        return { steps: leadsSteps };
      }
    }

    // =====================================================
    // ADD LEAD FORM
    // =====================================================

    if (shouldShowAddLeadTour(flags)) {
      var alInfo = document.getElementById(
        'intro-lead-information'
      );
      var alTrip = document.getElementById(
        'intro-lead-trip-details'
      );
      var alVeh = document.getElementById(
        'intro-lead-select-vehicle'
      );
      var alSum = document.getElementById(
        'intro-lead-live-summary'
      );
      var alSave = document.getElementById('al-save-btn');

      var addLeadSteps = [];

      if (alInfo) {
        addLeadSteps.push({
          element: '#intro-lead-information',

          popover: {
            title: 'Lead information',

            description:
              'Enter client and contact details for the new lead.',

            showProgress: true,
          },
        });
      }

      if (alTrip) {
        addLeadSteps.push({
          element: '#intro-lead-trip-details',

          popover: {
            title: 'Trip details',

            description:
              'Set pickup, drop-off, timing, and trip requirements.',

            showProgress: true,
          },
        });
      }

      if (alVeh) {
        addLeadSteps.push({
          element: '#intro-lead-select-vehicle',

          popover: {
            title: 'Vehicle',

            description:
              'Choose a vehicle class or specific vehicle for the quote.',

            showProgress: true,
          },
        });
      }

      if (alSum) {
        addLeadSteps.push({
          element: '#intro-lead-live-summary',

          popover: {
            title: 'Live summary',

            description:
              'Review the key fields before saving.',

            showProgress: true,
          },
        });
      }

      if (alSave) {
        addLeadSteps.push({
          element: '#al-save-btn',

          popover: {
            title: 'Save lead',

            description:
              'Save when the form is complete. After the tour you will return to Lead Management.',

            showProgress: true,
          },
        });
      }

      if (addLeadSteps.length === 0) {
        storageRemove(ADD_LEAD_INTRO_PENDING_KEY);

        markFullyDone();
      } else {
        stepMeta = {
          totalHumanSteps: TOTAL_INTRO_STEPS,
          globalOffset: 22,
          isVehiclesTail: false,
        };

        addLeadSteps.forEach(function (s, i) {
          s.popover = s.popover || {};

          s.popover.showProgress = true;

          s.popover.progressText =
            'Step ' +
            (23 + i) +
            ' of ' +
            TOTAL_INTRO_STEPS;

          if (i === 0) {
            s.popover.disableButtons = ['previous'];
          }

          if (i === addLeadSteps.length - 1) {
            s.popover.nextBtnText = 'Finish';

            s.popover.doneBtnText = 'Finish';

            s.popover.onNextClick = function () {
              navigatingAway = true;

              persistProgressFromDriver();

              markFullyDone();

              if (
                driverInstance &&
                typeof driverInstance.destroy === 'function'
              ) {
                driverInstance.destroy();
              }

              setTimeout(function () {
                window.location.href = leadsHref;
              }, 0);
            };
          }
        });

        return { steps: addLeadSteps };
      }
    }

    // =====================================================
    // VEHICLES LIST PAGE
    // =====================================================

    var addBtn = document.getElementById(
      'add-vehicle-btn'
    );

    var pending = storageGet(PENDING_KEY) === '1';

    var vehiclesTail = !!(
      addBtn &&
      (flags.limoIntro || pending)
    );

    if (vehiclesTail) {
      stepMeta = {
        totalHumanSteps: TOTAL_INTRO_STEPS,
        globalOffset: 4,
        isVehiclesTail: true,
      };

      return {
        steps: [
          {
            element: '#add-vehicle-btn',

            popover: {
              title: 'Add Vehicle',

              description:
                'Click here to create a new vehicle.',

              showProgress: true,

              progressText: 'Step 5 of ' + TOTAL_INTRO_STEPS,

              disableButtons: ['previous'],

              nextBtnText: 'Open Vehicle Form',

              onNextClick: function () {
                var addEl = document.getElementById(
                  'add-vehicle-btn'
                );

                var href =
                  addEl && addEl.getAttribute
                    ? addEl.getAttribute('href')
                    : null;

                if (!href) {
                  href = 'vehicle.php';
                }

                navigatingAway = true;

                persistProgressFromDriver();

                storageSet(PENDING_KEY, '1');

                if (
                  driverInstance &&
                  typeof driverInstance.destroy === 'function'
                ) {
                  driverInstance.destroy();
                }

                setTimeout(function () {
                  window.location.href = appendQuery(
                    href,
                    'limo_intro=vehicle_form'
                  );
                }, 0);
              },
            },
          },
        ],
      };
    }

    // =====================================================
    // DASHBOARD PAGE
    // =====================================================

    if (!isDashboardPage()) {
      stepMeta = {
        totalHumanSteps: 0,
        globalOffset: 0,
        isVehiclesTail: false,
      };

      return { steps: [] };
    }

    var list = [];

    var selStats = document.getElementById(
      'intro-wizard-stats'
    );

    var selSales = document.getElementById(
      'intro-wizard-sales'
    );

    var selLeads = document.getElementById(
      'intro-wizard-leads-report'
    );

    var selVehicles = document.getElementById(
      'intro-nav-vehicles'
    );

    if (selStats) {
      list.push({
        element: '#intro-wizard-stats',

        popover: {
          title: 'Statistics',

          description:
            'View lead totals, wins, revenue, and conversions.',
        },
      });
    }

    if (selSales) {
      list.push({
        element: '#intro-wizard-sales',

        popover: {
          title: 'Sales Overview',

          description:
            'Monthly sales and conversion analytics.',
        },
      });
    }

    if (selLeads) {
      list.push({
        element: '#intro-wizard-leads-report',

        popover: {
          title: 'Leads Report',

          description:
            'Search, filter, and manage leads.',
        },
      });
    }

    if (selVehicles) {
      list.push({
        element: '#intro-nav-vehicles',

        popover: {
          title: 'Vehicles',

          description:
            'Manage fleet vehicles and pricing.',

          nextBtnText: 'Open Vehicles',

          onNextClick: function () {
            var ix =
              driverInstance &&
              typeof driverInstance.getActiveIndex ===
                'function'
                ? driverInstance.getActiveIndex()
                : 0;

            storageSet(
              STEP_KEY,
              String(
                currentGlobalStepIndex(ix) + 1
              )
            );

            storageSet(PENDING_KEY, '1');

            navigatingAway = true;

            if (
              driverInstance &&
              typeof driverInstance.destroy ===
                'function'
            ) {
              driverInstance.destroy();
            }

            setTimeout(function () {
              window.location.href = appendQuery(
                vehiclesHref,
                'limo_intro=1'
              );
            }, 0);
          },
        },
      });
    }

    stepMeta = {
      totalHumanSteps: TOTAL_INTRO_STEPS,
      globalOffset: 0,
      isVehiclesTail: false,
    };

    list.forEach(function (s, i) {
      s.popover = s.popover || {};

      s.popover.showProgress = true;

      s.popover.progressText =
        'Step ' + (i + 1) + ' of ' + TOTAL_INTRO_STEPS;

      if (i === 0) {
        s.popover.disableButtons = ['previous'];
      }
    });

    return { steps: list };
  }

  // =========================================================
  // Resolve Start
  // =========================================================

  function resolveStartIndex(cfg) {
    var saved = readSavedGlobalStep();

    var maxLocal = cfg.steps.length - 1;

    var targetGlobal = Math.min(
      saved,
      stepMeta.globalOffset + maxLocal
    );

    return Math.min(
      Math.max(
        0,
        targetGlobal - stepMeta.globalOffset
      ),
      maxLocal
    );
  }

  // =========================================================
  // Start
  // =========================================================

  function endIntroTourDismissed() {
    navigatingAway = true;

    persistProgressFromDriver();

    markDismissed();

    if (
      driverInstance &&
      typeof driverInstance.destroy === 'function'
    ) {
      driverInstance.destroy();
    }
  }

  var END_TOUR_FAB_ID = 'limo-intro-end-tour-fab';

  function removeEndTourFloatingBtn() {
    var el = document.getElementById(END_TOUR_FAB_ID);

    if (el && el.parentNode) {
      el.parentNode.removeChild(el);
    }
  }

  function showEndTourFloatingBtn() {
    removeEndTourFloatingBtn();

    var btn = document.createElement('button');

    btn.id = END_TOUR_FAB_ID;
    btn.type = 'button';
    btn.className = 'limo-intro-end-tour-fab';
    btn.textContent = 'End tour';
    btn.setAttribute('aria-label', 'End tour');

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      endIntroTourDismissed();
    });

    document.body.appendChild(btn);
  }

  function start() {
    var flags = getFlags();

    if (flags.introRestart) {
      clearAllIntroKeys();
    }

    var cfg = buildTourConfig();

    if (!cfg.steps || cfg.steps.length === 0) {
      removeEndTourFloatingBtn();

      return;
    }

    if (
      driverInstance &&
      typeof driverInstance.destroy === 'function'
    ) {
      ignoreDriverEvents = true;

      navigatingAway = true;

      driverInstance.destroy();

      driverInstance = null;

      navigatingAway = false;

      ignoreDriverEvents = false;
    }

    var startIndex = resolveStartIndex(cfg);

    navigatingAway = false;

    clearLeadsReflow();

    driverInstance = createDriver({
      showProgress: false,

      smoothScroll: true,

      animate: true,

      allowClose: true,

      overlayOpacity: 0.72,

      overlayColor: '#0f172a',

      stageRadius: 14,

      stagePadding: 10,

      popoverClass: 'limo-driver-popover',

      nextBtnText: 'Next',

      prevBtnText: 'Back',

      doneBtnText: 'Done',

      steps: cfg.steps,

      onHighlighted: function (
        element,
        step,
        o
      ) {
        persistProgressFromDriver(o.driver);

        if (
          element &&
          element.id ===
            'intro-wizard-leads-report'
        ) {
          attachLeadsReflowIfNeeded();
        } else {
          clearLeadsReflow();
        }
      },

      onCloseClick: function () {
        endIntroTourDismissed();
      },

      onDestroyStarted: function (
        element,
        step,
        o
      ) {
        if (
          ignoreDriverEvents ||
          navigatingAway
        ) {
          return;
        }

        persistProgressFromDriver(
          o && o.driver
        );
      },

      onDestroyed: function () {
        removeEndTourFloatingBtn();

        clearLeadsReflow();

        driverInstance = null;

        if (ignoreDriverEvents) {
          return;
        }

        var nav = navigatingAway;

        navigatingAway = false;

        if (nav) {
          return;
        }

        markDismissed();
      },
    });

    var launchFlags = getFlags();

    if (
      cfg.steps &&
      cfg.steps.length &&
      isVehicleFormPage() &&
      launchFlags.vehicleForm &&
      !launchFlags.forceVehicleIntro
    ) {
      storageSet(VEHICLE_FORM_LAUNCH_KEY, '1');
      stripVehicleFormQueryParam();
    }

    if (
      cfg.steps &&
      cfg.steps.length &&
      isVehiclesListPage() &&
      !isVehicleFormPage() &&
      launchFlags.fleetOverview
    ) {
      stripFleetOverviewParam();
    }

    if (
      cfg.steps &&
      cfg.steps.length &&
      isEmailTemplatesPage() &&
      launchFlags.emailTemplates &&
      !launchFlags.forceEmailIntro
    ) {
      stripEmailTemplatesParam();
    }

    if (
      cfg.steps &&
      cfg.steps.length &&
      isIntegrationPage() &&
      launchFlags.integrationPage &&
      !launchFlags.forceIntegrationIntro
    ) {
      stripIntegrationParam();
    }

    if (
      cfg.steps &&
      cfg.steps.length &&
      isLeadsListPage() &&
      launchFlags.leadsIntro &&
      !launchFlags.forceLeadsIntro
    ) {
      stripLeadsIntroParam();
    }

    if (
      cfg.steps &&
      cfg.steps.length &&
      isAddLeadPage() &&
      launchFlags.addLeadIntro &&
      !launchFlags.forceAddLeadIntro
    ) {
      stripAddLeadIntroParam();
    }

    driverInstance.drive(startIndex);

    showEndTourFloatingBtn();
  }

  // =========================================================
  // Header Button
  // =========================================================

  function bindHeaderIntro() {
    var btn = document.getElementById(
      'limo-header-intro-btn'
    );

    if (!btn) {
      return;
    }

    btn.addEventListener('click', function () {
      var fullDone =
        storageGet(FULL_DONE_KEY) === '1';

      var pending =
        storageGet(PENDING_KEY) === '1';

      var saved = readSavedGlobalStep();

      if (fullDone) {
        window.location.href = appendQuery(
          indexHref,
          'intro=restart'
        );

        return;
      }

      if (
        storageGet(ADD_LEAD_INTRO_PENDING_KEY) === '1' &&
        saved >= 22 &&
        saved <= 26
      ) {
        window.location.href = appendQuery(
          addLeadHref,
          'limo_intro=add_lead'
        );

        return;
      }

      if (
        storageGet(LEADS_INTRO_PENDING_KEY) === '1' &&
        saved >= 19 &&
        saved <= 21
      ) {
        window.location.href = appendQuery(
          leadsHref,
          'limo_intro=leads'
        );

        return;
      }

      if (
        storageGet(INTEGRATION_INTRO_PENDING_KEY) === '1' &&
        saved >= 16 &&
        saved <= 18
      ) {
        window.location.href = appendQuery(
          integrationHref,
          'limo_intro=integration'
        );

        return;
      }

      if (
        storageGet(EMAIL_INTRO_PENDING_KEY) === '1' &&
        saved >= 13 &&
        saved <= 15
      ) {
        window.location.href = appendQuery(
          emailTemplatesHref,
          'limo_intro=email_templates'
        );

        return;
      }

      if (
        storageGet(POST_VEHICLE_FLEET_KEY) === '1' &&
        saved >= 11 &&
        saved <= 12
      ) {
        window.location.href = appendQuery(
          vehiclesHref,
          'limo_intro=fleet_overview'
        );

        return;
      }

      if (saved >= 5 && saved <= 10) {
        window.location.href = './vehicle.php?force_vehicle_intro=1';

        return;
      }

      if (pending || saved >= 4) {
        window.location.href = appendQuery(
          vehiclesHref,
          'limo_intro=resume'
        );

        return;
      }

      window.location.href = appendQuery(
        indexHref,
        'intro=resume'
      );
    });
  }

  // =========================================================
  // Expose
  // =========================================================

  window.LimoIntroWizard = {
    start: start,
    clearProgress: clearAllIntroKeys,
  };

  bindHeaderIntro();

  // =========================================================
  // Auto Start
  // =========================================================

  window.addEventListener('load', function () {
    setTimeout(function () {
      if (!shouldAutoRunIntro()) {
        return;
      }
      start();
    }, 800);
  });
})();