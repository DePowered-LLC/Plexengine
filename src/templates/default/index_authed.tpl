<div id="wrapper">
    <div class="box left_block">
        <i open-modal="my_profile" tooltip="| my_page |" t-right class="chat_icon_home"></i>
        <i tooltip="| security |" t-right class="chat_icon_settings"></i>
        <i open-modal="upload_photo" tooltip="| upload_photo |" t-right class="chat_icon_camera"></i>
        <i tooltip="| gifts |" t-right class="chat_icon_gift"></i>
        <i tooltip="| attach |" t-right class="chat_icon_attach"></i>
        <i open-modal="ignore_list" total="0" tooltip="| ignore_list |" t-right class="chat_icon_ignore_list"></i>
        <i tooltip="| video |" t-right class="chat_icon_video"></i>
        <i tooltip="| favorites |" t-right class="chat_icon_favorite"></i>
        <i tooltip="| chat_help |" load-modal="help_main" load-path="/help" t-right class="chat_icon_help"></i>
        <i tooltip="| rules |" load-modal="rules" t-right class="chat_icon_rules"></i>
    </div>
    <div class="main_block">
        <div class="box" id="vip_photos">
            <span class="caption"><i class="chat_icon_photos"></i> | mod_vip_photo |</span>
            <span class="close" tooltip="| hide |" t-left></span>
            <div class="photoline">
                {% if $_SESSION['userdata']['id'] == -1 %}
                    <div tooltip="| ignore_guest |" t-right class="add item">
                        <img avatar src="/uploads/avatars/id-1.jpg" />
                    </div>
                {% else %}
                    <div load-modal="add-to-photoline" load-path="/add_to_photoline" class="add item">
                        <img avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
                    </div>
                {% endif %}
                {% for $info in DB::find('vip_photos', 'ORDER BY `id` DESC LIMIT 0,3') %}
                <div uid="{{ $info['user_id'] }}" i="{{ $info['id'] }}" class="item">
                    <div class="img-wrapper">
                        <img src="/uploads/{{ $info['photo'] }}.jpg" />
                        <span><i class="chat_icon_like"></i> {{ $info['likes'] }}</span>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
        {{ self::load('chat_wrapper') }}
    </div>
    <div class="right_block">
        <div class="tabs">
            <span class="tab" do="smiles">smiles</span>
            <span class="tab" do="users">users</span>
        </div>
        <div id="smiles" class="box"></div>
        <div id="userlist" class="box">

        <div class="nano">
            <div class="nano-content">
                <div class="category" category="admin">
                    <div class="caption">
                        | admins |
                        <span class="right">
                            <i class="chat_icon_admins"></i>
                            <span count>0</span>
                        </span>
                    </div>
                    <div class="content"></div>
                </div>
                <div class="category" category="male">
                    <div class="caption">
                        | boys |
                        <span class="right">
                            <i class="chat_icon_male"></i>
                            <span count>0</span>
                        </span>
                    </div>
                    <div class="content"></div>
                </div>
                <div class="category" category="female">
                    <div class="caption">
                        | girls |
                        <span class="right">
                            <i class="chat_icon_female"></i>
                            <span count>0</span>
                        </span>
                    </div>
                    <div class="content"></div>
                </div>
                <div class="category" category="guest">
                    <div class="caption">
                        | guests |
                        <span class="right">
                            <i class="chat_icon_guest"></i>
                            <span count>0</span>
                        </span>
                    </div>
                    <div class="content"></div>
                </div>
            </div>
        </div>
        <div id="user_cat_select">
            <span>| select_to_view |</span>
            <button onclick="chat.users_filter = 'all'">| all1 |</button>
            <button onclick="chat.users_filter = 'male'">| boys1 |</button>
            <button onclick="chat.users_filter = 'female'">| girls1 |</button>
            <button onclick="chat.users_filter = 'guest'">| guests1 |</button>
        </div>
    </div>
</div>

<div class="modal_wrapper modal-tooltip" modal-name="vip-sel">
    <div class="modal t-menu" style="width: 150px;">
        <span do="view" class="item">| mod_vip_profile |</span>
        <span do="like" class="item">| mod_vip_like |</span>
    </div>
</div>

