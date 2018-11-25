/*
@copy
*/

// Chat popups
$(document).on('click', '.chat_popup', e => {
    var content = $('.chat_popup_content', e.target);
    if (content.prop('offsetHeight') == 0 && content.prop('offsetWidth') == 0) {
        content.show();
        content.css('bottom', '100%');
        setTimeout(() => {
            content.css('opacity', 1);
            content.css('bottom', 'calc(100% + 8px)');
        }, 100);
    } else {
        content.css('opacity', 0);
        content.css('bottom', '100%');
        setTimeout(() => content.hide(), 300);
    }
});

$(document).on('click', e => {
    var popup = $(e.target).closest('.chat_popup');

    $('.chat_popup_content').each((key, content) => {
        var content = $(content);
        if (!popup.has(content).length && content.is(':visible')) {
            content.css('opacity', 0);
            content.css('bottom', '100%');
            setTimeout(() => content.hide(), 300);
        }
    });
});

// Local API
var chat = {
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
        $.post('/modules/Admin/action', {
            nick: $('#chat_admin [name="nick"]').val(),
            action: $('#chat_admin [name="action"]').val(),
            reason:  $('#chat_admin [name="reason"]').val(),
            time1:  $('#chat_admin [name="time1"]').val(),
            time2:  $('#chat_admin [name="time2"]').val()
        }, () => {
            $('#chat_admin [name="nick"]').val('');
            $('#chat_admin [name="reason"]').val('');
        });
        chat.toggle_admin();
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
    $('#chat_admin').attr('m', e.target.value);
});

