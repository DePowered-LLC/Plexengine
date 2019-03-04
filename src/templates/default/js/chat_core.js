/*
@copy
*/

// Chat popups
const openPopup = e => {
    var $content = $('.chat_popup_content', e.target);
    if (!$content.is(':visible')) {
        $content.show();
        $content.css('bottom', '100%');
        setTimeout(() => {
            $content.css('opacity', 1);
            $content.css('bottom', 'calc(100% + 8px)');
        }, 100);
    }
};

$(document).on('mouseenter', '.chat_popup', openPopup);
$(document).on('mousemove', '.chat_popup', openPopup);

$(document).on('mouseleave', '.chat_popup', e => {
    var $content = $(e.target).closest('.chat_popup').children('.chat_popup_content');
    $content.css('opacity', 0);
    $content.css('bottom', '100%');
    setTimeout(() => $content.hide(), 300);
});

function build_html (struct) {
    var result = '';
    struct.forEach(tag => {
        if (typeof tag != 'object') { result += tag; return; }
        let content = typeof tag.content == 'object' ? build_html(tag.content) : tag.content;
        let attr = '';
        if (tag.attr) {
            attr = ' ' + Object.keys(tag.attr).map(atr_name => atr_name + '="' + tag.attr[atr_name] + '"').join(' ');
        }
        if (!content) {
            result += `<${tag.name}${attr}/>`;
        } else {
            result += `<${tag.name}${attr}>${content}</${tag.name}>`;
        }
    });
    return result;
}

function parseTime (time, sec = true) {
    var date = new Date(time * 1000);
    var timestamp = '';
    timestamp += date.getDate().toString().padStart(2, 0);
    timestamp += '.' + (date.getMonth() + 1).toString().padStart(2, 0);
    timestamp += '.' + date.getFullYear();

    timestamp += ' ' + date.getHours().toString().padStart(2, 0);
    timestamp += ':' + date.getMinutes().toString().padStart(2, 0);
    if (sec) timestamp += ':' + date.getSeconds().toString().padStart(2, 0);
    return timestamp;
}

// Local API
var chat = {
    set users_filter (val) {
        if (val != 'all') {
            $('#userlist .category').hide();
            $('#userlist [category="' + val + '"]').show();
        } else {
            $('#userlist [category]').show();
        }
    },

    add_to_msg (text) {
        var input = $('#chat chat-input-wrapper > input');
        input.val(input.val() + text);
    },

    gradient: [],
    update_gradient () {
        $('#chat_gradient > .chat_color').css('background', `linear-gradient(45deg, ${this.gradient[0]} 0%, ${this.gradient[1]} 100%)`);
        $('#chat_gradient > .chat_color')[0].onclick = () => this.msg_color(`g${this.gradient[0].slice(1)}-${this.gradient[1].slice(1)}`);
    },
    
    make_gradient_step: 0,
    make_gradient () {
        switch (this.make_gradient_step) {
            case 0:
                $('#chat_gradient > span:not(.chat_color)').html(lang.select_color + ' 1');
                break;
            case 1:
                $('#chat_gradient > span:not(.chat_color)').html(lang.select_color + ' 2');
                break;
            case 2:
                $('#chat_gradient > span:not(.chat_color)').html(lang.create_gradient);
                this.make_gradient_step = 0;
                return;
        }
        this.make_gradient_step++;
    },

    current_color: '#000000',
    msg_color (color) {
        if (this.make_gradient_step > 0) {
            this.gradient[this.make_gradient_step - 1] = color;
            this.update_gradient();
            this.make_gradient();
        } else this.current_color = color;

        var style = this.current_color;
        if (style.startsWith('g')) {
            style = style.slice(1).split('-');
            style = `background: linear-gradient(45deg, #${style[0]} 0%, #${style[1]} 100%);`;
            style += '-webkit-text-fill-color: transparent;';
            style += '-webkit-background-clip: text;';
        } else {
            style = 'color: ' + style + ';';
        }

        $('#chat_color_example').attr('style', style);
    },

    toggle_banlist () {
        $('#chat_admin .content').slideToggle();
        if ($('#chat_admin .content[banlist]').is(':visible')) {
            $.get('/admin/banlist', res => {
                $tbody = $('#chat_admin .content[banlist] tbody');
                $('tr', $tbody).remove();
                res = JSON.parse(res);
                res.forEach(user => {
                    var info = '';
                    switch (user[0]) {
                        case 'mute':
                            info = 'Мут до ' + parseTime(user[1]);
                            break;
                        case 'ban':
                            info = 'Бан до ' + parseTime(user[1]);
                            break;
                        case 'kick':
                            info = 'Кик';
                            break;
                    }

                    $tbody.append(build_html([{
                        name: 'tr',
                        content: [
                            {
                                name: 'td',
                                content: user.nick
                            },
                            {
                                name: 'td',
                                content: info
                            },
                            {
                                name: 'td',
                                content: [{
                                    name: 'button',
                                    content: 'Разбанить',
                                    attr: {
                                        class: 'btn',
                                        onclick: `chat.unban('${user.nick}')`
                                    }
                                }]
                            }
                        ]
                    }]));
                });
            });
        }
    },

    unban (nick) {
        $.get('/admin/unban?nick=' + encodeURIComponent(nick), () => {
            this.toggle_banlist();
            this.toggle_banlist();
        });
    },

    toggle_admin (data) {
        if (data) {
            data.nick && $('#chat_admin [name="nick"]').val(data.nick);
            data.action && $('#chat_admin [name="action"]').val(data.action);
            $('#chat_admin').attr('m', data.action);
        }

        if (!data && $('#chat_admin').is(':visible')) close_modal('admin');
        else open_modal('admin');
    },

    apply_admin () {
        if($('#chat_admin [name="reason"]').val().trim() == '') return alert(lang.enter_reason + '!');
        if($('#chat_admin [name="nick"]').val().trim() == '') return alert(lang.enter_nick + '!');
        $.post('/admin/action', {
            nick: $('#chat_admin [name="nick"]').val(),
            action: $('#chat_admin [name="action"]').val(),
            reason:  $('#chat_admin [name="reason"]').val(),
            time1:  $('#chat_admin [name="time1"]').val(),
            time2:  $('#chat_admin [name="time2"]').val()
        }, res => {
            if (res == 'no_account') alert(lang.user_not_found);
            $('#chat_admin [name="nick"]').val('');
            $('#chat_admin [name="reason"]').val('');
        });
        chat.toggle_admin();
    },

    open_ignore_list () {
        $.get('/auth/get_ignored')
        open_modal('ignore_list');
    }
};

