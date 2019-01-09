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
		{# <link rel="stylesheet" href="/public/css/a.css" /> #}
	</head>
	<body>
		{% if isset($_SESSION['userdata']) %}
		<div id="header">
			<img id="logo" src="/public/img/white_logo.png" />
			<div id="search">
				<input type="text" placeholder="| search |" />
				<i class="chat_icon_search"></i>
			</div>
			<div id="menu">
				<div>
					<span onclick="load_modal('rules')" class="item">| rules |</span>
					<span class="item">
						<i class="chat_icon_premium"></i>
						&nbsp; Premium
					</span>
					{% if $_SESSION['userdata']['access'] == 'admin' %}
					<a href="/admin" class="item">| adminpanel |</a>
					{% endif %}
					<span class="item">| report |</span>
				</div>
			</div>
			<span class="separator"></span>
			<div id="balance">
				<i class="chat_icon_wallet"></i>
				| balance_text | {{ $_SESSION['userdata']['credits'] }}
				<i class="chat_icon_coin"></i>
			</div>
			<div>
				<span class="item" tooltip="| tool_alert |"><i class="chat_icon_alert"></i></span>
				<span class="item" tooltip="| tool_friends |"><i class="chat_icon_message"></i></span>
			</div>
			<div class="user_block dropdown">
				<span class="name">{{ $_SESSION['userdata']['nick'] }}</span>
				<img src="/public/avatars/id{{ $_SESSION['userdata']['id'] }}.png" />
				<div class="dropdown_container">
					<img id="user_cover" src="/public/covers/id{{ $_SESSION['userdata']['id'] }}.png?s" />
					<img id="user_avatar" src="/public/avatars/id{{ $_SESSION['userdata']['id'] }}.png" />
					<div id="user_adata">
						<span id="user_nick">{{ $_SESSION['userdata']['nick'] }}</span>
						{% if $_SESSION['userdata']['id'] == -1 %}
						<span id="user_id">Без профиля</span>
						{% else %}
						<span id="user_id">id{{ $_SESSION['userdata']['id'] }}</span>
						{% endif %}
					</div>
					<a href="/modules/Auth/logout" id="user_logout"><i class="chat_icon_off"></i></a>
					<hr />
					<a class="item" onclick="open_profile({{ $_SESSION['userdata']['id'] }})"><i class="chat_icon_user"></i> | my_page |</a>
					<a class="item" href="javascript:void(0);" onclick="load_modal('help_main', '/help')"><i class="chat_icon_group"></i> | chat_help |</a>
					<a class="item" href="/"><i class="chat_icon_settings"></i> | security |</a>
					<div class="item dropdown">
						<i class="chat_icon_settings"></i> | language |
						<div class="dropdown_container">
							{% for $lang_code, $lang_name in self::get_languages() %}
							<span class="item" onclick="change_lang('{{ $lang_code }}')">
								{# <img src="/public/img/flags/{{ strtoupper($lang_code) }}.gif" /> #}
								{{ $lang_name }}
							</span>
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
					<a class="item" href="javascript:void(0);" onclick="load_modal('about_dev')"><i class="chat_icon_settings"></i> | about_developer |</a>
				</div>
			</div>
		</div>
	{% endif %}