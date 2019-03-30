<!DOCTYPE html>
<html>
	<head>
		<title>{{ $_CONFIG['site_name'] }}</title>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="/public/css/admin.css" />
		<script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script defer src="/public/js/admin.js"></script>

		<svg style="display: none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
			<defs>
				<symbol id="truncateIcon" viewBox="0 0 16 16">
					<path fill="currentColor" d="M14.5,1h-3.8l-0.3-0.6C10.3,0.2,10.1,0,9.8,0H6.2C5.9,0,5.7,0.2,5.5,0.4L5.3,1H1.5C1.2,1,1,1.2,1,1.5v1C1,2.8,1.2,3,1.5,3h13C14.8,3,15,2.8,15,2.5v-1C15,1.2,14.8,1,14.5,1z M2.7,14.6c0,0.8,0.7,1.4,1.5,1.4h7.7c0.8,0,1.4-0.6,1.5-1.4L14,4H2L2.7,14.6z" />
				</symbol>
			</defs>
		</svg>
	</head>
	<body>
		<div id="header">
			<img id="logo" src="/public/img/logo.png" />
			<div id="user_block">
				<img src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
				<span class="name">{{ $_SESSION['userdata']['nick'] }}</span>
			</div>
			<div id="menu">
				<a href="/admin" class="item">| adminpanel |</a>
				{% for $path, $name in $vars->menus %}
					<a href="/admin/{{ $path }}" class="item">{{ $name }}</a>
				{% endfor %}
				<a href="javascript:window.close()" class="item">Выйти</a>
			</div>
		</div>
		<div id="wrapper">