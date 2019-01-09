// var gs = 0;
// setInterval(() => {
// 	gs += 3 * Math.random();
// 	$('body').css({filter: 'hue-rotate('+gs+'deg)'});
// 	$('body').css({transform: 'rotateY('+50*Math.random()+'deg) rotateX('+50*Math.random()+'deg) rotateZ('+50*Math.random()+'deg)'});
// 	if (gs > 360) gs = 0;
// }, 10)

// $(document).on('click', '*', e => {
//     var t = $(e.target);
//     if (!t.is(':visible')) { t.remove(); return;}
//     if (t.prop('childElementCount') > 0) {
//         t.children().each((k, v) => {
//             setTimeout(() => v.click(), 150*k);
//             setTimeout(() => v.remove(), 10000*k);
//         });
//     }
// 	t.css({
// 		position: 'fixed',
// 		top: t.offset().top - parseInt(t.css('margin-top')) /*+ parseInt(t.css('padding-top'))*/,
// 		left: t.offset().left - parseInt(t.css('margin-left')) /*+ parseInt(t.css('padding-left'))*/,
// 		zIndex: 99999999
//     });
// 	t.animate({
// 		top: (100 - Math.random()*200) + '%',
// 		left: (100 - Math.random()*200) + '%'
//     }, 1500);
// });

if (document.cookie.split('tutorial=')[1]) {
    open_modal('help');
    document.cookie = 'tutorial=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

$(document).on('click', e => {
    if (!$(e.target).closest('#userlist .user')[0]) {
        $('#user_menu').hide();
    }
});

function open_profile (uid) {
    $.get('/id' + uid + '?short', res => {
        $('#profile_wrapper').html(res);
        setTimeout(() => open_modal('profile'), 100);
    });
}

$(document).on('click', '#user_menu > .item', e => {
    var uid = $(e.currentTarget).parent().attr('uid');
    var action = $(e.currentTarget).attr('do');
    switch(action) {
        case 'write':
            if (uid == '-1') $('#chat chat-send-to > input').val($(e.currentTarget).parent().attr('nick'));
            else $('#chat chat-send-to > input').val($(`#userlist .user[id="uid${uid}"]`).text().trim());
            $('#chat chat-input-wrapper > input').focus();
            break;
        case 'profile':
            open_profile(uid);
            break;
        case 'ignore':
            $.get('/modules/Helper/ignore?u=' + uid);
            var $user = $(`#userlist .user[id="uid${uid}"]`);
            $user.attr('ignored', $user.attr('ignored') == 'true' ? 'false' : 'true');
            break;
        case 'ban':
        case 'kick':
            chat.toggle_admin({
                action,
                nick: $(`#userlist .user[id="uid${uid}"]`).text().trim()
            });
            break;
    }
});

$(document).on('click', '#userlist .user', e => {
	if(e.target == e.currentTarget) {
        $('#user_menu').show();
        $('#user_menu').attr('uid', $(e.target).attr('id').replace(/^uid/, ''));
        if($(e.target).attr('id') == 'uid-1') {
            $('#user_menu').attr('nick', $(e.target).text().trim());
        }
        $('#user_menu').css('left', e.pageX);
        $('#user_menu').css('top', e.pageY);
	}
});

$(document).on('click', '#userlist .user > [info]', e => {
    open_profile($(e.currentTarget).parent().attr('id').replace(/^uid/, ''));
});

if (sessionStorage.getItem('write_to')) {
    $('#chat chat-send-to > input').val(sessionStorage.getItem('write_to'));
    $('#chat chat-input-wrapper > input').focus();
    sessionStorage.removeItem('write_to');
}