<div class="modal_wrapper" modal-name="ignored">
    <div class="modal modal-flat">
        <div class="title">
            | antispam | <span class="close"></span>
        </div>
        <div class="content row">
            <div class="col-10">
                <br>
                | ignored |
                <br>
                <br>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="ignored_to">
    <div class="modal modal-flat">
        <div class="title">
            | antispam | <span class="close"></span>
        </div>
        <div class="content row">
            <div class="col-10">
                <br>
                | ignored_to |
                <br>
                <br>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="spam">
    <div class="modal modal-flat">
        <div class="title">
            | antispam | <span class="close"></span>
        </div>
        <div class="content row">
            <div class="col-10">
                <br>
                | antispam_warn |
                <br>
                <br>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="status_spam">
    <div class="modal modal-flat">
        <div class="title">
            | antispam | <span class="close"></span>
        </div>
        <div class="content row">
            <div class="col-10">
                <br>
                | antispam_status_warn |
                <br>
                <br>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="help">
    <div class="modal">
        <div class="title">
            | chat_help | <span class="close"></span>
        </div>
        <div class="content">
            <img style="display: block;" class="full_img" src="/public/img/help1.gif" />
            | chat_help_text |
            <br>
            <br>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/public/css/profile.css" />
<div class="modal_wrapper" modal-name="profile">
    <div class="modal" id="profile_wrapper" loadhere></div>
</div>

<div class="modal_wrapper modal-tooltip" modal-name="my_profile" t-right-bottom>
    <div class="modal">
        <div class="content">{% include my_profile %}</div>
    </div>
</div>
<div class="modal_wrapper" modal-name="ignore_list">
    <div class="modal">
        <div class="title">
            | ignore_list | <span class="close"></span>
        </div>
        <div class="content">
            {% if $_SESSION['userdata']['id'] == -1 %}
            <h2>| ignore_guest |</h2>
            <img style="display: block; margin: 40px auto;" src="/public/img/icons/list.png" />
            {% else %}
            <table></table>
            <div class="light" empty>
                <h2>| ignored_none |</h2>
                <h5>| ignored_info |</h5>
                <img style="display: block; margin: 40px auto;" src="/public/img/icons/list.png" />
            </div>
            {% endif %}
        </div>
    </div>
</div>

<?php
// Ref: https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function file_upload_max_size() {
    static $max_size = -1;
    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) $max_size = $post_max_size;

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

$max_file = file_upload_max_size() / 1024 / 1024;
?>
<script>var max_file = {{ $max_file }}</script>
<div class="modal_wrapper" modal-name="upload_photo">
    <div class="modal">
        <div class="title">
            | pr_photo_title | <span class="close"></span>
        </div>
        {% if $_SESSION['userdata']['id'] != -1 %}
        <div id="upload_photo" class="content">
            <div>
                <img avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
                {{ str_replace('{size}', $max_file, View::lang('pr_photo_info')) }}
            </div>
            <div class="upload_wrapper">
                <label>| pr_photo_drag |</label>
                <input type="file" accept="image/gif, image/jpeg, image/png" />
            </div>
            <h2>| pr_photo_forbidden |</h2>
            <p>| pr_photo_forb_info |</p>
        </div>
        <div id="upload_photo_preview" class="content" style="display: none;">
            <div cropper>
                <img src="about:blank" />
            </div>
            <div tools>
                <i rotate="-90" class="chat_icon_rotate"></i>
                <i rotate="90" class="chat_icon_rotate" style="transform: scaleX(-1);"></i>
            </div>
            <div class="progress">
                <div>0%</div>
            </div>
            <div style="display: flex; margin-top: 10px; justify-content: space-between;">
                <button cancel class="btn">Отмена</button>
                <button upload class="btn bg_green">Продолжить</button>
            </div>
        </div>
        {% else %}
        <div class="content">
            <h2>| ignore_guest |</h2>
            <img style="display: block; margin: 40px auto;" src="/public/img/icons/list.png" />
        </div>
        {% endif %}
    </div>
</div>

<div id="user_menu" class="t-menu tooltip tooltip-bottom">
    <span do="profile" class="item">| profile |</span>
    <span do="write" class="item">| write_to |</span>
    {% if $_SESSION['userdata']['id'] != -1 %}
    <span do="ignore" class="item">| ignore |</span>
    <span do="report" class="item">| report |</span>
    {% endif %}
    {% if $_SESSION['userdata']['access'] == 'admin' %}
    <span do="ban" class="item">| ban |</span>
    <span do="kick" class="item">| kick |</span>
    <span do="mute" class="item">| mute |</span>
    {% endif %}
</div>

<div id="lmodal" class="modal_wrapper" modal-name="limitation">
    <div class="modal modal-flat" style="min-width: 550px;">
        <div class="title">
            | limitation | <span class="close"></span>
        </div>
        <div class="content" style="text-align: center;">
			<br />
            <span info></span>
			<br />
            | reason |: <span reason></span>
			<br />
			<br />
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="add-to-photoline">
    <div class="modal">
        <div class="title">
            | mod_vip_title | <span class="close"></span>
        </div>
        <div class="content" loadhere>
			{# <br />
            <span info></span>
			<br />
            | reason |: <span reason></span>
			<br />
			<br /> #}
        </div>
    </div>
</div>

<script>
    var afk_time = {{ $_CONFIG['afk_time'] }};
</script>
<script defer src="/public/js/chat.js"></script>