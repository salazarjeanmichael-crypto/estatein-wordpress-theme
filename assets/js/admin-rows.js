/**
 * Row editor for the theme's list fields.
 *
 * The value stays a textarea of "a | b | c" lines, so nothing about storage or
 * the front end changes; this only replaces the typing surface.
 */
(function () {
	'use strict';

	var CONFIG = window.estateinRows || { fields: {}, i18n: {} };

	function el(tag, className, text) {
		var node = document.createElement(tag);
		if (className) { node.className = className; }
		if (text) { node.textContent = text; }
		return node;
	}

	/**
	 * Turn one textarea into a table of inputs.
	 *
	 * @param {HTMLTextAreaElement} textarea Field holding the piped lines.
	 * @param {Object}              spec     Column definitions for this field.
	 */
	function build(textarea, spec) {
		var columns = spec.columns || [];
		var root    = el('div', 'estatein-rows');
		var body    = el('div', 'estatein-rows__body');

		textarea.classList.add('estatein-rows__source');

		/** Collect the inputs back into the textarea. */
		function sync() {
			var lines = [];

			Array.prototype.forEach.call(body.children, function (row) {
				var cells = Array.prototype.map.call(
					row.querySelectorAll('[data-cell]'),
					function (input) { return input.value.trim(); }
				);

				// A row with nothing in its first column is not a row yet.
				if (cells[0]) {
					lines.push(cells.join(' | '));
				}
			});

			textarea.value = lines.join('\n');
		}

		function addRow(values, focus) {
			var row = el('div', 'estatein-rows__row');

			columns.forEach(function (column, i) {
				var cell  = el('div', 'estatein-rows__cell');
				var value = values[i] || '';
				var input;

				if (column.options) {
					input = el('select');
					input.appendChild(el('option', '', '—'));

					Object.keys(column.options).forEach(function (key) {
						var option = el('option', '', column.options[key]);
						option.value = key;
						if (key === value) { option.selected = true; }
						input.appendChild(option);
					});
				} else {
					input = el('input');
					input.type = 'text';
					input.value = value;
					input.placeholder = column.placeholder || column.label;
				}

				input.setAttribute('data-cell', i);
				input.setAttribute('aria-label', column.label);
				cell.style.flex = (column.width || 100) + ' 1 0';
				cell.appendChild(input);
				row.appendChild(cell);
			});

			var tools = el('div', 'estatein-rows__tools');

			[
				['up', '↑', CONFIG.i18n.up],
				['down', '↓', CONFIG.i18n.down],
				['remove', '×', CONFIG.i18n.remove]
			].forEach(function (def) {
				var button = el('button', 'estatein-rows__btn estatein-rows__btn--' + def[0], def[1]);
				button.type = 'button';
				button.title = def[2];
				button.setAttribute('aria-label', def[2]);
				button.setAttribute('data-action', def[0]);
				tools.appendChild(button);
			});

			row.appendChild(tools);
			body.appendChild(row);

			if (focus) {
				row.querySelector('[data-cell]').focus();
			}

			return row;
		}

		// Header, so the columns are named once rather than on every input.
		if (columns.length > 1) {
			var head = el('div', 'estatein-rows__head');

			columns.forEach(function (column) {
				var cell = el('div', 'estatein-rows__cell', column.label);
				cell.style.flex = (column.width || 100) + ' 1 0';
				head.appendChild(cell);
			});

			head.appendChild(el('div', 'estatein-rows__tools'));
			root.appendChild(head);
		}

		root.appendChild(body);

		var add = el('button', 'button estatein-rows__add', spec.add || '+');
		add.type = 'button';
		add.addEventListener('click', function () {
			addRow([], true);
			sync();
		});
		root.appendChild(add);

		body.addEventListener('input', sync);
		body.addEventListener('change', sync);

		body.addEventListener('click', function (e) {
			var button = e.target.closest('[data-action]');

			if (!button) { return; }

			var row = button.parentNode.parentNode;

			if (button.getAttribute('data-action') === 'remove') {
				row.remove();
			} else if (button.getAttribute('data-action') === 'up' && row.previousElementSibling) {
				body.insertBefore(row, row.previousElementSibling);
			} else if (button.getAttribute('data-action') === 'down' && row.nextElementSibling) {
				body.insertBefore(row.nextElementSibling, row);
			}

			sync();
		});

		textarea.value.split(/\r\n|\r|\n/).forEach(function (line) {
			if (line.trim()) {
				addRow(line.split('|').map(function (part) { return part.trim(); }), false);
			}
		});

		if (!body.children.length) {
			addRow([], false);
		}

		textarea.parentNode.insertBefore(root, textarea);
	}

	function init() {
		// ACF marks its wrapper with the field name; the theme's own meta box
		// falls back to the input name it renders.
		Object.keys(CONFIG.fields).forEach(function (name) {
			var wrap = document.querySelector('.acf-field[data-name="' + name + '"]');
			var textarea = wrap
				? wrap.querySelector('textarea')
				: document.querySelector('textarea[name="estatein_meta[' + name + ']"]');

			if (textarea && !textarea.classList.contains('estatein-rows__source')) {
				build(textarea, CONFIG.fields[name]);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
