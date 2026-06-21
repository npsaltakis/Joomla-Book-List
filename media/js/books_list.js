/**
 * com_books_list frontend behaviour
 */
((document) => {
	'use strict';

	document.addEventListener('DOMContentLoaded', () => {
		// Auto-submit filter form when a select changes.
		document.querySelectorAll('.booklist-filters select').forEach((select) => {
			select.addEventListener('change', () => {
				const form = select.closest('form');
				if (form) {
					form.submit();
				}
			});
		});
	});
})(document);
