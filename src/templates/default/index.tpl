{% if isset($_SESSION['userdata']) %}
	{% include 'common/header' %}
	{% include 'index_authed' %}
	{% include 'common/footer' %}
{% else %}
{% include 'common/header' %}
<style>
	body { background-color: #fff; }
	.box { border: 0; }
	
	#index_wrapper {
		margin: 0 170px;
		width: auto;
		margin-top: 70px;
	}
	
	#login_form {
		width: 100%;
		padding: 0 40px;
		box-sizing: border-box;
		text-align: center;
	}
	
	.btn.bg_purple { text-align: center; }
	
	.link {
		margin: 0;
		line-height: 36px;
		font-size: 15px;
	}
	
	h2 {
		display: block;
		font-size: 25px;
		margin: 5px 0;
    	font-weight: 500;
	}
	
	#register_form { margin: 20px 0 45px 0; }
</style>
<div id="index_wrapper">
	<span id="lang_change">
		<div class="dropdown">
				<span class="name link" style="line-height: 25px;">| language_name |</span>
				<div class="dropdown_container">
					{% for $lang_code, $lang_name in self::get_languages() %}
					<span class="item" onclick="change_lang('{{ $lang_code }}')">{{ $lang_name }}</span>
					{% endfor %}
				</div>
			</div>
	</span>
	<div style="display: inline-block; width: 38%;">
		<img id="big_logo" src="/public/img/logo.png" />
		<div id="login_form" class="box">
			<span class="title">| enter_to_chat |</span>
			<span class="post_title">| enter_auth_data |</span>
			<br />
			<h4 class="text_red" style="margin: 3px 0;">&nbsp;</h4>
			<input name="login" type="text" placeholder="| login |" />
			<div class="input_text">
				<input name="pass" type="password" placeholder="| password |" />
			</div>
			<div class="row" style="font-size: 0;">
				<span class="col-9 link">| forgot_password |</span>
				<button class="col-3 btn bg_purple" onclick="login()">| sign_in |</button>
			</div>
			<br />
			<button class="row btn" open-modal="guest">| sign_in_guest |</button>
		</div>
		<br />
	</div>
	<div style="display: inline-block; text-align: center; width: calc(62% - 50px); margin-left: 45px; vertical-align: top;">
		<img src="/public/img/main_page.png" style="display: block; width: calc(100% - 40px); margin: auto; margin-bottom: 15px;" />
		<h2><?php echo DB::count('users', 'id'); ?> | registered_in_chat |</h2>
		<h2>| join_and_you |</h2>
		<br />
		<button style="width: 200px;" class="btn bg_green" open-modal="register">| register |</button>
	</div>
	<br />
	<span style="font-size: 13px;">2018 &copy; Powered on PlexEngine / Developed by DePowered LLC</span>
	<br />
	<br />
</div>

