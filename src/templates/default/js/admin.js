/*
 * Dropdown
 */
$(document).on('click', '.dropdown', e => {
	if (e.target === e.currentTarget || !$(e.target).closest('.dropdown_container')[0]) {
		if ($(e.currentTarget).attr('openned')) {		
			$(e.currentTarget).removeAttr('openned');
		} else {
			$(e.currentTarget).attr('openned', true);
		}
	}
});

$(document).click(e => {
	if (!$(e.target).closest('.dropdown')[0] || $(e.target).closest('.dropdown_container > a.item')[0]) { $('.dropdown').removeAttr('openned'); }
});

$(document).on('mouseouver', '.dropdown > .dropdown_container', e => {
	if(true) {
		if($(e.currentTarget).attr('openned')) {
			$(e.currentTarget).removeAttr('openned');
		} else {
			$(e.currentTarget).attr('openned', true);
		}
	}
});

function apply (mode, data) {
	return new Promise(resolve => {
		$.post('?apply=' + mode, data, resolve);
	});
}