<!DOCTYPE html>
<html>
	<head>
		<title>{{ $_CONFIG['site_name'] }}</title>
		<meta charset="UTF-8" />
		<link rel="stylesheet" href="/public/css/admin.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	</head>
	<body>
		<div id="header">
			<img id="logo" src="/public/img/logo.png" />
			<div id="user_block">
				<img src="/public/img/no_photo.png" />
				<span class="name">{{ $_SESSION['userdata']['nick'] }}</span>
			</div>
			<div id="menu">
				<a href="/admin" class="item">| adminpanel |</a>
			</div>
		</div>