(() => {
    if (!window.nick) return console.error('Required pre-defined `nick` variable');
    if (!window.$) return console.error('Required jQuery');

    $('#chat_admin [name="time2"]').change(e => {
        if (e.target.value == 'inf') $('#chat_admin [name="time1"]').val(-1);
    })

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

    function parseTimestamp(time) {
        time = new Date(time*1000);
        return [
            (time.getHours() + '').padStart(2, 0),
            (time.getMinutes() + '').padStart(2, 0),
            (time.getSeconds() + '').padStart(2, 0)
        ]
    }
    
    var online_list = [],
        online_guests = [];
    var last_guests = 0;
    function updateOnline (list, ignored) {
        // Stored users
        online_list.forEach((user, key) => {
            var listIndex;
            var userData = list.find((u, i) => {
                listIndex = i;
                return u.id == user.id;
            });

            if (!userData) {
                online_list.splice(key, 1);
                $('#userlist #uid' + user.id).remove();
                $('#userlist > [category="' + user.gender + '"] [count]')[0].innerHTML--;
                $('#userlist > [category="all"] [count]')[0].innerHTML--;
            } else {
                $('#uid' + user.id + ' > [status]').attr('class', 'chat_icon_' + userData.status);
                $('#uid' + user.id).attr('ignored', ignored.indexOf(userData.nick) !== -1);
                list.splice(listIndex, 1);
            }
        });

        // New users
        list.forEach(user => {
            if (user.id == -1) {
                // Last is always empty
                user.list = user.list.slice(0, user.list.length - 1);
                $('#userlist > [category="guest"] > .content').html('');
                user.list.forEach(guest => {
                    var user_block = build_html([{
                        name: 'div',
                        attr: {
                            class: 'user',
                            id: 'uid' + user.id,
                            ignored: ignored.indexOf(user.nick) !== -1
                        },
                        content: [{
                            name: 'i',
                            attr: {
                                status: true,
                                class: 'chat_icon_chat'
                            }
                        }, {
                            name: 'i',
                            attr: {
                                gender: true,
                                class: 'chat_icon_guest'
                            }
                        }, {
                            name: 'i',
                            attr: {
                                info: true,
                                class: 'chat_icon_info'
                            }
                        }, guest]
                    }]);
                    $('#userlist > [category="guest"] > .content').append(user_block);
                    online_guests.push(guest);
                });
                
                $('#userlist > [category="guest"] [count]')[0].innerHTML = user.list.length;
                var last_all = parseInt($('#userlist > [category="all"] [count]')[0].innerHTML);
                $('#userlist > [category="all"] [count]')[0].innerHTML = last_all + user.list.length - last_guests;
                last_guests = user.list.length;
                return;
            }

            var user_block = build_html([{
                name: 'div',
                attr: {
                    class: 'user',
                    id: 'uid' + user.id
                },
                content: [{
                        name: 'i',
                        attr: {
                            status: true,
                            class: 'chat_icon_' + user.status
                        }
                    }, {
                        name: 'i',
                        attr: {
                            gender: true,
                            class: 'chat_icon_' + user.gender
                        }
                    }, {
                        name: 'i',
                        attr: {
                            info: true,
                            class: 'chat_icon_info'
                        }
                    },
                    user.nick,
                    user.verificated == '1' ? {
                        name: 'i',
                        attr: {
                            class: 'chat_icon_verificated',
                            style: 'margin: 3px; vertical-align: bottom;'
                        }
                    } : {}
                ]
            }]);

            $('#userlist > [category="' + user.gender + '"] > .content').append(user_block);
            $('#userlist > [category="' + user.gender + '"] [count]')[0].innerHTML++;
            $('#userlist > [category="all"] [count]')[0].innerHTML++;
            online_list.push(user);
        });
    }

    function updateSmiles (smiles) {
        var packs_list = $('#chat chat-smile-categories');
        Object.keys(smiles).forEach((smile_group, key) => {
            $('#smiles').append('<div class="smile_group" smile_group="'+smile_group+'"></div>');
            smiles[smile_group].forEach(smile_text => {
                $('#smiles > [smile_group="'+smile_group+'"]').append('<img src="/public/smiles/'+smile_text+'.png" onclick="chat.add_to_msg(\' ['+smile_text+']\');" />');
                if(key !== 1) {
                    $('#smiles > [smile_group="'+smile_group+'"]').hide();
                }
            });
            packs_list.append(`<img src="/public/smiles/${smile_group}/icon.png" smile_group="${smile_group}">`);
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
        switch (args[0]) {
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
            case 'ban':
                var date = new Date(args[3]*1000);
				var timestamp = '';
				timestamp += date.getDate().toString().padStart(2, 0);
				timestamp += '.' + (date.getMonth() + 1).toString().padStart(2, 0);
				timestamp += '.' + date.getFullYear();

				timestamp += ' ' + date.getHours().toString().padStart(2, 0);
				timestamp += ':' + date.getMinutes().toString().padStart(2, 0);
                timestamp += ':' + date.getSeconds().toString().padStart(2, 0);
                
                msg.message = lang.spy_ban
                    .split('{nick}').join(args[1])
                    .split('{reason}').join(args[2])
                    .split('{time}').join(timestamp);
                msg.light = true;
                break;
        }
        return msg;
    }

    var sound_mode = true;
    var loadedMsgList = [];
    function insertMessages (msgList, is_first = false) {
        var chat_list = $('#chat > chat-list');
        var is_scrolled = chat_list.innerHeight() + chat_list.scrollTop() + 2 < chat_list.prop('scrollHeight');
        var is_sound = false;

        msgList.forEach(msg => {
            msg.id = parseInt(msg.id);
            if (loadedMsgList.includes(msg.id)) return;
            loadedMsgList.push(msg.id);
        
            if (msg.user_id == 0) parseSpyMsg(msg);
            else {
                msg.message = msg.message.split('<').join('&lt;').split('>').join('&gt;');
            }
            
            if (msg.message.indexOf('') === 0) {
                msg.message.replace('@')
            }
            
            msg.light = msg.light || false;
            if (msg.message.search(/^\w+,/) !== -1) {
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

            // Parse smiles
            msg.message = msg.message.replace(/\[([0-9]+\/[0-9]+)\]/g, '<img src="/public/smiles/$1.png" />');
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
    
    // Receive data
    var lastUpdate;
    (function update () {
        var getURL = '/modules/Helper/load_data';
        if (lastUpdate) getURL += '?t=' + (Math.floor(Date.now() / 1000) - lastUpdate);
        $.get(getURL, data => {
            try {
                data = JSON.parse(data);
            } catch {
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
            setTimeout(update, 1500);

            insertMessages(data.msgs, true);
            updateOnline(data.online, data.ignored || []);
            data.smiles && updateSmiles(data.smiles);
        });
    })();

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
        $.post('/modules/Helper/send_msg', { message, color: chat.current_color }, res => {
            if (res == 'spam') open_modal('spam');
            if (res == 'ignored') open_modal('ignored');
            if (res == 'ignored_to') open_modal('ignored_to');
        });
        $('#chat chat-message-limit').html(message_limit);
        return false;
    });

    // Change status
    $(document).on('click', '#chat chat-status-set', e => {
        var status = $(e.currentTarget).attr('status');
        $.get('/modules/Helper/spy_msg?m=st&v=' + status, res => {
            res == 'timeout'
                ? open_modal('status_spam')
                : $('#chat chat-status > i').attr('class', 'chat_icon_' + status);
            // Close popup
            $(document).click();
        });
    });

    window.onbeforeunload = () => {
        $.get({
            url: '/modules/Helper/spy_msg?m=leave',
            async: false
        });
    };

    // Toggle message audio
    $('#chat chat-sounds').click(() => {
        sound_mode = !sound_mode;
        var mode = sound_mode ? 'off' : 'on';
        $('#chat chat-sounds > i').attr('class', 'chat_icon_sound_' + mode);
        $('#chat chat-sounds > span').html(lang['sounds_' + mode]);
    });
})();

/*
function chat_widget(user_hash, helper_path) {
    var domain = 'plexengine.com';
    
    function on_down(reason = '') {
        switch(reason) {
            case '':
            reason = 'Невозможно подключиться к серверу. Проверьте Ваше интернет соединение.<br />Возможно, что на сервере проводятся запланированные тех. работы.';
            break;
    case 'no_domain':
        reason = 'Данный домен не подключён к системе.';
        break;
    case 'domain_blocked':
        reason = 'Данный домен был заблокирован.';
        break;
}

var fall_overlay = build_html([{
    name: 'chat-fall',
    content: [
        {
            name: 'b',
            content: 'Внимание!'
        },
        '<br />',
        '<br />',
        reason
    ]
}]);
chat.querySelector('chat-list').innerHTML = fall_overlay + chat.querySelector('chat-list').innerHTML;
}

// Getting chat-widget node
var chat = document.getElementsByTagName('chat-widget')[0];
if (!chat) {
throw new Error('[Chat] chat-widget tag bot found');
}

// I like beautiful names of variables :D
var adamantite = parseInt(user_hash.substr(-1))?true:false;

// My utility for building html from object
function build_html(struct) {
var result = '';
struct.forEach(tag => {
    if (typeof tag != 'object') { result += tag; return false; }
    let content = typeof tag.content == 'object' ? build_html(tag.content) : tag.content;
    let attr = '';
    if (tag.attr) {
        attr = ' ' + Object.keys(tag.attr).map(atr_name => atr_name + '="' + tag.attr[atr_name] + '"').join(' ');
    }
    if (!content) {
        result += '<' + tag.name + attr + ' />';
    } else {
        result += '<' + tag.name + attr + '>' + content + '</' + tag.name + '>';
    }
});
return result;
};

// On event listener
function on(event, query, cb, is_document = false) {
if (is_document) {
    document.addEventListener(event, function (e) {
        if (e.target.matches(query)) { cb.apply(this, arguments); }
    });
} else {
    chat.querySelectorAll(query).forEach(elem => { elem.addEventListener(event, cb) });
}
}


function closest(target, selector) {
while (target) {
    if (target.matches && target.matches(selector)) return target;
    target = target.parentNode;
}
return null;
}

// Send post
function obj_to_url(obj) {
var str = [];
for (var p in obj) {
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
    }
}
return str.join('&');
}

function send_post(url, params, cb = r => { }) {
var http = new XMLHttpRequest();
http.open('POST', url, true);
http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

http.onreadystatechange = function () {
    if (http.readyState == 4 && http.status == 200) {
        cb(http.responseText);
    }
}
http.send(obj_to_url(params));
}

// Default variables
var ui_vars = {
send: {
    val: 'SEND',
    elem: 'chat-send-btn'
},
to: {
    val: 'TO',
    elem: 'chat-send-to > input',
    attr: 'placeholder'
},
to_clear: {
    val: 'Check the box to not clear the field "to" after sending',
    elem: 'chat-send-to > input[type="checkbox"]',
    attr: 'tooltip'
},
message_limit_free: {
    val: 220,
    elem: 'chat-message-limit'
},
select_smile_category: {
    val: 'Select smiles from category',
    elem: 'chat-smile-list > span'
},
message_sound: {
    val: '',
    elem: 'audio[message]',
    attr: 'src'
},
sounds: {
    val: true,
    elem: 'chat-sounds',
    attr: 'sounds'
},
sounds_stat: {
    val: 'Disable notifications',
    elem: 'chat-sounds > span'
},
enter_msg: {
    val: 'Enter message text...',
    attr: 'placeholder',
    elem: 'chat-input-wrapper > input'
},
chat_clear: {
    val: 'Clear message history',
    elem: 'chat-clear > span'
},
chat: {
    val: 'In chat',
    elem: 'chat-status [status="chat"] > span'
},
attach: {
    val: 'Attach attachment',
    elem: 'chat-attach > span'
},
dnd: {
    val: 'Don`t disturb',
    elem: 'chat-status [status="dnd"] > span'
},
na: {
    val: 'Not here',
    elem: 'chat-status [status="na"] > span'
},
sounds_on: {
    val: 'Enable notifications'
},
sounds_off: {
    val: 'Disable notifications'
}
};

// Registering main class object
var chat_widget_obj = {
elem: chat,
ui_vars,
clear() {
    chat.querySelector('chat-list').innerHTML = '';
},
load_messages(data) {
    function get_smiles(msg) {
        if (!msg) { return false; }
        Object.keys(chat_widget_obj.smiles).forEach(smile_pack => {
            var smile_list = chat_widget_obj.smiles[smile_pack];
            smile_list.forEach(smile_id => {
                msg = msg.split('[' + smile_id + ']').join('<img src="' + chat_widget_obj.smiles_path + '/' + smile_id + '.png" />');
            });
        });
        return msg;
    }

    var is_sound = false;
    var parsed = data.map(val => {
        if (!val.is_spy) {
            val.message = val.message.split('<').join('&lt;').split('>').join('&gt;');
        } else {
            val.message = val.message.replace(new RegExp('{flag;([A-Z]+)}'), '<img src="' + chat_widget_obj.flags_path + '/$1.gif" />');
            var status = val.message.split(new RegExp('{status;([a-z]+)}'))[1];
            if (status) { val.message = val.message.replace(new RegExp('{status;([a-z]+)}'), '<i class="chat_icon_$1"></i> ' + chat_widget_obj.ui_vars[status]); }
        }

        var light = (val.message.split(',')[0].trim() == chat_widget_obj.nick && val.message.split(',')[1]) ? true : false;
        if ((val.message.split(',')[0].trim() == chat_widget_obj.nick || val.message.split(',')[0].indexOf(' ') == -1 && val.user_name == chat_widget_obj.nick) && val.message.split(',')[1]) {
            light = true;
            val.message = val.message.split(',');
            val.message[0] = '<b>' + val.message[0] + '</b>';
            val.message = val.message.join(',');
            if (!is_sound && val.online && val.user_name != chat_widget_obj.nick && chat_widget_obj.ui_vars.sounds) { is_sound = true; }
        }

        return {
            name: 'chat-message',
            content: [
                {
                    name: 'chat-timestamp',
                    content: new Date(val.timestamp*1000).toLocaleTimeString()
                },
                {
                    name: 'chat-user-name',
                    content: val.user_name
                },
                get_smiles(val.message)
            ],
            attr: { msg_id: val.id, light, is_spy: val.is_spy }
        };
    });
    if (is_sound) { chat.querySelector('audio[message]').play(); }

    var html_nodes = document.createElement('div');
    html_nodes.innerHTML = build_html(parsed);
    Array.from(html_nodes.children).forEach(html_node => {
        chat_widget_obj.elem.querySelector('chat-list').appendChild(html_node);
    });

    var msg_list = chat.querySelector('chat-list');
    msg_list.scrollTop = msg_list.scrollHeight;
},
load_smiles(smiles) {
    chat_widget_obj.smiles = smiles;
    var packs_list = chat_widget_obj.elem.querySelector('chat-smile-categories');
    Object.keys(smiles).forEach(pack_id => {
        packs_list.innerHTML += '<img src="' + chat_widget_obj.smiles_path + '/' + pack_id + '/icon.png" smile_group="' + pack_id + '">';
    });
    packs_list.querySelector('[smile_group="' + Object.keys(smiles)[0] + '"]').click();
},
on_message(msg_data) { },
on_user(user_data) { },
on_pack() { },
on_send() { },
on_spam(prefix) { },
add_to_msg(text) {
    var input = chat.querySelector('chat-input-wrapper > input');
    input.value += text;
    input.focus();
    input.dispatchEvent(new Event('input'));
},
message_limit: 220,
helper_path,
flags_path: '/public/img/flags',
smiles_path: '/public/smiles',
spy_nick: 'Spy',
spy_leave: 'User {nick} leave from chat.',
spy_join: 'User {nick} joined to chat.',
spy_status_change: 'User {nick} has changed status to {status}.',
nick: '',
smiles: [],
user_list: {}
};

socket.on('spam', prefix => {
chat_widget_obj.on_spam(prefix);
});

function spy_msg(message) {
var timestamp = (new Date).toGMTString().split(' ')[4].split(':');
timestamp = parseInt(timestamp[0] * 60 * 60) + parseInt(timestamp[1] * 60) + parseInt(timestamp[2]);

chat_widget_obj.load_messages([{
    user_name: chat_widget_obj.spy_nick,
    message,
    timestamp,
    is_spy: true
}]);
}

socket.on('user_list', data => {
var message = false;
switch(data.mode) {
    case 'list':
        chat_widget_obj.user_list = data.users;
        break;
    case 'join':
        chat_widget_obj.user_list[data.socket_id] = data.user;
        message = chat_widget_obj.spy_join.split('{nick}').join(data.user.nick);
        message = message.split('{flag}').join('<img src="' + chat_widget_obj.flags_path + '/' + data.user.country + '.gif" />');
        break;
    case 'status':
        message = chat_widget_obj.spy_status_change.split('{nick}').join(data.user.nick);
        message = message.split('{status}').join('<i class="chat_icon_' + data.user.status + '"></i> ' + chat_widget_obj.ui_vars[data.user.status]);
        break;
    case 'leave':
        message = chat_widget_obj.spy_leave.split('{nick}').join(data.user.nick);
        delete chat_widget_obj.user_list[data.socket_id];
        break;
}
if (message) { spy_msg(message); }
chat_widget_obj.on_user(data);
});

// UI variables proxy
chat_widget_obj.ui_vars = new Proxy(ui_vars, {
get(target, var_key) {
    return target[var_key].val;
},
set(target, var_key, val) {
    target[var_key].val = val;
    if (target[var_key].elem) {
        if (target[var_key].attr) {
            chat_widget_obj.elem.querySelector(target[var_key].elem).setAttribute(target[var_key].attr, val);
        } else {
            chat_widget_obj.elem.querySelector(target[var_key].elem).innerHTML = val;
        }
    }
}
});

// Generating chat html

/*chat_widget_obj.elem.innerHTML = build_html([
{
    name: 'audio',
    content: ' ',
    attr: {
        src: chat_widget_obj.ui_vars.message_sound,
        message: ''
    }
},
{
    name: 'chat-list',
    content: ' '
},
{
    name: 'chat-admin-window',
    content: [
        {
            name: 'chat-admin-caption',
            content: 'Админ панель <i class="close"></i>'
        },
        {
            name: 'chat-admin-cont',
            content: [
                {
                    name: 'input',
                    attr: { class: 'chat_admin_nick', placeholder: 'Ник' }
                },
                {
                    name: 'select',
                    attr: { class: 'chat_admin_do' },
                    content: [
                        {
                            name: 'option',
                            content: 'Предупредить'
                        }
                    ]
                },
                {
                    name: 'select',
                    attr: { class: 'chat_admin_time' },
                    content: [
                        {
                            name: 'option',
                            content: '5 мин'
                        }
                    ]
                },
                {
                    name: 'textarea',
                    attr: { class: 'chat_admin_reason' },
                    content: 'Укажите причину'
                },
                {
                    name: 'button',
                    attr: { class: 'chat_admin_submit' },
                    content: 'Наказать'
                }
            ]
        }
    ]
},
{
    name: 'chat-send',
    content: [
        {
            name: 'chat-send-to',
            content: [
                {
                    name: 'img',
                    attr: {
                        src: '/public/img/no_photo.png'
                    }
                },
                {
                    name: 'b',
                    content: '>>'
                },
                {
                    name: 'input',
                    attr: {
                        placeholder: chat_widget_obj.ui_vars.to
                    }
                },
                {
                    name: 'input', 
                    attr: { type: 'checkbox', tooltip: chat_widget_obj.ui_vars.to_clear }
                }
            ]
        },
        {
            name: 'chat-input-wrapper',
            content: [
                { name: 'input', attr: { placeholder: chat_widget_obj.ui_vars.enter_msg } },
                {
                    name: 'chat-input-right',
                    content: [
                        {
                            name: 'chat-more-group',
                            attr: {
                                class: 'chat_popup'
                            },
                            content: [
                                {
                                    name: 'i',
                                    attr: { class: 'chat_icon_more' },
                                    content: ' '
                                },
                                {
                                    name: 'chat-more-window',
                                    attr: {
                                        class: 'chat_popup_content chat_list'
                                    },
                                    content: [
                                        {
                                            name: 'chat-attach',
                                            attr: { class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_attach' },
                                                    content: ' '
                                                },
                                                '<span>'+chat_widget_obj.ui_vars.attach+'</span>'
                                            ]
                                        },
                                        {
                                            name: 'chat-sounds',
                                            attr: {
                                                class: 'chat_list_item',
                                                sounds: chat_widget_obj.ui_vars.sounds
                                            },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_sound_off' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.sounds_stat + '</span>'
                                            ]
                                        },
                                        {
                                            name: 'chat-clear',
                                            attr: { class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_trash' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.chat_clear + '</span>'
                                            ]
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'chat-smile-group',
                            attr: { class: 'chat_popup chat_icon_emoji' },
                            content: [
                                {
                                    name: 'chat-smile-window',
                                    attr: {
                                        class: 'chat_popup_content'
                                    },
                                    content: [
                                        {
                                            name: 'chat-smile-list',
                                            content: ''
                                        },
                                        {
                                            name: 'span',
                                            content: chat_widget_obj.ui_vars.select_smile_category
                                        },
                                        {
                                            name: 'chat-smile-categories',
                                            content: ' '
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'chat-admin',
                            attr: { class: adamantite?'':'chat_none' },
                            content: [{
                                name: 'i',
                                attr: { class: 'chat_icon_admin' },
                                content: ' '
                            }]
                        },
                        {
                            name: 'chat-message-colors',
                            attr: { class: 'chat_popup' },
                            content: [
                                {
                                    name: 'i',
                                    attr: { class: 'chat_icon_brush' },
                                    content: ' '
                                },
                                {
                                    name: 'chat-message-colors-window',
                                    attr: {
                                        class: 'chat_popup_content chat_list'
                                    },
                                    content: [
                                        {
                                            name: 'chat-clear',
                                            attr: { class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_trash' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.chat_clear + '</span>'
                                            ]
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'chat-status',
                            attr: { class: 'chat_popup' },
                            content: [
                                {
                                    name: 'i',
                                    attr: { class: 'chat_icon_chat' },
                                    content: ' '
                                },
                                {
                                    name: 'chat-status-window',
                                    attr: {
                                        class: 'chat_popup_content chat_list'
                                    },
                                    content: [
                                        {
                                            name: 'chat-status-set',
                                            attr: { status: 'chat', class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_chat' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.chat + '</span>'
                                            ]
                                        },
                                        {
                                            name: 'chat-status-set',
                                            attr: { status: 'dnd', class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_dnd' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.dnd + '</span>'
                                            ]
                                        },
                                        {
                                            name: 'chat-status-set',
                                            attr: { status: 'na', class: 'chat_list_item' },
                                            content: [
                                                {
                                                    name: 'i',
                                                    attr: { class: 'chat_icon_na' },
                                                    content: ' '
                                                },
                                                '<span>' + chat_widget_obj.ui_vars.na + '</span>'
                                            ]
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            name: 'chat-message-limit',
                            content: chat_widget_obj.message_limit
                        }
                    ]
                }
            ]
        },
        {
            name: 'chat-send-btn',
            content: chat_widget_obj.ui_vars.send
        }
    ]
}
]);
chat = chat_widget_obj.elem;

// On message trigger
socket.on('new_msg', msg_data => {
if (chat_widget_obj.on_message(msg_data) === false) { return false; }
var timestamp = (new Date).toGMTString().split(' ')[4].split(':');
timestamp = parseInt(timestamp[0] * 60 * 60) + parseInt(timestamp[1] * 60) + parseInt(timestamp[2]);
chat_widget_obj.load_messages([Object.assign(msg_data, { timestamp, online: true })]);
});

// Sounds toggle
on('click', 'chat-sounds', e => {
chat_widget_obj.ui_vars.sounds = !chat_widget_obj.ui_vars.sounds;
chat_widget_obj.ui_vars.sounds_stat = chat_widget_obj.ui_vars['sounds_' + (chat_widget_obj.ui_vars.sounds ? 'off' : 'on')];
e.currentTarget.querySelector('[class*="chat_icon"]').setAttribute('class', 'chat_icon_sound_' + (chat_widget_obj.ui_vars.sounds ? 'off' : 'on'));
});

// Chat clear
on('click', 'chat-clear', chat_widget_obj.clear);

// Window dismiss
document.addEventListener('click', function (e) {
var handler = closest(e.target, '.chat_popup');
Array.from(document.querySelectorAll('.chat_popup_content')).forEach(win => {
    if (win.parentNode != handler && win.offsetHeight !== 0 && win.offsetWidth !== 0) {
        win.style.opacity = 0;
        win.style.bottom = '100%';
        setTimeout(() => { win.style.display = 'none'; }, 300);
    }
});
});

/* Admin window controller *\/
on('click', 'chat-admin', () => {
var win = chat.querySelector('chat-admin-window');
if (win.offsetHeight !== 0 && win.offsetWidth !== 0) {
    win.style.opacity = 0;
    setTimeout(() => { win.style.display = 'none'; }, 300);
} else {
    win.style.display = 'block';
    setTimeout(() => {
        win.style.opacity = 1;
    }, 100);
}
});

on('click', 'chat-admin-window .close', () => {
var win = chat.querySelector('chat-admin-window');
win.style.opacity = 0;
setTimeout(() => { win.style.display = 'none'; }, 300);
});

on('click', 'chat-admin-window .chat_admin_submit', () => {
socket.emit('admin', {
    nick: chat.querySelector('chat-admin-window .chat_admin_nick').value,
    do: chat.querySelector('chat-admin-window .chat_admin_do').value,
    reason: chat.querySelector('chat-admin-window .chat_admin_reason').innerHTML
});

var win = chat.querySelector('chat-admin-window');
win.style.opacity = 0;
setTimeout(() => { win.style.display = 'none'; }, 300);
});

on('click', 'chat-status-set', (e) => {
chat.querySelector('chat-status').click();
socket.emit('status_change', closest(e.target, 'chat-status-set').getAttribute('status'));
});

return chat_widget_obj;
}*/