<!-- Registration modal -->
<div class="modal_wrapper" modal-name="register">
	<div class="modal">
		<div class="title">
			| instant_registration |
			<span class="close"></span>
		</div>
		<div class="content row">

			<div id="register_form" class="col-6">
				<h4 class="text_red"></h4>
				<input type="text" name="nick" placeholder="| your_nick |" />
				<input type="password" name="pass" placeholder="| password |" />
				<input type="password" name="passr" placeholder="| repeate_password |" />
				<input type="email" name="email" placeholder="E-Mail" />
				<div class="select">
					<input type="hidden" name="country">
					<span class="selected">| country |</span>
					<div class="options">
						<span class="option" value="RU">Россия</span>
						<span class="option" value="UA">Україна</span>
						<span class="option" value="UK">England</span>
						<span class="option" value="DE">Deutschland</span>
						<span class="option" value="PL">Polska</span>
						<span class="option" value="KZ">Қазақстан</span>
						<span class="option" value="BY">Беларусь</span>
						<span class="option" value="AM">Հայաստան</span>
						<span class="option" value="AZ">Azərbaycan</span>
					</div>
				</div>
				<div class="select">
					<input type="hidden" name="gender">
					<span class="selected">| gender |</span>
					<div class="options">
						<span class="option" value="male">| male |</span>
						<span class="option" value="female">| female |</span>
					</div>
				</div>
				<span class="mute_heading">| date_of_birth |</span>
				<div class="row">
					<div class="select select_date" style="margin-left: 0;">
						<input type="hidden" name="birth_day" />
						<span class="selected">| day |</span>
						<div class="options">
							<span class="option">1</span>
							<span class="option">2</span>
							<span class="option">3</span>
							<span class="option">4</span>
							<span class="option">5</span>
							<span class="option">6</span>
							<span class="option">7</span>
							<span class="option">8</span>
							<span class="option">9</span>
							<span class="option">10</span>
							<span class="option">11</span>
							<span class="option">12</span>
							<span class="option">13</span>
							<span class="option">14</span>
							<span class="option">15</span>
							<span class="option">16</span>
							<span class="option">17</span>
							<span class="option">18</span>
							<span class="option">19</span>
							<span class="option">20</span>
							<span class="option">21</span>
							<span class="option">22</span>
							<span class="option">23</span>
							<span class="option">24</span>
							<span class="option">25</span>
							<span class="option">26</span>
							<span class="option">27</span>
							<span class="option">28</span>
							<span class="option">29</span>
							<span class="option">30</span>
							<span class="option">31</span>
						</div>
					</div>
					<div class="select select_date">
						<input type="hidden" name="birth_month" />
						<span class="selected">| month |</span>
						<div class="options">
							<span class="option" value="1">| month_1 |</span>
							<span class="option" value="2">| month_2 |</span>
							<span class="option" value="3">| month_3 |</span>
							<span class="option" value="4">| month_4 |</span>
							<span class="option" value="5">| month_5 |</span>
							<span class="option" value="6">| month_6 |</span>
							<span class="option" value="7">| month_7 |</span>
							<span class="option" value="8">| month_8 |</span>
							<span class="option" value="9">| month_9 |</span>
							<span class="option" value="10">| month_10 |</span>
							<span class="option" value="11">| month_11 |</span>
							<span class="option" value="12">| month_12 |</span>
						</div>
					</div>
					<div class="select select_date">
						<input type="hidden" name="birth_year" />
						<span class="selected">| year |</span>
						<div class="options"></div>
					</div>
				</div>
				<button onclick="register()" class="col-12 btn bg_green">| register |</button>
			</div>

		</div>
	</div>
</div>

<!-- Guest login modal -->
<div class="modal_wrapper" modal-name="guest">
	<div class="modal">
		<div class="title">
			| sign_in_guest |
			<span class="close"></span>
		</div>
		<div class="content row">
			<br />
			<div id="guest_form" class="col-10" style="font-size: 0;">
				<h4 class="text_red" style="font-size: 16px; margin: 3px 0;"></h4>
				<div class="col-6" style="width: 185px !important; margin-top: 10px; padding-right: 40px; box-sizing: content-box;">
					<div style="position: relative; cursor: pointer;" onclick="captcha.src = captcha.src.split('#')[0]+'#'+Math.random()" tooltip="Нажмите, чтобы обновить каптчу">
						<img id="captcha" src="/modules/Auth/captcha.png" style="width: 185px; image-rendering: -webkit-optimize-contrast;" />
						<i class="chat_icon_update" style="position: absolute; right: 5px; bottom: 5px;"></i>
					</div>
					<input type="text" name="captcha" maxlength="5" placeholder="Введите код с картинки" />
				</div>
				<div class="col-6">
					<input type="text" name="nick" placeholder="| your_nick |" />
					<div class="select">
						<input type="hidden" name="country">
						<span class="selected">| country |</span>
						<div class="options">
							<span class="option" value="RU">Россия</span>
							<span class="option" value="UA">Україна</span>
							<span class="option" value="UK">England</span>
							<span class="option" value="DE">Deutschland</span>
							<span class="option" value="PL">Polska</span>
							<span class="option" value="KZ">Қазақстан</span>
							<span class="option" value="BY">Беларусь</span>
							<span class="option" value="AM">Հայաստան</span>
							<span class="option" value="AZ">Azərbaycan</span>
						</div>
					</div>
					<button onclick="guest()" class="row btn bg_green">| sign_in |</button>
				</div>
			</div>
			<br />
			<br />
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

