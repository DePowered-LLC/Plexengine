/*
 █▀▄ █   █▀▀ █ █ █▀▀ █▄ █ ▄▀▀  ▀ █▄ █ █▀▀ 
 █ █ █ ▄ █▀▀ ▄▀▄ █▀▀ █ ▀█ █ ▀▌ █ █ ▀█ █▀▀ 
 █▀  ▀▀▀ ▀▀▀ ▀ ▀ ▀▀▀ ▀  ▀ ▀▀▀  ▀ ▀  ▀ ▀▀▀ 
                             v0.3.2 Alpha
 * Chat Core
 * (C) DePowered LLC & PlexEngine 2018
 * All rights reserved
 */
$('body > div img[alt="www.000webhost.com"]').closest('div').remove();
function change_lang(lng_code) {
    document.cookie = 'lng='+lng_code;
    window.location.reload();
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
function open_modal(modal_name) {
    let elem = $('[modal-name="'+modal_name+'"]');
    elem.css('display', 'flex');
    setTimeout(() => { elem.css('opacity', 1); }, 10);
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

function load_modal(modal, url) {
	let elem = $('[modal-name="' + modal + '"] [loadhere]');
	if(!url) { url = '/'+modal; }
	//if (!elem.attr('loaded')) {
		$.get(url, data => {
			elem.html(data);
			elem.attr('loaded', true);
		});
	//}
	open_modal(modal);
}

/*
 * Dropdown
 */
$(document).on('click', '.dropdown', e => {
	if(e.target === e.currentTarget || !$(e.target).closest('.dropdown_container')[0]) {
		if($(e.currentTarget).attr('openned')) {
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

var tooltip = 0;
$(document).on('mouseenter', '[tooltip]', function () {
	$(this).attr('ltid', tooltip);
	$('body').append('<div tid="' + tooltip + '" class="tooltip">' + $(this).attr('tooltip') + '</div>');
	var $tooltip = $('[tid="' + tooltip + '"]');

	var left = $(this).offset().left + $(this).outerWidth() / 2 - $tooltip.outerWidth() / 2;
	if(left < 0) {
		var styles = document.getElementById('tag_offset_style');
		if (!styles) {
			styles = document.body.appendChild(document.createElement('style'));
			styles.id = 'tag_offset_style';
		}
		styles.innerHTML = '[tid="' + tooltip + '"]:before { left: ' + (left * 2) + 'px }';
		left = 0;
	}
	$tooltip.css('left', left);
	$tooltip.css('top', $(this).offset().top + $(this).outerHeight());
	$tooltip.css('opacity', 1);
	$tooltip.css('margin-top', 8);

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
	$('#vip_photos').slideUp();
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
