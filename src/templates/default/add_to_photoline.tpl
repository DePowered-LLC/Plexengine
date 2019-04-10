<div id="photo_add">
    <span class="img">
        <img src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg?{{ time() }}" />
    </span>
    <div>| mod_vip_info |</div>
</div>
<div id="photo_footer">
    <span>| mod_vip_money | 10 | coins | : | points_iya | {{ $_SESSION['userdata']['credits'] }} | coins |</span>
    <button class="btn">| mod_vip_place |</button>
</div>
<script>
$('#photo_select > .item').click(e => {
    var $item = $(e.currentTarget);
    $('#photo_add > .img').css('background-image', `url('${$('img', $item).attr('src')}')`);
    $item.addClass('active');
    $item.siblings().removeClass('active');
});

$('#photo_footer > .btn').click(e => {
    $.get('/profile/vip_add', res => {
        close_modal('add-to-photoline');
        if (res == 'no_photo') return alert('| mod_vip_nophoto |');
        if (res == 'no_coins') return alert('| mod_vip_no_coins |');

        res = JSON.parse(res);
        $(build_html([{
            name: 'div',
            attr: {
                onclick: 'openProfileDatings(' + res.uid + ')',
                class: 'item'
            },
            content: [{
                name: 'div',
                attr: { class: 'img-wrapper' },
                content: [{
                    name: 'img',
                    attr: { src: '/uploads/' + res.photo + '.jpg' }
                }, {
                    name: 'span',
                    content: [{
                        name: 'i',
                        attr: { class: 'chat_icon_like' }
                    }, ' 0']
                }]
            }]
        }])).insertAfter('.photoline > :first-child');
        $('#balance [coins]').html(parseFloat($('#balance [coins]').html()) - 10);
    });
});
</script>
<style>
#photo_add > .img > img {
    width: 100%;
    height: 100%;
}

#photo_add > div {
    padding: 15px 20px;
    font-size: 14px;
    color: #6d778a;
}

#photo_add {
    display: flex;
    flex-direction: row;
    align-items: center;
    font-size: 15px;
    color: #333a48;
    padding: 15px 20px;
    position: relative;
}

#photo_add:before {
    content: '';
    position: absolute;
    top: 107px;
    left: 110px;
    background-color: #4CAF50;
    width: 25px;
    height: 25px;
    border-radius: 100%;
}

#photo_add:after {
    content: '';
    position: absolute;
    top: 112px;
    left: 120px;
    width: 5px;
    height: 10px;
    border: 0 solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotateZ(45deg);
}

#photo_add > .img {
    width: 120px;
    height: 120px;
    min-width: 120px;
    border: 1px solid #dadada;
    border-radius: 100%;
    margin-right: 15px;
    overflow: hidden;
}

#photo_select {
    display: flex;
    width: 545px;
    margin-top: 5px;
    white-space: nowrap;
    overflow: auto hidden;
}

#photo_select > .item {
    position: relative;
    width: 70px;
    height: 70px;
    margin: 2px;
}

[modal-name="add-to-photoline"] > .modal {
    width: 720px;
}

#photo_select > .item.active:after {
    box-shadow: inset #282e3e 0 0 0 2px, inset #fff 0 0 0 4px;
    border-radius: 100%;
}

#photo_select > .item > img {
    width: 100%;
    height: 100%;
	border-radius: 100%;
}

#photo_select > .item:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    transition: 0.3s;
}

#photo_footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    margin-top: 15px;
    margin-bottom: -15px;
    background-color: #333a48;
    font-size: 13px;
    color: #fff;
}

#photo_info {
    padding: 15px 20px;
    font-size: 14px;
    color: #6d778a;
}
</style>