{% include 'common/footer' %}
<script>
	if (sessionStorage.getItem('limitation')) {
		open_modal('limitation');
		var data = sessionStorage.getItem('limitation').split(';');
		switch (data[0]) {
			case 'kick':
				$('#lmodal [info]').html('| kick_info |');
				break;
			case 'ban':
				var date = new Date(data[2] * 1000);
				var timestamp = '';
				timestamp += date.getDate().toString().padStart(2, 0);
				timestamp += '.' + (date.getMonth() + 1).toString().padStart(2, 0);
				timestamp += '.' + date.getFullYear();

				timestamp += ' ' + date.getHours().toString().padStart(2, 0);
				timestamp += ':' + date.getMinutes().toString().padStart(2, 0);
				timestamp += ':' + date.getSeconds().toString().padStart(2, 0);
				$('#lmodal [info]').html('| ban_info |'.replace('{time}', timestamp));
				break;
		}
		$('#lmodal [reason]').html(data[1]);
		sessionStorage.removeItem('limitation');
	}

	let year_options = $('[name="birth_year"]').siblings('.options');
	let ynum = 0;
	let tyear = (new Date()).getFullYear();
	while(ynum < 80) {
		year_options.append('<span class="option">'+(tyear-ynum)+'</span>');
		ynum++;
	}
	
	function login() {
		$('#login_form [onclick~="login()"]').addClass('loading');
		$.post('/modules/Auth/login', {
			login: $('#login_form [name="login"]').val(),
			pass: $('#login_form [name="pass"]').val()
		}, result => {
			switch(result) {
				case 'user_not_found':
					$('#login_form .text_red').html('| user_not_found |');
					break;
				case 'wrong_password':
					$('#login_form .text_red').html('| wrong_password |');
					break;
				case 'success':
					window.location.href = '/';
					break;
			}
			$('#login_form [onclick~="login()"]').removeClass('loading');
		});
	}
	
	function guest() {
		$('#guest_form .text_red').html('');
		$('#guest_form [onclick~="guest()"]').addClass('loading');		
		$.post('/modules/Auth/guest', {
			nick: $('#guest_form [name="nick"]').val(),
			country: $('#guest_form [name="country"]').val(),
			captcha: $('#guest_form [name="captcha"]').val()
		}, result => {
			switch(result) {
				case 'nick_empty':
					$('#guest_form .text_red').html('| enter_nick |');
					break;
				case 'nick_wrong_length':
					$('#guest_form .text_red').html('| long_nick |');
					break;
				case 'nick_used':
					$('#guest_form .text_red').html('| nick_used |');
					break;
				case 'country_empty':
					$('#guest_form .text_red').html('| no_country |');
					break;
				case 'captcha_empty':
				case 'captcha_not_valid':
					$('#guest_form .text_red').html('| wrong_captcha |');
					break;
				case 'success':
					document.cookie = 'tutorial=true';
					window.location.href = '/';
					break;
			}
			$('#guest_form [onclick~="guest()"]').removeClass('loading');
		});
	}
	
	function register() {
		$('#register_form [onclick~="register()"]').addClass('loading');
		$.post('/modules/Auth/register', {
			email: $('#register_form [name="email"]').val(),
			nick: $('#register_form [name="nick"]').val(),
			pass: $('#register_form [name="pass"]').val(),
			passr: $('#register_form [name="passr"]').val(),
			birth_day: $('#register_form [name="birth_day"]').val(),
			birth_month: $('#register_form [name="birth_month"]').val(),
			birth_year: $('#register_form [name="birth_year"]').val(),
			gender: $('#register_form [name="gender"]').val(),
			country: $('#register_form [name="country"]').val()
		}, result => {
			switch(result) {
				case 'fill_fields':
					$('#register_form .text_red').html('| fill_fields |');
					break;
				case 'invalid_email':
					$('#register_form .text_red').html('| invalid_email |');
					break;
				case 'user_email_exist':
					$('#register_form .text_red').html('| user_email_exist |');
					break;
				case 'user_nick_exist':
					$('#register_form .text_red').html('| user_nick_exist |');
					break;
				case 'long_nick':
					$('#register_form .text_red').html('| long_nick |');
					break;
				case 'passwords_doesnt_match':
					$('#register_form .text_red').html('| passwords_doesnt_match |');
					break;
				case 'success':
					document.cookie = 'tutorial=true';
					window.location.href = '/';
					break;
				default:
					document.write(result);
					break;
			}
			$('#register_form [onclick~="register()"]').removeClass('loading');
		});
	}
</script>
{% endif %}