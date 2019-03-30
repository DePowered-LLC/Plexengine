<?php
    function getAbout($field) {
        global $vars;
        if (isset($vars->profile['about'][$field])) return $vars->profile['about'][$field];
        else return View::lang('pr_info_none');
    }

    function getAccessLang() {
        global $vars;
        switch ($vars->profile['access']) {
            case 'admin':   $l = 'info_administrator'; break;
            case 'moderator': $l = 'info_moderator';     break;
            case 'premium': $l = 'pr_info_vip';        break;
            case 'user':    $l = 'pr_info_user';       break;
        }
        return View::lang($l);
    }
?>
{% if !isset($_GET['short']) %}
<!DOCTYPE html>
<html profile>
    <head>
        <title>{{ $_CONFIG['site_name'] }}</title>
        <meta charset="UTF-8" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link rel="stylesheet" href="/public/css/profile.css" />
    </head>
    <body id="profile_wrapper">
{% else %}
        <i class="close"></i>
{% endif %}
        {% if !isset($vars->profile) %}
        <h1 style="font-size: 32px;">| no_profile |</h1>
        <p style="font-size: 16px;">| no_profile_info |</p>
        <style>
            [profile] body,
            #profile_wrapper {
                padding: 5px 50px;
            }
        </style>
        {% else %}
        {% if isset($_SESSION['userdata']) %}
        <img avatar class="my_avatar" src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
        <div id="profile_free_gifts">
            <h1>| free |</h1>
            <span>| free_gift_info |</span>
            <img class="gift" src="/public/img/test_gift.png" />
        </div>
        {% else %}
        <style>[profile] > body { margin: 15px auto; }</style>
        {% endif %}
        {% if !isset($_SESSION['userdata']) || $_SESSION['userdata']['id'] != $vars->profile['id'] %}
        <style>i[edit] { display: none; }</style>
        {% endif %}
        <img src="/uploads/covers/id{{ $vars->profile['id'] }}.png" />
        <i edit="cover" class="chat_icon_edit"></i>
        <div id="profile_top">
            {% if $vars->profile['last_online'] + 5 >= time() %}
                <i online="{{ $vars->profile['status'] }}" tooltip="{{ self::lang('status_'.$vars->profile['status']) }}"></i>
            {% endif %}
            {% if isset($_SESSION['userdata']) && $vars->profile['id'] == $_SESSION['userdata']['id'] %}
            <img avatar width="180px" height="180px" src="/uploads/avatars/id{{ $vars->profile['id'] }}.jpg" />
            {% else %}
            <img width="180px" height="180px" src="/uploads/avatars/id{{ $vars->profile['id'] }}.jpg" />
            {% endif %}
            <i edit="avatar" class="chat_icon_edit"></i>
            <span id="profile_status"><b>| pr_info_myid |</b> <a href="http://{{ $_SERVER['SERVER_NAME'] }}/id{{ $vars->profile['id'] }}" target="_blank">http://{{ $_SERVER['SERVER_NAME'] }}/id{{ $vars->profile['id'] }}</a></span>
            <div style="flex: 1 1 100%;">
                <h1>
                    | pr_info | {{ $vars->profile['nick'] }}
                    {% if $vars->profile['verificated'] == 1 %}
                    <i tooltip="| verifed_info |" class="chat_icon_verificated"></i>
                    {% endif %}
                </h1>
                {% if $vars->profile['last_online'] + 5 >= time() %}
                <span>
                    {% if isset($vars->profile['about']['city']) %}
                    {{ $vars->profile['about']['city'] }},
                    {% endif %}
                    {{ $vars->profile['zodiac'] }},
                    | age | {{ $vars->profile['age'] }}
                </span>
                {% if isset($_GET['short']) %}
                <button onclick="write_fpr()" class="btn">| write_to |</button>
                {% endif %}
                {% else %}
                <span>| user_was_online | {{ date('d.m.Y H:i:s', $vars->profile['last_online']) }}</span>
                {% endif %}
                <div class="access">
                    <i class="chat_icon_{{ $vars->profile['access'] }}1"></i>
                    <b>{{ $vars->profile['nick'] }}</b>
                    <span>{{ getAccessLang() }}</span>
                </div>
            </div>
        </div>
        <div id="profile_gifts">
            <div no-gifts>
                <i></i>
                <span>
                    {{ $vars->profile['nick'] }}
                    {% if $vars->profile['gender'] == 'male' %}| pr_info_to_gift_male |{% endif %}
                    {% if $vars->profile['gender'] == 'female' %}| pr_info_to_gift_female |{% endif %}
                </span>
                <!-- <span>| pr_info_sh_all |</span> -->
            </div>
        </div>
        <div class="tabs">
            <div class="caption">
                <span tab-id="info" class="active">| pr_info_about |</span>
                <span tab-id="contacts">| pr_info_contacts |</span>
                <span tab-id="photos">| pr_info_photo |</span>
            </div>
            <form id="profile_about" tab-id="info" class="tab flex active">
                <div style="position: relative; flex: 1;">
                    <h1>| pr_info_about_me |</h1>
                    <p edit="info">{{ getAbout('info') }}</p>
                    <i edit class="chat_icon_edit"></i>
                    <br />
                    
                    <table>
                        <tr>
                            <td>| pr_info_date |</td>
                            <td>{{ $vars->profile['date_of_birth'] }}</td>
                        </tr>
                        <tr>
                            <td>| pr_info_city |</td>
                            <td edit="city">{{ getAbout('city') }}</td>
                        </tr>
                        <tr>
                            <td>| pr_info_fam_status |</td>
                            <td edit="family_status">{{ getAbout('family_status') }}</td>
                        </tr>
                        <tr>
                            <td>| pr_info_work |</td>
                            <td edit="work">{{ getAbout('work') }}</td>
                        </tr>
                        <tr>
                            <td>| pr_info_www |</td>
                            <td edit="site">{{ getAbout('site') }}</td>
                        </tr>
                    </table>
                    <br />
                    <button style="display: none;" class="btn" type="submit">| apply |</button>
                </div>
            </form>
            <form id="profile_contacts" tab-id="contacts" class="tab">
                <div>
                    <i edit class="chat_icon_edit"></i>
                    <span edit="vk"><img src="/public/img/icons/vk.png" /> {{ getAbout('vk') }}</span>
                    <span edit="phone"><img src="/public/img/icons/phone.png" /> {{ getAbout('phone') }}</span>
                    <span edit="skype"><img src="/public/img/icons/skype.png" /> {{ getAbout('skype') }}</span>
                    <span edit="inst"><img src="/public/img/icons/inst.png" /> {{ getAbout('inst') }}</span>
                </div>
                <br />
                <button style="display: none;" class="btn" type="submit">| apply |</button>
            </form>
            <div tab-id="photos" class="tab"></div>
        </div>
        {% endif %}
        <script>
            $('#profile_about i[edit]').click(e => {
                if ($('#profile_about').attr('editing')) return;
                else {
                    var $about = $('#profile_about [edit="info"]');
                    var about_val = $about.html();
                    if (about_val == '| pr_info_none |') about_val = '';
                    $about.html($('<textarea maxlength="200">').html(about_val));
                    $('#profile_about tr > td[edit]:last-child').each((key, field) => {
                        var val = $(field).html();
                        if (val == '| pr_info_none |') val = '';
                        $(field).html($('<input maxlength="26">').val(val));
                    });
                    $('#profile_about button[type="submit"]').show();
                    $(e.target).hide();
                    $('#profile_about').attr('editing', true);
                }
            });

            $('#profile_about').on('submit', e => {
                e.preventDefault();
                var $btn = $('#profile_about button[type="submit"]');
                if ($('#profile_about button[type="submit"]').attr('disabled')) return false;
                var data = {};
                $btn.attr('disabled', true);

                $('#profile_about i[edit]').show();
                var $about = $('#profile_about [edit="info"] > textarea');
                data.info = $about.val();
                if (data.info.trim() == '') {
                    $about.parent().html('| pr_info_none |');
                    data.info = 'none';
                } else {
                    $about.parent().html(data.info);
                }

                $('#profile_about tr > td[edit]:last-child > input').each((key, field) => {
                    var val = $(field).val();
                    if (val.trim() == '') {
                        data[$(field).parent().attr('edit')] = 'none';
                        $(field).parent().html('| pr_info_none |');
                    } else {
                        data[$(field).parent().attr('edit')] = val;
                        $(field).parent().html(val);
                    }
                });

                Object.keys(data).forEach(k => data[k].trim() == '' && (data[k] = 'none'));
                $.post('/auth/profile_save', data, res => {
                    $btn.removeAttr('disabled');
                    $btn.hide();
                    $('#profile_about').removeAttr('editing');
                });
                return false;
            });
            
            $('#profile_contacts i[edit]').click(e => {
                if ($('#profile_contacts').attr('editing')) return;
                else {
                    $('#profile_contacts span[edit]').each((key, field) => {
                        var val = $(field).text().trim();
                        if (val == '| pr_info_none |') val = '';
                        var $img = $('img', field).clone();
                        $(field).html($('<input maxlength="26">').val(val)).prepend($img);
                    });
                    $('#profile_contacts button[type="submit"]').show();
                    $(e.target).hide();
                    $('#profile_contacts').attr('editing', true);
                }
            });

            $('#profile_contacts').on('submit', e => {
                e.preventDefault();
                var $btn = $('#profile_contacts button[type="submit"]');
                if ($('#profile_contacts button[type="submit"]').attr('disabled')) return false;
                var data = {};
                $btn.attr('disabled', true);

                $('#profile_contacts i[edit]').show();
                $('#profile_contacts span[edit]').each((key, field) => {
                    var val = $('input', field).val();
                    var $img = $('img', field).clone();
                    if (val.trim() == '') {
                        $(field).html('| pr_info_none |');
                        data[$(field).attr('edit')] = 'none';
                    } else {
                        data[$(field).attr('edit')] = val;
                        $(field).html(val);
                    }
                    $(field).prepend($img);
                });

                Object.keys(data).forEach(k => data[k].trim() == '' && (data[k] = 'none'));
                $.post('/auth/profile_save', data, res => {
                    $btn.removeAttr('disabled');
                    $btn.hide();
                    $('#profile_contacts').removeAttr('editing');
                });
                return false;
            });

            function write_fpr () {
                $('#chat chat-send-to > input').val('{{ $vars->profile['nick'] }}');
                $('#chat-send-input').focus();
                close_modal('profile');
            }
            
            $avatar = $('img[avatar]');
            $avatar.attr('src', $avatar.attr('src').split('?')[0] + '?' + Math.random());
        </script>
{% if !isset($_GET['short']) %}
    </body>
    <script src="/public/js/main.js"></script>
</html>
{% endif %}
