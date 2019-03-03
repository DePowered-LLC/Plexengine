{% module System.Auth %}
<form id="chat">
    <div id="chat_heading">Рандомный текст чисто для теста</div>
    <div id="chat_tools">
        <i class="chat_icon_photos" tooltip="| show |" open-pl onclick="open_pl()" style="display: none;"></i>
        <i sound tooltip="| sounds_off |" class="chat_icon_sound_off"></i>
    </div>
    <audio src="/public/message.ogg" message></audio>
    <chat-list></chat-list>
    <chat-send>
        <chat-send-to>
            <img avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
            <b>>></b>
            <input placeholder="| to |" />
            <input type="checkbox" tooltip="| chat_to_clear |">
        </chat-send-to>
        <chat-input-wrapper>
        <input placeholder="| enter_msg |">
            <chat-input-right>
                <chat-more-group class="chat_popup">
                    <i class="chat_icon_more"></i>
                    <chat-more-window class="chat_popup_content chat_list">
                        <span class="chat_list_item" sounds="true">
                            <i class="chat_icon_sound_off"></i>
                            <span>| sounds_off |</span>
                        </span>
                    </chat-more-window>
                </chat-more-group>
                <chat-smile-group class="chat_popup chat_icon_emoji">
                    <chat-smile-window class="chat_popup_content">
                        <chat-smile-list>
                            <span>| select_smile_category |</span>
                            <chat-smile-categories></chat-smile-categories>
                        </chat-smile-list>
                    </chat-smile-window>
                </chat-smile-group>
                {% if $_SESSION['userdata']['access'] == 'admin' %}
                <i onclick="chat.toggle_admin()" class="chat_icon_admin"></i>
                {% endif %}
                <div class="chat_popup">
                    <i class="chat_icon_brush"></i>
                    <div class="chat_popup_content chat_list">
                        {% for $c in $colors = explode(PHP_EOL, file_get_contents(DATA.'/colors')) %}
                            <i class="chat_color" style="background-color: {{ trim($c) }};" onclick="chat.msg_color('{{ trim($c) }}')"></i>
                        {% endfor %}
                        {% if Auth::is_access('premium') %}
                        <span class="chat_list_item" id="chat_gradient">
                            <span onclick="chat.make_gradient()">| create_gradient |</span>
                            <span class="chat_color" style="width: 35px; background: #000000;">| apply_gradient |</span>
                        </span>
                        {% endif %}
                        <div>
                            <span id="chat_color_example">| font_test |</span>
                        </div>
                    </div>
                </div>
                <chat-status class="chat_popup">
                    <i class="chat_icon_{{ $_SESSION['userdata']['status'] }}"></i>
                    <chat-status-window class="chat_popup_content chat_list">
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
                    </chat-status-window>
                </chat-status>
                <chat-message-limit>300</chat-message-limit>
            </chat-input-right>
        </chat-input-wrapper>
        <button type="submit">| send |</button>
    </chat-send>
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

<div id="lmodal" class="modal_wrapper" modal-name="limitation">
    <div class="modal">
        <div class="title">
            | limitation | <span class="close"></span>
        </div>
        <div class="content">
			<br />
            <span info></span>
			<br />
            | reason |: <span reason></span>
			<br />
			<br />
        </div>
    </div>
</div>

<script>
    var nick = '{{ $_SESSION['userdata']['nick'] }}';
    var message_limit = '{{ $_CONFIG['message_limit'] }}';
    var access = '{{ $_SESSION['userdata']['access'] }}';
    {% if Auth::is_access('premium') %}
    var default_gradient = ['{{ trim($colors[rand(0, count($colors) - 1)]) }}', '{{ trim($colors[rand(0, count($colors) - 1)]) }}'];
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
