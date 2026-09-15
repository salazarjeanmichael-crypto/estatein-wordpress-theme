/**
 * Estatein - front-end behaviour.
 *
 * Pure progressive enhancement: the markup works without any of this, and
 * nothing throws on a missing element, so template parts can be removed.
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

		// Above 1100px the CSS shows the nav anyway, so a stale aria-expanded
		// would announce a menu against a button that is no longer visible.
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

		// Bound to the carousel rather than the document: arrow keys belong to
		// the page for scrolling, and hijacking them globally would break that
		// for everyone not currently inside a carousel.
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
	 * Entrance animations
	 * ------------------------------------------------------------------ */

	var REVEAL = [
		'.page-hero__inner', '.hero__text', '.hero__actions', '.hero__stats',
		'.hero__media', '.section-head', '.feature', '.property-grid > *',
		'.testimonial-grid > *', '.faq-grid > *', '.carousel', '.service-card',
		'.promo', '.value', '.value-card', '.step', '.team-card', '.client',
		'.office', '.enquiry', '.property-search', '.journey__media',
		'.values__panel', '.cta__inner', '.property-facts', '.result'
	].join(',');

	function initReveal() {
		var root = document.documentElement;

		// The inline head script decided this; honouring the same flag keeps
		// the CSS that hides these elements and this code in agreement.
		if (!root.classList.contains('js') || root.classList.contains('no-reveal')) {
			return;
		}

		var parents = [];
		var counts = [];
		var stagger = window.innerWidth > 700;

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				entry.target.classList.add('is-visible');
				io.unobserve(entry.target);
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

		// Tells the head script's watchdog that this file loaded and took over,
		// so it does not unhide everything on the assumption that it failed.
		root.setAttribute('data-reveal-ready', '1');

		Array.prototype.forEach.call(document.querySelectorAll(REVEAL), function (el) {
			// Carousel slides sit outside the clipped viewport, so they would
			// never intersect. The carousel itself animates as one block.
			if (el.closest('.carousel__track')) {
				el.setAttribute('data-revealed', '');
				return;
			}

			var parent = el.parentNode;
			var at = parents.indexOf(parent);

			if (at === -1) {
				parents.push(parent);
				counts.push(0);
				at = parents.length - 1;
			}

			var index = counts[at]++;

			// Stagger siblings, capped so a long grid does not crawl in. Skipped
			// on a phone, where siblings are stacked and a delayed sibling only
			// reads as an item arriving late.
			if (stagger && index > 0 && !el.style.getPropertyValue('--reveal-delay')) {
				el.style.setProperty('--reveal-delay', Math.min(index, 5) * 70 + 'ms');
			}

			el.addEventListener('animationend', function () {
				el.setAttribute('data-revealed', '');
				el.classList.remove('is-visible');
				el.style.removeProperty('--reveal-delay');
			});

			io.observe(el);
		});
	}

	/* ---------------------------------------------------------------------
	 * Property gallery
	 * ------------------------------------------------------------------ */

	function initGallery() {
		var root = document.querySelector('[data-gallery]');

		if (!root) {
			return;
		}

		var track  = root.querySelector('[data-gallery-track]');
		var slides = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-slide]'));
		var thumbs = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-thumb]'));
		var dots   = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-dot]'));
		var prev   = root.querySelector('[data-gallery-prev]');
		var next   = root.querySelector('[data-gallery-next]');
		var index  = 0;

		if (!track || slides.length < 2) {
			return;
		}

		// Two photos at a time on desktop, mirroring the breakpoint in main.css.
		function perView() {
			return window.innerWidth > 700 ? 2 : 1;
		}

		function maxIndex() {
			return Math.max(0, slides.length - perView());
		}

		function render() {
			index = Math.min(index, maxIndex());

			var gap  = parseFloat(getComputedStyle(track).columnGap || '30') || 30;
			var step = slides[0].getBoundingClientRect().width + gap;

			track.style.transform = 'translateX(' + (-index * step) + 'px)';

			if (prev) { prev.disabled = index === 0; }
			if (next) { next.disabled = index >= maxIndex(); }

			// Photos scrolled out of the stage must not be reachable by Tab.
			slides.forEach(function (slide, i) {
				var visible = i >= index && i < index + perView();
				slide.setAttribute('aria-hidden', visible ? 'false' : 'true');
			});

			// Only the leading thumb carries the marker, as in the design; the
			// second photo on screen is still announced as current.
			thumbs.forEach(function (thumb, i) {
				var visible = i >= index && i < index + perView();
				thumb.classList.toggle('is-active', i === index);
				thumb.setAttribute('aria-current', visible ? 'true' : 'false');
			});

			// One dot per stop, so the count follows however many photos a
			// listing has rather than being fixed in the template.
			dots.forEach(function (dot, i) {
				dot.hidden = i > maxIndex();
				dot.classList.toggle('is-active', i === index);
			});
		}

		function show(i) {
			index = Math.max(0, Math.min(maxIndex(), i));
			render();
		}

		thumbs.forEach(function (thumb, i) {
			thumb.addEventListener('click', function () { show(i); });
		});

		if (prev) { prev.addEventListener('click', function () { show(index - 1); }); }
		if (next) { next.addEventListener('click', function () { show(index + 1); }); }

		// Arrow keys once focus is inside, matching the carousels.
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft')  { show(index - 1); }
			if (e.key === 'ArrowRight') { show(index + 1); }
		});

		var resizeTimer;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(render, 150);
		});

		render();
	}

	/* ---------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */

	function init() {
		initNav();
		initDismiss();
		initFaq();
		initFilters();
		initReveal();
		initGallery();

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
