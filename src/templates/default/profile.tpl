<?php
    function getAbout($field) {
        if (isset($GLOBALS['profile_data']['about'][$field])) return $GLOBALS['profile_data']['about'][$field];
        else return 'Не указано';/*View::lang('status_dnd');*/
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
        {% if !isset($GLOBALS['profile_data']) %}
        <h1 style="font-size: 32px;">Профиль не найден</h1>
        <p style="font-size: 16px;">Пользователя не существует или Вы пытаетесь посмотреть профиль гостя</p>
        <style>
            [profile] body,
            #profile_wrapper {
                padding: 5px 50px;
            }
        </style>
        {% else %}
        {% if $_SESSION['userdata']['id'] != $GLOBALS['profile_data']['id'] %}
        <style>i[edit] { display: none; }</style>
        {% endif %}
        <img src="/public/covers/id{{ $GLOBALS['profile_data']['id'] }}.png" />
        <i edit="cover" class="chat_icon_edit"></i>
        <div id="profile_top">
            <img width="180px" height="180px" src="/public/avatars/id{{ $GLOBALS['profile_data']['id'] }}.png" />
            <i edit="avatar" class="chat_icon_edit"></i>
            <span id="profile_status">Мой профиль: https://{{ $_SERVER['SERVER_NAME'] }}/id{{ $GLOBALS['profile_data']['id'] }}</span>
            <div style="flex: 1 1 100%;">
                <h1>
                    {{ $GLOBALS['profile_data']['nick'] }}
                    {% if $GLOBALS['profile_data']['verificated'] == 1 %}
                    <i tooltip="| verifed_info |" class="chat_icon_verificated"></i>
                    {% endif %}
                </h1>
                {% if $GLOBALS['profile_data']['last_online'] + 5 >= time() %}
                <span online>| status_chat |</span>
                {% else %}
                <span>Пользователь был онлайн {{ date('d.m.Y H:i:s', $GLOBALS['profile_data']['last_online']) }}</span>
                {% endif %}
            </div>
            {% if $GLOBALS['profile_data']['access'] == 'premium' %}
            <div><img style="margin-top: 5px;" src="/public/img/vip.png" /></div>
            {% endif %}
        </div>
        <div id="profile_gifts">
            <span>Показать все</span>
        </div>
        <div class="tabs">
            <div class="caption">
                <span tab-id="info" class="active">Информация</span>
                <span tab-id="contacts">Контакты</span>
                <span tab-id="photos">Фотографии</span>
            </div>
            <form id="profile_about" tab-id="info" class="tab flex active">
                <div style="position: relative; flex: 1;">
                    <h1>Обо мне</h1>
                    <p edit="info">{{ getAbout('info') }}</p>
                    <i edit class="chat_icon_edit"></i>
                    <br />
                    
                    <table>
                        <tr>
                            <td>Дата рождения:</td>
                            <td>{{ $GLOBALS['profile_data']['date_of_birth'] }}</td>
                        </tr>
                        <tr>
                            <td>Город:</td>
                            <td edit="city">{{ getAbout('city') }}</td>
                        </tr>
                        <tr>
                            <td>Семейное положение:</td>
                            <td edit="family_status">{{ getAbout('family_status') }}</td>
                        </tr>
                        <tr>
                            <td>Место работы:</td>
                            <td edit="work">{{ getAbout('work') }}</td>
                        </tr>
                        <tr>
                            <td>Веб-сайт:</td>
                            <td edit="site">{{ getAbout('site') }}</td>
                        </tr>
                    </table>
                    <br />
                    <button style="display: none;" class="btn" type="submit">Сохранить</button>
                </div>
                <img src="/public/img/zodiac/{{ $GLOBALS['profile_data']['zodiac'] }}.png">
            </form>
            <div tab-id="contacts" class="tab">
                {{ getAbout('contacts') }}
            </div>
            <div tab-id="photos" class="tab"></div>
        </div>
        {% endif %}
        <script>
            $('#profile_about i[edit]').click(e => {
                if ($('#profile_about').attr('editing')) return;
                else {
                    var $about = $('#profile_about [edit="info"]');
                    $about.html($('<textarea maxlength="200">').html($about.html()));
                    $('#profile_about tr > td[edit]:last-child').each((key, field) => {
                        $(field).html($('<input>').val($(field).html()));
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
                $about.parent().html(data.info);
                $('#profile_about tr > td[edit]:last-child > input').each((key, field) => {
                    data[$(field).parent().attr('edit')] = $(field).val();
                    $(field).parent().html($(field).val());
                });

                Object.keys(data).forEach(k => (data[k].trim() == 'Не указано' || data[k].trim() == '') && delete data[k]);
                $.post('/modules/Auth/pofile_save', data, res => {
                    $btn.removeAttr('disabled');
                    $btn.hide();
                    $('#profile_about').removeAttr('editing');
                });
                return false;
            });
        </script>
{% if !isset($_GET['short']) %}
    </body>
    <script src="/public/js/main.js"></script>
</html>
{% endif %}
