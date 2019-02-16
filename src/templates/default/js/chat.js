if (document.cookie.split('tutorial=')[1]) {
    open_modal('help');
    document.cookie = 'tutorial=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

$('#search').click(e => {
    $(e.currentTarget).addClass('active');
    $('input', e.currentTarget).focus();
});

$(document).on('click', e => {
    if (!$(e.target).closest('#userlist .user')[0]) {
        $('#user_menu').css('opacity', 0);
        $('#user_menu').css('pointer-events', 'none');
    }
    if (!$(e.target).closest('#search')[0]) $('#search').removeClass('active');
});

$(document).on('click', '[modal-name="ignore_list"] .content > table button[unignore]', e => {
    $.get('/modules/Helper/ignore?n=' + $(e.target).attr('unignore'));
});

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
            load_modal('profile', '/id' + uid + '?short');
            break;
        case 'ignore':
            $.get('/modules/Helper/ignore?u=' + uid);
            var $user = $(`#userlist .user[id="uid${uid}"]`);
            $user.attr('ignored', $user.attr('ignored') == 'true' ? 'false' : 'true');
            break;
        case 'ban':
        case 'mute':
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
        $('#user_menu').css('pointer-events', 'all');
        $('#user_menu').css('opacity', 1);
        $('#user_menu').css('transform', 'scale(1.1)');
        setTimeout(() => $('#user_menu').css('transform', ''), 300);
        $('#user_menu').attr('uid', $(e.target).attr('id').replace(/^uid/, ''));
        if($(e.target).attr('id') == 'uid-1') {
            $('#user_menu').attr('nick', $(e.target).text().trim());
        }
        $('#user_menu').css('left', e.pageX - $('#user_menu').width() / 2);
        $('#user_menu').css('top', e.pageY);
	}
});

if (sessionStorage.getItem('write_to')) {
    $('#chat chat-send-to > input').val(sessionStorage.getItem('write_to'));
    $('#chat chat-input-wrapper > input').focus();
    sessionStorage.removeItem('write_to');
}


$('#userlist .nano-content').on('scroll', e => {
    $('.category', e.currentTarget).each((key, cbox) => {
        if($(cbox).position().top < 0) {
            $('.caption', cbox).css('top', $(cbox).position().top * -1);
        } else {
            $('.caption', cbox).css('top', 0);
        }
    });
});

$(document).on('click', 'chat-message > [remove]', e => {
    var $msg = $(e.target).parent();
    $.get('/modules/Admin/remove_msg?id=' + $msg.attr('msg_id'), res => {
        if (res == 'sys') alert('This is system message.');
        else { $msg.remove(); $('.tooltip').remove(); }
    });
});

var upload_crop;
$('#upload_photo .upload_wrapper > input').on('change', e => {
    if (!e.target.files[0].type.startsWith('image/')) {
        alert('not image error');
        e.target.value = '';
    } else if (e.target.files[0].size / 1024 / 1024 > max_file) {
        alert(lang.big_file);
        e.target.value = '';
    } else {
        var fr = new FileReader();
        fr.onload = () => {
            var img = new Image();
            img.onload = () => {
                if (img.width < 180 || img.height < 180) {
                    alert(lang.pr_photo_low_size);
                    e.target.value = '';
                } else {
                    var $img = $('#upload_photo_preview > [cropper] > img');
                    $img.attr('src', img.src);
                    upload_crop && upload_crop.destroy();
                    upload_crop = new Cropper($img[0], {
                        viewMode: 2,
                        aspectRatio: 1,
                        minWidth: 180,
                        minHeight: 180,
                        minCanvasWidth: 180,
                        minCanvasHeight: 180,
                        minCropBoxWidth: 180,
                        minCropBoxHeight: 180,
                        dragMode: 'move',
                        crop () {
                            var w = upload_crop.getCropBoxData().width * upload_crop.getCanvasData().naturalWidth / upload_crop.getCanvasData().width;
                            if (w < 180) {
                                upload_crop.setCanvasData({
                                    width: w / 180 * upload_crop.getCanvasData().width
                                });
                            }

                            var h = upload_crop.getCropBoxData().height * upload_crop.getCanvasData().naturalHeight / upload_crop.getCanvasData().height;
                            if (h < 180) {
                                upload_crop.setCanvasData({
                                    height: h / 180 * upload_crop.getCanvasData().height
                                });
                            }
                        }
                    });
                    $('#upload_photo_preview').slideDown();
                    $('#upload_photo').slideUp();
                }
            };
            img.src = fr.result;
        };
        fr.readAsDataURL(e.target.files[0]);
    }
});

$('#upload_photo_preview [cancel]').click(e => {
    $('#upload_photo .upload_wrapper > input').val('');
    $('#upload_photo_preview').slideUp();
    $('#upload_photo').slideDown();
});

$('#upload_photo_preview [rotate]').click(e => {
    upload_crop.setData({ rotate: upload_crop.getData().rotate + parseInt($(e.target).attr('rotate')) });
});

$('#upload_photo_preview [upload]').click(e => {
    var data = new FormData();
    var rect = upload_crop.getData();
    Object.keys(rect).forEach(k => data.set(k, rect[k]));
    data.set('file', $('#upload_photo .upload_wrapper > input')[0].files[0]);

    $.post({
        type: 'POST',
        url: '/modules/Auth/upload_photo',
        data,
        cache: false,
        contentType: false,
        processData: false,
        xhr () {
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', e => {
                var percent = (e.loaded / e.total * 100) + '%';
                $('#upload_photo_preview > .progress > *').css('width', percent);
                $('#upload_photo_preview > .progress > *').html(percent);
            });
            return xhr;
        },
        success () {
            alert(lang.pr_photo_upload_success);
            close_modal('upload_photo');
            upload_crop.destroy();
            $('#upload_photo .upload_wrapper > input').val('');
            $('#upload_photo_preview').slideUp();
            $('#upload_photo').slideDown();
            $avatar = $('img[avatar]');
            $avatar.attr('src', $avatar.attr('src').split('?')[0] + '?' + Math.random());
        }
    });
});
