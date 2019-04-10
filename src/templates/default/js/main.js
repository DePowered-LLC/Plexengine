/*
@copy
 */
if (!window.loaded) {
function change_lang(lng_code) {
    document.cookie = 'lng='+lng_code;
    window.location.reload();
}

window.alert = text => {
	var $alert = $('<div>').addClass('alert');
	$alert.hide();
	$alert.append($('<span>').html(text));
	$('body').append($alert);
	$alert.fadeIn(500);
	setTimeout(() => $alert.fadeOut(500), 2500);
	setTimeout(() => $alert.remove(), 3000);
}

$(document).on('click', '.select', e => {
	let elem = $(e.target);
	if(elem.hasClass('option')) { return false; }
	if(elem.attr('openned')) {
		elem.removeAttr('openned');
	} else {
		$('.select').removeAttr('openned');
		elem.attr('openned', true);
	}
});

$(document).on('click', '.select > .options > .option', e => {
	let elem = $(e.currentTarget);
	if(!elem.attr('value')) {
	    $('input', elem.closest('.select')).val(elem.text().trim());
	} else {
	    $('input', elem.closest('.select')).val(elem.attr('value').trim());
	}
	$('.selected', elem.closest('.select')).html(elem.text().trim());
	elem.closest('.select').removeAttr('openned');
});

/*
 * Modal
 */
$(document).on('click', '[open-modal]', e => {
	var $this = $(e.target).closest('[open-modal]');
	open_modal($this.attr('open-modal'), $this);
});

$(document).on('click', '[load-modal]', e => {
	var $this = $(e.target).closest('[load-modal]');
	load_modal($this.attr('load-modal'), $this.attr('load-path'), $this);
});

$(document).on('click', e => {
	if (!$(e.target).closest('.modal')[0]) {
		$('.modal_wrapper[closable]').css('opacity', 0);
		setTimeout(() => {
			$('.modal_wrapper[closable]').hide();
		}, 300);
	}
});

function open_modal(modal_name, $caller = null) {
	let $elem = $('[modal-name="'+modal_name+'"]');
	$elem.removeAttr('closable');
	if ($elem.hasClass('modal-tooltip')) {
		var offset = $caller.offset();
		if ($elem.is('[t-right-bottom]')) {
			$elem.css('top', offset.top);
			$elem.css('left', offset.left + $caller.outerWidth());
		} else {
			$elem.css('top', offset.top + $caller.outerHeight());
			$elem.css('left', offset.left + $caller.outerWidth() / 2 - $elem.outerWidth() / 2);
		}
	} else {
		if (!$elem.attr('builded')) {
			var $title = $('.title', $elem);
			var $ch = $title.children().clone();
			$title.html($('<span>').addClass('inner').html($title.text().trim()));
			$title.append($ch);
			$elem.attr('builded', true);
		}
	}
    $elem.css('display', 'flex');
    setTimeout(() => { $elem.css('opacity', 1); }, 10);
    setTimeout(() => { $elem.attr('closable', true); }, 500);
}

function close_modal(modal_name) {
    let elem = $('[modal-name="'+modal_name+'"]');
    elem.css('opacity', 0);
    setTimeout(() => { elem.hide(); }, 300);
}

$(document).on('click', '.modal_wrapper', e => {
    let elem = $(e.target);
    if(elem.hasClass('modal_wrapper')) {
        close_modal(elem.attr('modal-name'));
    }
});

$(document).on('click', '.modal_wrapper .close', e => {
    let elem = $(e.target).closest('.modal_wrapper');
    close_modal(elem.attr('modal-name'));
});

window.modal_loaded = null;
function load_modal(modal, url, $caller = null) {
	let elem = $('[modal-name="' + modal + '"] [loadhere]');
	elem.removeAttr('loaded');
	if(!url) { url = '/' + modal; }
	$.get(url, data => {
		elem.html(data + `<script>
			setTimeout(() => {
				$('[modal-name="${modal}"] [loadhere]').attr('loaded', true);
				window.modal_loaded && window.modal_loaded();
			}, 100);
		</script>`);
	});
	open_modal(modal, $caller);
}

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

function update_tooltip (that, msg, side = 'bottom') {
	var $that = $(that);
	var $tooltip = $('[tid="' + $that.attr('ltid') + '"]');
	$tooltip.html(msg);
	var $helper = $('<i helper>');
	$tooltip.append($helper);
	
	var styles = { opacity: 1 };
	switch (side) {
		case 'top':
			styles.left = $that.offset().left + $that.outerWidth() / 2 - $tooltip.outerWidth() / 2;
			styles.top = $that.offset().top- $tooltip.outerHeight();
			styles.marginTop = -8;
			break;
		case 'bottom':
			styles.left = $that.offset().left + $that.outerWidth() / 2 - $tooltip.outerWidth() / 2;
			styles.top = $that.offset().top + $that.outerHeight();
			styles.marginTop = 8;
			break;
		case 'right':
			styles.left = $that.offset().left + $that.outerWidth();
			styles.top = $that.offset().top + $that.outerHeight() / 2 - $tooltip.outerHeight() / 2;
			styles.marginLeft = 8;
			break;
		case 'left':
			styles.left = $that.offset().left - $tooltip.outerWidth();
			styles.top = $that.offset().top + $that.outerHeight() / 2 - $tooltip.outerHeight() / 2;
			styles.marginLeft = -8;
			break;
	}
	
	if (styles.left < 5) {
		$helper.css({
			left: styles.left - 5,
			right: -styles.left + 5
		});
		styles.left = 5;
	}
	$tooltip.css(styles);
}

var tooltip = 0;
$(document).on('mouseenter', '[tooltip]', function () {
	$(this).attr('ltid', tooltip);
	var side = 'bottom';
	if ($(this).attr('t-right') != undefined) side = 'right';
	else if ($(this).attr('t-top') != undefined) side = 'top';
	else if ($(this).attr('t-left') != undefined) side = 'left';

	var tStyle = $(this).attr('t-style');
	$('body').append('<div tid="' + tooltip + '" class="tooltip tooltip-' + side + (tStyle ? ' tooltip-' + tStyle : '') + '"></div>');
	update_tooltip(this, $(this).attr('tooltip'), side);
	$(this).on('click', () => $('[tid="' + tooltip + '"]').html($(this).attr('tooltip')));
	tooltip++;
	return false;
})

$(document).on('mouseleave', '[tooltip]', function () {
	var $tooltip = $('[tid="' + $(this).attr('ltid') + '"]');
	$tooltip.css('opacity', 0);
	setTimeout(() => { $tooltip.remove(); }, 300);
	$(this).removeAttr('ltid');
	return false;
})

$('#vip_photos > .close').click(() => {
	$('#chat_tools > [open-pl]').fadeIn();
	$('#vip_photos').slideUp();
	$('#chat').attr('full', true);
});

function open_pl () {
	$('#chat_tools > [open-pl]').fadeOut();
	$('#vip_photos').slideDown();
	$('#chat').removeAttr('full');
}

$(document).on('click', '.accordion > .item > .caption', e => {
	$(e.target).siblings('.content').slideToggle();
	$(e.target).parent().siblings('.item').children('.content').slideUp();
});

/*
 * Tabs
 */
$(document).on('click', '.tabs > .caption > [tab-id]', e => {
	$(e.target).siblings().removeClass('active');
	$(e.target).addClass('active');
	$(e.target).closest('.tabs').children('.tab[tab-id]').removeClass('active');
	$(e.target).closest('.tabs').children('.tab[tab-id="'+$(e.target).attr('tab-id')+'"]').addClass('active');
});

window.loaded = true;
}