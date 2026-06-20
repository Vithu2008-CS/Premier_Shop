/**
 * CSP delegation shim — replaces inline event handlers (onclick=, onsubmit=,
 * onerror=, …) which are blocked under the nonce-based Content-Security-Policy.
 *
 * Supported declarative attributes:
 *   data-call="fnName"          Invoke window.fnName on click (or data-on event).
 *                               Dotted paths allowed (e.g. "location.reload").
 *   data-args='[1,"up"]'        JSON args for data-call. "$el" → the element,
 *                               "$event" → the DOM event. `this` inside the fn
 *                               is the element.
 *   data-on="change|input|…"    Event for data-call (default: click).
 *   data-prevent                preventDefault on click.
 *   data-confirm="msg"          Confirm dialog before form submit, or before
 *                               other click behaviour on links/buttons.
 *   data-submit-form="id"       preventDefault + submit form #id on click.
 *   data-trigger-click="id"     Forward click to element #id.
 *   data-href="/url"            Navigate on click.
 *   data-stop                   stopPropagation on click.
 *   data-autosubmit             Submit closest form on change.
 *   data-fallback-src="/img"    Swap img src once on load error.
 *   data-hide-on-error          Hide element on load error.
 *   data-error-html-target=".x" On load error, replace closest(".x")'s
 *   data-error-html="<div/>"    innerHTML with the given markup.
 *   data-media-onload="all"     Set link media attr once stylesheet loads.
 */
(function () {
    'use strict';

    function resolveArgs(el, event, raw) {
        if (!raw) return [];
        var args;
        try { args = JSON.parse(raw); } catch (e) { return []; }
        return args.map(function (a) {
            if (a === '$el') return el;
            if (a === '$event') return event;
            return a;
        });
    }

    function invoke(el, event) {
        var path = (el.getAttribute('data-call') || '').split('.');
        var ctx = window;
        var fn = window;
        for (var i = 0; i < path.length; i++) {
            ctx = fn;
            fn = fn ? fn[path[i]] : undefined;
        }
        if (typeof fn === 'function') {
            var args = resolveArgs(el, event, el.getAttribute('data-args'));
            // Dotted paths (window.print, location.reload) need their own
            // object as `this`; plain page functions get the element, matching
            // inline-handler semantics.
            fn.apply(path.length > 1 ? ctx : el, args);
        }
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-stop]')) event.stopPropagation();

        // Confirm gate (forms are handled in the submit listener instead, so a
        // submit button inside a form[data-confirm] doesn't double-prompt).
        var confirmEl = event.target.closest('[data-confirm]');
        if (confirmEl && confirmEl.tagName !== 'FORM'
            && !window.confirm(confirmEl.getAttribute('data-confirm'))) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        if (event.target.closest('[data-prevent]')) event.preventDefault();

        var submitEl = event.target.closest('[data-submit-form]');
        if (submitEl) {
            event.preventDefault();
            var form = document.getElementById(submitEl.getAttribute('data-submit-form'));
            if (form) form.submit();
            return;
        }

        var triggerEl = event.target.closest('[data-trigger-click]');
        if (triggerEl) {
            var target = document.getElementById(triggerEl.getAttribute('data-trigger-click'));
            if (target) target.click();
        }

        var callEl = event.target.closest('[data-call]');
        if (callEl && (callEl.getAttribute('data-on') || 'click') === 'click') {
            invoke(callEl, event);
        }

        var hrefEl = event.target.closest('[data-href]');
        if (hrefEl) window.location = hrefEl.getAttribute('data-href');
    });

    ['change', 'input', 'keyup', 'keydown'].forEach(function (type) {
        document.addEventListener(type, function (event) {
            var el = event.target.closest('[data-call][data-on="' + type + '"]');
            if (el) invoke(el, event);

            if (type === 'change') {
                var auto = event.target.closest('[data-autosubmit]');
                if (auto && auto.form) auto.form.submit();
            }
        });
    });

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('form[data-confirm]');
        if (form && !window.confirm(form.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });

    // error/load do not bubble — use capture phase.
    document.addEventListener('error', function (event) {
        var el = event.target;
        if (!(el instanceof Element)) return;

        var fallback = el.getAttribute('data-fallback-src');
        if (fallback && el.src !== fallback) {
            el.removeAttribute('data-fallback-src');
            el.src = fallback;
            return;
        }
        var htmlTarget = el.getAttribute('data-error-html-target');
        if (htmlTarget) {
            var wrap = el.closest(htmlTarget);
            if (wrap) wrap.innerHTML = el.getAttribute('data-error-html') || '';
            return;
        }
        if (el.hasAttribute('data-hide-on-error')) {
            el.style.display = 'none';
        }
    }, true);

    function applyMediaOnload(el) {
        if (el instanceof Element && el.tagName === 'LINK' && el.hasAttribute('data-media-onload')) {
            el.media = el.getAttribute('data-media-onload');
            el.removeAttribute('data-media-onload');
        }
    }

    // Swap media once a stylesheet finishes loading after this shim runs.
    document.addEventListener('load', function (event) {
        applyMediaOnload(event.target);
    }, true);

    // This shim is deferred, so browser-cached stylesheets may have already
    // fired their load event before the listener above existed — leaving their
    // media stuck at "print" forever (fonts/icons never appear). Flush any such
    // links immediately, and again on DOMContentLoaded for safety.
    function flushMediaOnload() {
        var links = document.querySelectorAll('link[data-media-onload]');
        for (var i = 0; i < links.length; i++) applyMediaOnload(links[i]);
    }
    flushMediaOnload();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', flushMediaOnload);
    }
})();
