/**
 * Add/Edit vehicle form tour (vehicle.php). Separate storage from dashboard intro.
 */
(function () {
  'use strict';

  var cfg = window.LIMO_INTRO_VEHICLE;
  if (!cfg || !cfg.userId) {
    return;
  }

  var userId = String(cfg.userId);
  var DONE_KEY = 'limocrm_intro_vehicle_v1_' + userId;
  var STEP_KEY = 'limocrm_intro_vehicle_step_v1_' + userId;

  var steps = [];
  var idx = 0;
  var root,
    hole,
    panel,
    btnPrimary,
    btnSkip,
    stepLabel;
  var started = false;

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

  function getUrlFlags() {
    var p = new URLSearchParams(window.location.search || '');
    var v = (p.get('vehicle_intro') || '').toString().toLowerCase().trim();
    var replay =
      v === 'restart' ||
      v === 'reset' ||
      v === 'again' ||
      v === '1' ||
      v === 'true' ||
      v === 'yes';
    return {
      resume: v === 'resume',
      replay: replay,
    };
  }

  function buildSteps() {
    var defs = [
      {
        id: 'intro-vehicle-basic',
        title: 'Basic Information',
        body: 'Start here: vehicle name, category, status, passenger capacity, and luggage capacity. Required fields are marked with an asterisk.',
      },
      {
        id: 'intro-vehicle-image',
        title: 'Vehicle Image',
        body: 'Upload a clear photo (JPG, PNG, or WEBP, max 2MB). You can click the upload area or Choose Image. A good photo improves trust and bookings.',
      },
      {
        id: 'intro-vehicle-pricing',
        title: 'Pricing',
        body: 'Set hourly rate, fuel surcharge (% of trip), and driver commission. These values feed quotes and driver payouts.',
      },
      {
        id: 'intro-vehicle-details',
        title: 'Details & Features',
        body: 'List amenities separated by commas, then add a helpful description for customers (minimum length is validated on save).',
      },
      {
        id: 'intro-vehicle-live-preview',
        title: 'Live Preview',
        body: 'This panel updates as you type so you can confirm name, category, status, capacity, and pricing before you save.',
      },
      {
        id: 'intro-vehicle-save',
        title: 'Save Vehicle',
        body: 'When required fields and the image are complete, use Save Vehicle to submit. The form scrolls to and marks anything still missing.',
      },
    ];
    var list = [];
    defs.forEach(function (d) {
      var el = document.getElementById(d.id);
      if (el) {
        list.push({ el: el, title: d.title, body: d.body });
      }
    });
    return list;
  }

  function destroy() {
    window.removeEventListener('resize', onResize);
    window.removeEventListener('keydown', onKey);
    if (root && root.parentNode) {
      root.parentNode.removeChild(root);
    }
    root = null;
    hole = null;
    panel = null;
    btnPrimary = null;
    btnSkip = null;
    stepLabel = null;
    document.body.style.overflow = '';
    started = false;
  }

  function markDone() {
    storageSet(DONE_KEY, '1');
    destroy();
  }

  function persistStep() {
    storageSet(STEP_KEY, String(idx));
  }

  function onResize() {
    if (hole && idx >= 0 && idx < steps.length) {
      positionHole(steps[idx].el);
    }
  }

  function onKey(ev) {
    if (ev.key === 'Escape') {
      ev.preventDefault();
      markDone();
    }
  }

  function positionHole(el) {
    if (!el || !hole) return;
    var pad = 10;
    var br = el.getBoundingClientRect();
    var top = Math.max(8, br.top - pad);
    var left = Math.max(8, br.left - pad);
    var w = br.width + pad * 2;
    var h = br.height + pad * 2;
    hole.style.top = top + 'px';
    hole.style.left = left + 'px';
    hole.style.width = w + 'px';
    hole.style.height = h + 'px';
    hole.style.borderRadius = '14px';
    hole.style.boxShadow = '0 0 0 9999px rgba(15, 23, 42, 0.72)';
    hole.style.display = 'block';
  }

  function renderStep() {
    var s = steps[idx];
    if (!s) {
      markDone();
      return;
    }
    persistStep();
    s.el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    positionHole(s.el);
    if (
      s.el.id === 'intro-vehicle-image' ||
      s.el.id === 'intro-vehicle-live-preview' ||
      s.el.id === 'intro-vehicle-save'
    ) {
      setTimeout(function () {
        if (idx < steps.length && steps[idx] && steps[idx].el === s.el) {
          positionHole(s.el);
        }
      }, 400);
    }

    var total = steps.length;
    stepLabel.textContent = 'Step ' + (idx + 1) + ' of ' + total;
    panel.querySelector('.limo-intro-vehicle-title').textContent = s.title;
    panel.querySelector('.limo-intro-vehicle-body').textContent = s.body;

    btnPrimary.textContent = idx === steps.length - 1 ? 'Done' : 'Next';
  }

  function nextOrFinish() {
    if (idx >= steps.length - 1) {
      storageSet(DONE_KEY, '1');
      storageSet(STEP_KEY, String(Math.max(0, steps.length - 1)));
      destroy();
      return;
    }
    idx += 1;
    renderStep();
  }

  function buildUi() {
    root = document.createElement('div');
    root.className = 'limo-intro-root limo-intro-vehicle-root';
    root.setAttribute('role', 'dialog');
    root.setAttribute('aria-modal', 'true');
    root.setAttribute('aria-labelledby', 'limo-intro-vehicle-title');

    hole = document.createElement('div');
    hole.className = 'limo-intro-hole';
    hole.style.position = 'fixed';
    hole.style.zIndex = '10051';
    hole.style.pointerEvents = 'none';
    hole.style.transition = 'top 0.25s ease, left 0.25s ease, width 0.25s ease, height 0.25s ease';

    panel = document.createElement('div');
    panel.className = 'limo-intro-panel';
    panel.style.cssText =
      'position:fixed;z-index:10052;left:50%;bottom:28px;transform:translateX(-50%);' +
      'max-width:min(440px,calc(100vw - 32px));width:100%;padding:20px 22px;border-radius:16px;' +
      'background:rgba(255,255,255,0.98);border:1px solid rgba(15,23,42,0.1);' +
      'box-shadow:0 20px 50px rgba(15,23,42,0.18);font-family:inherit;';

    if (document.documentElement.classList.contains('dark')) {
      panel.style.background = 'rgba(22,28,36,0.96)';
      panel.style.borderColor = 'rgba(255,255,255,0.1)';
      panel.style.boxShadow = '0 20px 50px rgba(0,0,0,0.45)';
    }

    stepLabel = document.createElement('div');
    stepLabel.style.cssText =
      'font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#cf1c82;margin-bottom:8px;';
    panel.appendChild(stepLabel);

    var h = document.createElement('h3');
    h.className = 'limo-intro-vehicle-title';
    h.id = 'limo-intro-vehicle-title';
    h.style.cssText = 'margin:0 0 10px;font-size:1.15rem;font-weight:700;color:inherit;line-height:1.25;';
    panel.appendChild(h);

    var p = document.createElement('p');
    p.className = 'limo-intro-vehicle-body';
    p.style.cssText = 'margin:0 0 18px;font-size:0.9rem;line-height:1.55;color:rgba(15,23,42,0.72);';
    if (document.documentElement.classList.contains('dark')) {
      p.style.color = 'rgba(255,255,255,0.75)';
    }
    panel.appendChild(p);

    var row = document.createElement('div');
    row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;';

    btnSkip = document.createElement('button');
    btnSkip.type = 'button';
    btnSkip.textContent = 'Skip tour';
    btnSkip.style.cssText =
      'border:none;background:transparent;color:rgba(15,23,42,0.55);cursor:pointer;font-size:0.85rem;font-weight:600;padding:8px 4px;';
    if (document.documentElement.classList.contains('dark')) {
      btnSkip.style.color = 'rgba(255,255,255,0.5)';
    }
    btnSkip.addEventListener('click', markDone);

    btnPrimary = document.createElement('button');
    btnPrimary.type = 'button';
    btnPrimary.style.cssText =
      'margin-left:auto;border:none;border-radius:12px;padding:10px 20px;font-weight:600;font-size:0.9rem;cursor:pointer;' +
      'background:#cf1c82;color:#fff;box-shadow:0 4px 14px rgba(207,28,130,0.35);';
    btnPrimary.addEventListener('click', nextOrFinish);

    row.appendChild(btnSkip);
    row.appendChild(btnPrimary);
    panel.appendChild(row);

    root.appendChild(hole);
    root.appendChild(panel);
    document.body.appendChild(root);
    document.body.style.overflow = 'hidden';

    window.addEventListener('resize', onResize);
    window.addEventListener('keydown', onKey);
  }

  function readSavedIndex() {
    var raw = storageGet(STEP_KEY);
    if (raw === null || raw === '') {
      return 0;
    }
    var n = parseInt(raw, 10);
    return isNaN(n) ? 0 : n;
  }

  function start(opts) {
    opts = opts || {};
    steps = buildSteps();
    if (steps.length === 0) {
      return;
    }

    var flags = getUrlFlags();
    if (opts.restart || opts.replay || flags.replay) {
      storageRemove(DONE_KEY);
      storageRemove(STEP_KEY);
      idx = 0;
    } else {
      idx = Math.min(Math.max(0, readSavedIndex()), steps.length - 1);
    }

    if (started && root) {
      destroy();
    }
    started = true;

    buildUi();
    renderStep();
    setTimeout(onResize, 400);
    btnPrimary.focus();
  }

  function shouldAutoOpen() {
    var flags = getUrlFlags();
    if (flags.replay) {
      return true;
    }
    if (flags.resume && storageGet(DONE_KEY) === '1') {
      return false;
    }
    if (flags.resume) {
      return true;
    }
    if (storageGet(DONE_KEY) === '1') {
      return false;
    }
    return !!cfg.autoStart;
  }

  window.LimoIntroVehicle = {
    start: function (o) {
      start(o || {});
    },
    clearProgress: function () {
      storageRemove(DONE_KEY);
      storageRemove(STEP_KEY);
    },
  };

  function scheduleAutoKickOnce() {
    if (!shouldAutoOpen()) {
      return;
    }
    setTimeout(function () {
      start({});
    }, 800);
  }

  if (document.readyState === 'complete') {
    scheduleAutoKickOnce();
  } else {
    window.addEventListener('load', scheduleAutoKickOnce);
  }
})();
