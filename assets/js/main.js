/**
 * Estatein - front-end behaviour.
 *
 * Everything here is progressive enhancement. The markup is complete and
 * usable on its own; JavaScript only improves the experience. Nothing below
 * throws if an element is missing, so template parts can be removed freely.
 */
(function () {
	'use strict';

	/* ---------------------------------------------------------------------
	 * Mobile navigation
	 * ------------------------------------------------------------------ */

	function initNav() {
		var toggle = document.querySelector('.nav-toggle');
		var nav    = document.getElementById('site-nav');

		if (!toggle || !nav) {
			return;
		}

		function setOpen(open) {
			toggle.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
		}

		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});

		// Escape closes the menu and returns focus, so keyboard users are
		// never trapped inside it.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
				toggle.focus();
			}
		});

		// Reset the toggle state when the layout returns to desktop width.
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1100) {
				setOpen(false);
			}
		});
	}

	/* ---------------------------------------------------------------------
	 * Announcement banner
	 * ------------------------------------------------------------------ */

	function initDismiss() {
		var buttons = document.querySelectorAll('[data-dismiss]');

		Array.prototype.forEach.call(buttons, function (button) {
			var target = document.querySelector(button.getAttribute('data-dismiss'));

			if (!target) {
				return;
			}

			// Respect a previous dismissal for this browser session.
			try {
				if (sessionStorage.getItem('estatein-dismissed') === '1') {
					target.hidden = true;
				}
			} catch (err) {
				// Private browsing can throw on storage access; ignore.
			}

			button.addEventListener('click', function () {
				target.hidden = true;

				try {
					sessionStorage.setItem('estatein-dismissed', '1');
				} catch (err) {
					// Nothing to do - the banner still closes for this view.
				}
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Carousels
	 * ------------------------------------------------------------------ */

	/**
	 * How many cards are visible at the current width.
	 *
	 * Mirrors the breakpoints in main.css so the maths and the layout agree.
	 *
	 * @return {number}
	 */
	function itemsPerView() {
		if (window.innerWidth >= 1100) { return 3; }
		if (window.innerWidth >= 700)  { return 2; }
		return 1;
	}

	function initCarousel(root) {
		var track = root.querySelector('[data-carousel-track]');
		var prev  = root.querySelector('[data-carousel-prev]');
		var next  = root.querySelector('[data-carousel-next]');
		var count = root.querySelector('[data-carousel-current]');

		if (!track || !track.children.length) {
			return;
		}

		var items = Array.prototype.slice.call(track.children);
		var index = 0;

		function maxIndex() {
			return Math.max(0, items.length - itemsPerView());
		}

		function render() {
			index = Math.min(index, maxIndex());

			var first = items[0];
			var gap   = parseFloat(getComputedStyle(track).columnGap || '30') || 30;
			var step  = first.getBoundingClientRect().width + gap;

			track.style.transform = 'translateX(' + (-index * step) + 'px)';

			if (prev) { prev.disabled = index === 0; }
			if (next) { next.disabled = index >= maxIndex(); }

			if (count) {
				count.textContent = String(index + 1).padStart(2, '0');
			}

			// Cards scrolled out of view must not be reachable by Tab.
			items.forEach(function (item, i) {
				var visible = i >= index && i < index + itemsPerView();
				item.setAttribute('aria-hidden', visible ? 'false' : 'true');

				Array.prototype.forEach.call(
					item.querySelectorAll('a, button, input'),
					function (el) {
						if (visible) {
							el.removeAttribute('tabindex');
						} else {
							el.setAttribute('tabindex', '-1');
						}
					}
				);
			});
		}

		function move(delta) {
			index = Math.max(0, Math.min(maxIndex(), index + delta));
			render();
		}

		if (prev) { prev.addEventListener('click', function () { move(-1); }); }
		if (next) { next.addEventListener('click', function () { move(1); }); }

		// Arrow keys work once focus is inside the carousel.
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft')  { move(-1); }
			if (e.key === 'ArrowRight') { move(1); }
		});

		var resizeTimer;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(render, 150);
		});

		render();
	}

	/* ---------------------------------------------------------------------
	 * FAQ read more / read less
	 * ------------------------------------------------------------------ */

	function initFaq() {
		var toggles = document.querySelectorAll('[data-faq-toggle]');

		Array.prototype.forEach.call(toggles, function (button) {
			var card = button.closest('.faq-card');

			if (!card) {
				return;
			}

			var teaser = card.querySelector('[data-faq-teaser]');
			var full   = card.querySelector('[data-faq-full]');
			var label  = button.querySelector('[data-faq-label]');

			if (!teaser || !full) {
				return;
			}

			// Nothing was truncated, so the control would be pointless.
			if (teaser.textContent.trim() === full.textContent.trim()) {
				button.hidden = true;
				return;
			}

			button.addEventListener('click', function () {
				var open = button.getAttribute('aria-expanded') === 'true';

				button.setAttribute('aria-expanded', String(!open));
				teaser.hidden = !open;
				full.hidden   = open;

				if (label) {
					label.textContent = open ? 'Read More' : 'Read Less';
				}
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Category tabs (office locations)
	 * ------------------------------------------------------------------ */

	function initFilters() {
		var group = document.querySelector('[data-filter-group]');
		var list  = document.querySelector('[data-filter-items]');

		if (!group || !list) {
			return;
		}

		var empty = document.querySelector('[data-filter-empty]');
		var tabs  = Array.prototype.slice.call(group.querySelectorAll('[data-filter]'));
		var items = Array.prototype.slice.call(list.children);

		function apply(kind) {
			var shown = 0;

			items.forEach(function (item) {
				var match = kind === 'all' || item.getAttribute('data-kind') === kind;
				item.hidden = !match;
				if (match) { shown++; }
			});

			tabs.forEach(function (tab) {
				var active = tab.getAttribute('data-filter') === kind;
				tab.classList.toggle('is-active', active);
				tab.setAttribute('aria-pressed', String(active));
			});

			if (empty) {
				empty.hidden = shown > 0;
			}
		}

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				apply(tab.getAttribute('data-filter'));
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */

	function init() {
		initNav();
		initDismiss();
		initFaq();
		initFilters();

		Array.prototype.forEach.call(
			document.querySelectorAll('[data-carousel]'),
			initCarousel
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