if (window.default_gradient) {
    chat.gradient = default_gradient;
    chat.update_gradient();
}

$(document).on('click', '#chat .chat_color', e => {
    if (chat.make_gradient_step == 0) {
        $('#chat .chat_color').removeClass('active');
        $(e.target).addClass('active');
    }
});

$('#chat_admin [name="action"]').on('change', e => {
    switch (e.target.value) {
        case 'kick':
            $('#chat_admin').attr('no-time', true);
            break;
        default:
            $('#chat_admin').removeAttr('no-time');
            break;
    }
});

(() => {
    if (!window.nick) return console.error('Required pre-defined `nick` variable');
    if (!window.$) return console.error('Required jQuery');

    $('#chat_admin [name="time2"]').change(e => {
        if (e.target.value == 'inf') $('#chat_admin [name="time1"]').val(-1);
    });

    function parseTimestamp(time) {
        time = new Date(time*1000);
        return [
            (time.getHours() + '').padStart(2, 0),
            (time.getMinutes() + '').padStart(2, 0),
            (time.getSeconds() + '').padStart(2, 0)
        ]
    }
    
    class UserBlock {
        constructor (user) {
            this.uid = user.id;
            this._status = user.status;
            this.gender = user.gender;
            this.nick = user.nick;
            this.verificated = user.verificated;
            this._ignored = false;
        }

        get $el () { return $(document.getElementById('uid' + this.uid)); }

        set ignored (val) {
            if (val) this.$el.attr('ignored', true);
            else this.$el.removeAttr('ignored');
            this._ignored = val;
        }

        get status () { return this._status; }
        set status (val) {
            if (this._status == val) return;
            $('[status]', this.$el).attr('class', 'chat_icon_' + val);
            this._status = val;
        }

        get html () {
            return build_html([{
                name: 'div',
                attr: {
                    class: 'user',
                    id: 'uid' + this.uid,
                    ignored: this._ignored
                },
                content: [{
                        name: 'i',
                        attr: {
                            status: true,
                            class: 'chat_icon_' + this._status
                        }
                    }, {
                        name: 'i',
                        attr: {
                            gender: true,
                            class: 'chat_icon_' + this.gender
                        }
                    }, {
                        name: 'i',
                        attr: {
                            info: true,
                            'load-modal': 'profile',
                            'load-path': '/id' + this.uid + '?short',
                            class: 'chat_icon_info'
                        }
                    },
                    this.nick,
                    this.verificated == '1' ? {
                        name: 'i',
                        attr: {
                            class: 'chat_icon_verificated',
                            style: 'margin: 4px; vertical-align: bottom;'
                        }
                    } : {}
                ]
            }]);
        }

        render () {
            $('#userlist [category="' + this.gender + '"] > .content').append(this.html);
            $('#userlist [category="' + this.gender + '"] [count]')[0].innerHTML++;
            $('#userlist [category="all"] [count]')[0].innerHTML++;
        }

        remove () {
            this.$el.remove();
            $('#userlist [category="' + this.gender + '"] [count]')[0].innerHTML--;
            $('#userlist [category="all"] [count]')[0].innerHTML--;
        }
    }

    var rendered = {};
    function updateOnline (list, ignored) {
        var $table = $('[modal-name="ignore_list"] .content > table');
        $table.html('');
        if (ignored.length > 1 || ignored.length == 1 && ignored[0] != '') {
            $table.append(ignored.map(nick => {
                return build_html([{
                    name: 'tr',
                    content: [{
                        name: 'td',
                        content: nick
                    }, {
                        name: 'td',
                        attr: { style: 'text-align: right' },
                        content: [{
                            name: 'button',
                            attr: { class: 'btn', unignore: nick },
                            content: lang.unignore
                        }]
                    }]
                }]);
            }));
            $('.left_block > .chat_icon_ignore_list').attr('total', ignored.length);
            $('[modal-name="ignore_list"] .content > [empty]').hide();
        } else {
            $('.left_block > .chat_icon_ignore_list').attr('total', 0);
            $('[modal-name="ignore_list"] .content > [empty]').show();
        }

        var renderCheck = Object.assign({}, rendered);
        list.forEach(user => {
            if (user.id == -1) {
                user.list.forEach(guest => {
                    if (!('-1' + guest in rendered)) {
                        var guestBlock = new UserBlock({
                            id: '-1' + guest,
                            status: 'chat',
                            gender: 'guest',
                            nick: guest
                        });
                        guestBlock.render();
                        rendered['-1' + guest] = guestBlock;
                    }
                    delete renderCheck['-1' + guest];
                });
            } else if (user.id in rendered) {
                rendered[user.id].status = user.status;
                rendered[user.id].ignored = !!~ignored.indexOf(user.nick);
                delete renderCheck[user.id];
            } else {
                const userBlock = new UserBlock(user);
                userBlock.render();
                userBlock.ignored = !!~ignored.indexOf(user.nick);
                $('#userlist .nano').nanoScroller();
                rendered[user.id] = userBlock;
                delete renderCheck[user.id];
            }
        });

        Object.keys(renderCheck).forEach(rKey => {
            renderCheck[rKey].remove();
            delete rendered[rKey];
        });
    }

    function updateSmiles (smiles) {
        var packs_list = $('#chat chat-smile-categories');
        Object.keys(smiles).forEach((smile_group, key) => {
            $('#smiles').append('<div class="smile_group" smile_group="'+smile_group+'"></div>');
            smiles[smile_group].forEach(smile_text => {
                $('#smiles > [smile_group="'+smile_group+'"]').append('<img src="/uploads/smiles/'+smile_text+'.png" onclick="chat.add_to_msg(\' ['+smile_text+']\');" />');
                if(key !== 1) {
                    $('#smiles > [smile_group="'+smile_group+'"]').hide();
                }
            });
            packs_list.append(`<img src="/uploads/smiles/${smile_group}/icon.png" smile_group="${smile_group}">`);
        });
    }

    $(document).on('click', '#chat chat-smile-categories > img', e => {
        $('#smiles > *').hide();
        var smile_group = $(e.currentTarget).attr('smile_group');
        $('#smiles > [smile_group="' + smile_group + '"]').show();
    });

    $(document).on('input', '#chat chat-input-wrapper > input', e => {
        var message_limit_free = message_limit - e.target.value.length;
        if (message_limit_free < 0) {
            e.target.value = e.target.value.slice(0, message_limit);
            message_limit_free = 0;
        }
        $('#chat chat-message-limit').html(message_limit_free);
    });

    $(document).on('focusin', '#chat chat-input-wrapper', e => $(e.currentTarget).attr('focus', true));
    $(document).on('focusout', '#chat chat-input-wrapper', e => $(e.currentTarget).removeAttr('focus'));

    $(document).on('click', '#chat chat-user-name', e => {
        $('#chat chat-send-to > input').val($(e.currentTarget).text().trim());
        $('#chat chat-input-wrapper > input').focus();
    });

    $(document).on('click', '#chat chat-clear', () => $('#chat chat-list').html(''));

    function parseSpyMsg (msg) {
        var args = msg.message.split(';');
        msg.nick = lang.spy_nick;
        if (args[1]) args[1] = args[1].split('&#59').join(';');
        switch (args[0]) {
            case 'remove':
                $('chat-message[msg_id="' + args[1] + '"]').remove();
                return;
            case 'enter':
                msg.message = lang.spy_join
                    .split('{nick}').join(args[1])
                    .split('{flag}').join(`<img src="/public/img/flags/${args[2]}.gif" />`);
                break;
            case 'leave':
                msg.message = lang.spy_leave
                    .split('{nick}').join(args[1]);
                break;
            case 'status':
                msg.message = lang.spy_status_change
                    .split('{nick}').join(args[1])
                    .split('{status}').join(lang['status_' + args[2]]);
                break;
            case 'kick':
                msg.message = lang.spy_kick
                    .split('{nick}').join(args[1])
                    .split('{reason}').join(args[2]);
                msg.light = true;
                break;
            case 'mute':
                msg.message = lang.spy_mute
                    .split('{nick}').join(args[1])
                    .split('{reason}').join(args[2])
                    .split('{time}').join(parseTime(args[3]));
                msg.light = true;
                break;
            case 'ban':
                msg.message = lang.spy_ban
                    .split('{nick}').join(args[1])
                    .split('{reason}').join(args[2])
                    .split('{time}').join(parseTime(args[3]));
                msg.light = true;
                break;
        }
        return msg;
    }

    var sound_mode = true;
    var loadedMsgList = [];
    var is_first = true;
    function insertMessages (msgList) {
        var chat_list = $('#chat > chat-list');
        var is_scrolled = chat_list.innerHeight() + chat_list.scrollTop() + 2 < chat_list.prop('scrollHeight');
        var is_sound = false;

        msgList.forEach(msg => {
            msg.id = parseInt(msg.id);
            if (loadedMsgList.includes(msg.id)) return;
            loadedMsgList.push(msg.id);
        
            if (msg.user_id == 0) msg = parseSpyMsg(msg);
            else msg.message = msg.message.split('<').join('&lt;').split('>').join('&gt;');
            if (!msg) return;
            
            if (msg.message.indexOf('') === 0) {
                msg.message.replace('@')
            }
            
            msg.light = msg.light || false;
            if (msg.message.search(/^[_a-zA-Z0-9А-Яа-яіІїЇєЄ;]+,/) !== -1) {
                if (msg.message.startsWith(nick + ',')) {
                    msg.message = `<b>${nick}</b>${msg.message.slice(nick.length)}`;
                    msg.light = true;
                    !is_first && (is_sound = true);
                }

                if (msg.nick == nick) {
                    var to_nick = msg.message.split(',')[0];
                    msg.message = `<b>${to_nick}</b>${msg.message.slice(to_nick.length)}`;
                    msg.light = true;
                }
            }
            
            // Parse colors
            if (msg.color.startsWith('#')) msg.color = 'color: ' + msg.color;
            else if (msg.color.startsWith('g')) {
                var colors = msg.color.slice(1).split('-');
                msg.color = `background: linear-gradient(45deg, #${colors[0]} 0%, #${colors[1]} 100%);`;
                msg.color += '-webkit-background-clip: text;';
                msg.color += '-webkit-text-fill-color: transparent;';
            }

            // Parse smiles and links
            msg.message = msg.message.replace(/\[([0-9]+\/[0-9]+)\]/g, '<img src="/uploads/smiles/$1.png" />');
            msg.message = msg.message.replace(/((\w|[:])+:\/\/(\w+.?)*(\/\w+)*\??(&?\w+=\w*)*)/, '<a href="/away?r=$1" target="_blank">$1</a>');
            chat_list.append(build_html([{
                name: 'chat-message',
                content: [
                    {
                        name: 'chat-timestamp',
                        content: parseTimestamp(msg.timestamp).join(':')
                    },
                    {
                        name: 'chat-user-name',
                        content: msg.nick
                    },
                    {
                        name: 'span',
                        attr: {
                            style: msg.color
                        },
                        content: msg.message
                    },
                    access == 'admin' ? {
                        name: 'i',
                        attr: {
                            class: 'close',
                            remove: true,
                            tooltip: lang.remove
                        }
                    } : '',
                    {
                        name: 'i',
                        attr: {
                            class: 'chat_icon_reply',
                            tooltip: lang.reply
                        }
                    }
                ],
                attr: { msg_id: msg.id, light: msg.light }
            }]));
        });

        if (!is_scrolled) {
            chat_list.scrollTop(chat_list.prop('scrollHeight'));
        }
        if (sound_mode && is_sound) $('#chat audio[message]')[0].play();
    }
    
    $(document).on('click', '.notification > [delete]', e => {
        $.get('/notifications/remove?id=' + $(e.target).attr('delete'), () => {
            $(e.target).parent().remove();
        });
    });
    
    // Receive data
    var lastUpdate;
    function update () {
        var getURL = '/helper/load_data';
        if (lastUpdate) {
            var t = (Math.floor(Date.now() / 1000) - lastUpdate);
            if (t <= 0) return;
            getURL += '?t=' + t;
        }

        $.get(getURL, data => {
            try {
                data = JSON.parse(data);
            } catch(err) {
                console.error('@load_data.error', data);
                setTimeout(update, 1000);
                return;
            }

            if (data.limitation) {
                switch (data.limitation[0]) {
                    case 'ban':
                    case 'kick':
                        sessionStorage.setItem('limitation', data.limitation.join(';'));
                        window.location.href = '/';
                        return;
                }
            }

            lastUpdate = Math.floor(Date.now() / 1000);
            insertMessages(data.msgs, true);
            updateOnline(data.online, (data.ignored || '').split(','));
            data.smiles && updateSmiles(data.smiles);

            data.notifications = data.notifications.map(n => {
                if ($('#n-' + n.id)[0]) return;

                n.info = JSON.parse(n.info);
                return build_html([{
                    name: 'div',
                    attr: {
                        class: 'notification',
                        id: 'n-' + n.id
                    },
                    content: [
                        {
                            name: 'img',
                            attr: { src: '/public/img/icons/note.png' }
                        },
                        {
                            name: 'p',
                            content: (() => {
                                switch (n.type) {
                                    case 'profile_view':
                                        return lang.notifi_viev_info.split('{user}').join('<b>' + n.info.sender_nick + '</b>');
                                }
                            })()
                        },
                        {
                            name: 'span',
                            attr: { delete: n.id },
                            content: lang.delete
                        },
                        {
                            name: 'span',
                            content: parseTime(n.timestamp, false)
                        } 
                    ]
                }]);
            });
            $('[notifications] > .sup').html(data.notifications.length);
            if (!data.notifications.length) {
                $('[notifications] > .sup').css('animation', '');
                $('#no_notifications').show();
            } else {
                $('[notifications] > .sup').css('animation', 'pulse 1s infinite');
                $('#no_notifications').hide();
            }

            $('#notifications').append(data.notifications);
            is_first && (is_first = false);
        });
    }
    update();
    setInterval(update, 1500);

    // Send message
    $('#chat').on('submit', () => {
        var input = $('#chat chat-input-wrapper > input');
        input.focus();
        var to = $('#chat chat-send-to > input').val().trim();
        if (!$('#chat chat-send-to > input[type="checkbox"]').prop('checked')) $('#chat chat-send-to > input').val('');
        var message = input.val();
        if (message.trim() == '') { return false; }
        input.val('');
        // TODO: message limit
        if (to != '') { message = to + ', ' + message; }
        $.post('/helper/send_msg', { message, color: chat.current_color }, res => {
            if (res == 'spam') open_modal('spam');
            else if (res.startsWith('mute')) {
                res = res.split(';');
                open_modal('limitation');
                $('#lmodal [reason]').html(res[1]);
				$('#lmodal [info]').html(lang.mute_info.replace('{time}', parseTime(res[2])));
            }
            else if (res == 'ignored') open_modal('ignored');
            else if (res == 'ignored_to') open_modal('ignored_to');
            else $('#balance > span').html((parseInt($('#balance > span').html()) + 10) + ' points');
        });
        $('#chat chat-message-limit').html(message_limit);
        return false;
    });

    // Change status
    $(document).on('click', '#chat chat-status-set', e => {
        var status = $(e.currentTarget).attr('status');
        $.get('/helper/spy_msg?m=st&v=' + status, res => {
            if (res == 'timeout') open_modal('status_spam')
            else $('#chat chat-status > i').attr('class', 'chat_icon_' + status);
            // Close popup
            $(document).click();
        });
    });

    window.onbeforeunload = () => {
        $.get({
            url: '/helper/spy_msg?m=leave',
            async: false
        });
    };

    // Toggle message audio
    $('#chat_tools > [sound]').click(e => {
        sound_mode = !sound_mode;
        var mode = sound_mode ? 'off' : 'on';
        $(e.target).attr('class', 'chat_icon_sound_' + mode);
        $(e.target).attr('tooltip', lang['sounds_' + mode]);
    });
})();
