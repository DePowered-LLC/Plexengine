{% module System.Auth %}
<form id="chat">
    <div id="chat_heading">Рандомный текст чисто для теста</div>
    <div id="chat_tools">
        <i class="chat_icon_photos" tooltip="| show |" open-pl onclick="open_pl()" style="display: none;"></i>
        <i sound tooltip="| sounds_off |" class="chat_icon_sound_off"></i>
    </div>
    <audio src="/public/message.ogg" message></audio>
    <chat-list></chat-list>
    <div id="chat-send-wrapper">
        <span id="chat-message-limit">300</span>
        <input id="chat-receiver-toggle" type="checkbox" t-right tooltip="| chat_to_clear |">
        <img avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
        <input id="chat-send-input" placeholder="| enter_msg |" />
        <div right>
            <hr />
            {% if Auth::is_access('premium') %}
                <div class="chat_popup">
                    <i class="chat_icon_gradient"></i>
                    <div class="chat_popup_content chat_list">
                        <div style="display: none;">
                            {% for $c in $colors = explode(PHP_EOL, file_get_contents(DATA.'/colors')) %}
                                <i class="chat_color" style="background-color: {{ trim($c) }};" onclick="chat.msg_color('{{ trim($c) }}')"></i>
                            {% endfor %}
                        </div>
                        <div id="chat_color_example">| font_test |</div>
                        <span class="chat_list_item" id="chat_gradient">
                            {# <span onclick="chat.make_gradient()">| create_gradient |</span> #}
                            <span class="chat_color" style="width: 35px; background: #000000;">| apply_gradient |</span>
                        </span>
                        <span onclick="chat.random_gradient()" class="chat_list_item">
                            {# <i class="chat_icon_update" style="width: 20px; height: 20px;"></i> #}
                            <span>| create_gradient |</span>
                        </span>
                    </div>
                </div>
                {% if $_SESSION['userdata']['access'] == 'admin' %}
                    <span><i onclick="chat.toggle_admin()" class="chat_icon_admin"></i></span>
                {% endif %}
                <hr />
            {% endif %}
            <div class="chat_popup">
                <i class="chat_icon_emoji"></i>
                <chat-smile-window class="chat_popup_content">
                    <chat-smile-list>
                        <span>| select_smile_category |</span>
                        <chat-smile-categories></chat-smile-categories>
                    </chat-smile-list>
                </chat-smile-window>
            </div>
            <div class="chat_popup">
                <i class="chat_icon_brush"></i>
                <div class="chat_popup_content chat_list">
                    {% for $c in $colors = explode(PHP_EOL, file_get_contents(DATA.'/colors')) %}
                        <i class="chat_color" style="background-color: {{ trim($c) }};" onclick="chat.msg_color('{{ trim($c) }}')"></i>
                    {% endfor %}
                </div>
            </div>
            <div id="chat-status" class="chat_popup">
                <i class="chat_icon_{{ $_SESSION['userdata']['status'] }}"></i>
                <div class="chat_popup_content chat_list">
                    {% if $_SESSION['userdata']['id'] == -1 %}
                        | ignore_guest |
                    {% else %}
                        <chat-status-set status="chat" class="chat_list_item">
                            <i class="chat_icon_chat"></i>
                            <span>| status_chat |</span>
                        </chat-status-set>
                        <chat-status-set status="dnd" class="chat_list_item">
                            <i class="chat_icon_dnd"></i>
                            <span>| status_dnd |</span>
                        </chat-status-set>
                        <chat-status-set status="na" class="chat_list_item">
                            <i class="chat_icon_na"></i>
                            <span>| status_na |</span>
                        </chat-status-set>
                    {% endif %}
                </div>
            </div>
            <hr />
        </div>
        <button type="submit">| send |</button>
    </div>
</form>

<div id="chat_admin" class="modal_wrapper" modal-name="admin">
    <div class="modal">
        <div class="title">
            | admin_panel | <span class="close"></span>
        </div>
        <div mgr class="content">
            <div>
                <input name="nick" placeholder="| nick |" />
                <select name="action">
                    <option value="ban">| ban |</option>
                    <option value="kick">| kick |</option>
                    <option value="mute">| mute |</option>
                </select>
                <div time>
                    <input name="time1" value="5">
                    <select name="time2">
                        <option selected value="min">| adm_time_min |</option>
                        <option value="h">| adm_time_hours |</option>
                        <option value="d">| adm_time_day |</option>
                        <option value="w">| adm_time_week |</option>
                        <option value="mon">| adm_time_month |</option>
                        <option value="inf">∞</option>
                    </select>
                </div>
            </div>
            <input type="text" name="reason" placeholder="| enter_reason |" />
            <button class="btn" onclick="chat.toggle_banlist()" banlist>| open_banlist |</button>
            <button class="btn" onclick="chat.apply_admin()">| apply |</button>
        </div>
        <div banlist class="content" style="display: none;">
            <table>
                <thead>
                    <th>| nick |</th>
                    <th>| adm_info |</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">| loading |</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn" onclick="chat.toggle_banlist()">| close_banlist |</button>
        </div>
    </div>
</div>

<script>
    var nick = '{{ $_SESSION['userdata']['nick'] }}';
    var message_limit = '{{ $_CONFIG['message_limit'] }}';
    var access = '{{ $_SESSION['userdata']['access'] }}';
    {% if Auth::is_access('premium') %}
    var colors = {{ json_encode($colors) }};
    {% endif %}
    var lang = {
        spy_nick: '| spy_nick |',
        spy_join: '| spy_join |',
        spy_leave: '| spy_leave |',
        spy_status_change: '| spy_status_change |',
        spy_kick: '| spy_kick |',
        spy_ban: '| spy_ban |',
        spy_mute: '| spy_mute |',
        mute_info: '| mute_info |',
        enter_reason: '| enter_reason |',
        enter_nick: '| enter_nick |',
        unignore: '| unignore |',
        reply: '| reply |',
        replyed_message: '| replyed_message |',
        loading: '| loading |',
        remove: '| remove |',

        status_chat: '| status_chat |',
        status_dnd: '| status_dnd |',
        status_na: '| status_na |',

        big_file: '| big_file |',
        pr_photo_low_size: '| pr_photo_low_size |',
        pr_photo_upload_success: '| pr_photo_upload_success |',
        user_not_found: '| user_not_found |',
        
        sounds_off: '| sounds_off |',
        sounds_on: '| sounds_on |',

        create_gradient: '| create_gradient |',
        select_color: '| select_color |',
        notifi_viev_info: '| notifi_viev_info |',
        delete: '| delete |'
    };
    
    /*var afk_timeout;
    $(document).click(() => {
        clearTimeout(afk_timeout);
        setTimeout(() => {
            window.location.href = '/auth/logout';
        }, {{ $_CONFIG['afk_time'] }}*60*1000);
    });*/
</script>
<script defer src="/public/js/chat_core.js"></script>
