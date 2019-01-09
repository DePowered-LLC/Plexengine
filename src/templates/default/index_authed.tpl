<div id="wrapper">
    <div class="main_block">
        <div class="box" id="vip_photos">
            <span class="caption">| mod_vip_photo |</span>
            <span class="close"></span>
            <div class="photoline">
                <div class="add">
                    <img src="/public/img/icons/add_vip_photo.png" />
                    | mod_vip_add |
                </div>
            </div>
        </div>
        {{ self::load('chat_wrapper') }}
    </div><!--
 --><div class="right_block">
        <div class="tabs">
            <span class="tab" do="smiles">smiles</span>
            <span class="tab" do="users">users</span>
        </div>
        <div id="smiles" class="box"></div>
        <div id="userlist" class="box">
            <div>
                <div class="category" category="all">
                    <div class="caption">
                        | all |
                        <span class="right">
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
            <div id="user_cat_select">
                <span>| select_to_view |</span>
                <button onclick="chat.users_filter = 'all'">| all1 |</button>
                <button onclick="chat.users_filter = 'male'">| boys1 |</button>
                <button onclick="chat.users_filter = 'female'">| girls1 |</button>
                <button onclick="chat.users_filter = 'guest'">| guests1 |</button>
            </div>
        </div>
    </div>
</div>

<div class="modal_wrapper" modal-name="ignored">
    <div class="modal">
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
    <div class="modal">
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
    <div class="modal">
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
    <div class="modal">
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
            <img class="full_img" src="/public/img/help1.gif" />
            | chat_help_text |
            <br>
            <br>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/public/css/profile.css" />
<div class="modal_wrapper" modal-name="profile">
    <div class="modal" id="profile_wrapper"></div>
</div>

<div id="user_menu">
    <span do="profile" class="item">| profile |</span>
    <span do="write" class="item">| write_to |</span>
    <span do="ignore" class="item">| ignore |</span>
    {% if $_SESSION['userdata']['access'] == 'admin' %}
    <span do="ban" class="item">| ban |</span>
    <span do="kick" class="item">| kick |</span>
    {% endif %}
</div>

<script>
    var afk_time = {{ $_CONFIG['afk_time'] }};
</script>
<script defer src="/public/js/chat.js"></script>