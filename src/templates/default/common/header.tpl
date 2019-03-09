<!--
@copy
-->
<!DOCTYPE html>
<html>
	<head>
		<title>{{ $_CONFIG['site_name'] }}</title>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="/public/css/main.css" />
		<link rel="stylesheet" href="/public/css/nanoscroller.css" />
		<link rel="shortcut icon" href="/public/favicon.ico" type="image/x-icon" />
		<link rel="yandex-tableau-widget" href="/public/manifest.json" />
		<link rel="manifest" href="/public/manifest.json">
		<meta name="yandex-tableau-widget" content="logo=https://latest.plexengine.com/public/img/logo_tablo.png, color=#ffffff" />
		{# <link rel="stylesheet" href="/public/css/a.css" /> #}
	</head>
	<body>
		{% if isset($_SESSION['userdata']) %}
		<div id="header">
			<div id="menu">
				<img src="/public/img/small_logo.png" />
				{% if $_SESSION['userdata']['id'] == -1 %}
					<span tooltip="| ignore_guest |" class="item" style="font-weight: 500; color: #289de3;">
						<i class="chat_icon_premium m"></i> Premium
					</span>
				{% else %}
					<span class="item" style="font-weight: 500; color: #289de3;">
						<i class="chat_icon_premium m"></i> Premium
					</span>
				{% endif %}
				{% if $_SESSION['userdata']['id'] == -1 %}
					<span tooltip="| ignore_guest |" id="balance" class="item">
						<i class="chat_icon_wallet m"></i>
						<t>0</t> &nbsp;| coins |
					</span>
					<span class="item" tooltip="| ignore_guest |"><i class="chat_icon_alert"></i></span>
					<span class="item" tooltip="| ignore_guest |"><i class="chat_icon_message"></i></span>
				{% else %}
					<span load-modal="wallet" id="balance" class="item">
						<span class="sup">{{ $_SESSION['userdata']['points'] }} points</span>
						<i class="chat_icon_wallet m"></i>
						<t>{{ $_SESSION['userdata']['credits'] }}</t> &nbsp;| coins |
					</span>
					<span notifications open-modal="notifications" class="item" tooltip="| tool_alert |">
						<i class="chat_icon_alert"></i>
						<span class="sup">0</span>
					</span>
					<span class="item" tooltip="| tool_message |"><i class="chat_icon_message"></i></span>
				{% endif %}
				<div>
					<span id="search" class="item">
						<i class="chat_icon_search"></i>
						<input />
						<span>| search |</span>
					</span>
					<span class="item" load-modal="rules">| rules |</span>
					{% if $_SESSION['userdata']['access'] == 'admin' %}
					<a class="item" href="/admin" target="_blank">| adminpanel |</a>
					{% endif %}
					<span class="item">| report |</span>
				</div>
			</div>

			<div>
				<div class="user_block dropdown">
					<span class="name">{{ $_SESSION['userdata']['nick'] }}</span>
					<img avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
					<div class="dropdown_container">
						<img id="user_cover" src="/uploads/covers/id{{ $_SESSION['userdata']['id'] }}.png?s" />
						<img id="user_avatar" avatar src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
						<div id="user_adata">
							<span id="user_nick">{{ $_SESSION['userdata']['nick'] }}</span>
							{% if $_SESSION['userdata']['id'] == -1 %}
							<span id="user_id">Без профиля</span>
							{% else %}
							<span id="user_id">id{{ $_SESSION['userdata']['id'] }}</span>
							{% endif %}
						</div>
						<a href="/auth/logout" id="user_logout"><i class="chat_icon_off"></i></a>
						<hr />
						<div class="item dropdown">
							<i class="chat_icon_settings"></i> | language |
							<div class="dropdown_container">
								{% for $lang_code, $lang_name in self::get_languages() %}
								<span class="item" onclick="change_lang('{{ $lang_code }}')">{{ $lang_name }}</span>
								{% endfor %}
							</div>
						</div>
						<hr />
						<div id="chat_color" class="item">
							<span style="background-color: #c8d5e6;"></span>
							<span style="background-color: #e2d1e4;"></span>
							<span style="background-color: #0183c1;"></span>
							<span style="background-color: #cb4383;"></span>
							| chat_color |
						</div>
						<hr />
						<a class="item" href="javascript:void(0);" load-modal="about_dev"><i class="chat_icon_settings"></i> | about_developer |</a>
					</div>
				</div>
				<a href="/auth/logout" style="font-weight: 500; color: #a2a2a2;" class="item">| logout |</a>
			</div>
		</div>
	{% endif %}