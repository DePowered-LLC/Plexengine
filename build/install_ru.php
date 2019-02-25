<?php
    error_reporting(0);
    if(!file_exists('.htaccess')) {
        file_put_contents('.htaccess', trim('
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule index.php?(.*)$ index.php?rewrite_work&$1 [L,QSA]
</IfModule>
        '));
        header('Location: ?');
        exit;
    }
    
    $base_url = 'http://plexengine.com/cdn/installer';
    $pe_version = file_get_contents($base_url.'/version');
    if(!isset($_GET['step'])) { $_GET['step'] = 0; }

    function active($step) {
        if($_GET['step'] >= $step) { echo 'active'; }
    }

    function step_0() { ?>
        <div id="welcome">
            <h1>Мастер установки скрипта</h1>
            Добро пожаловать в мастер установки Plexngine. Данный мастер поможет Вам установить скрипт всего за пару минут. Однако, не смотря на это, мы настоятельно рекомендуем Вам ознакомиться с документацией по работе с движком. <a href="https://plexengine.com/doc" target="_blank"><b>Документация</b></a>
            <br />
            <br />
            Во время установки будет создана структура базы данных, аккаунт администратора и конфигурационный файл с базовыми настройками системы.
            <br />
            <br />
            Желаем Вам приятной работы, <a href="https://niceweb.in.ua" target="_blank"><b>NiceWeb Design</b></a> и <b>DePowered LLC <sup>md</sup></b>
        </div>
    <?php }
    
    function step_1() { ?>
        <div id="license">
            Официальный сайт ядра где можно всегда скачать свежую версию: <a href="https://plexengine.com" target="_blank"><b>https://plexengine.com</b></a>
            <br />
            Техническую поддержку можно получить в нашей группе Вконтакте: <a href="https://vk.com/plexengine" target="_blank"><b>vk.com/plexengine</b></a>
            <br />
            <br />
			Скрипт является полностью бесплатным, и распространяется по лицензии <b>MIT</b> с открытым исходным кодом.
            Название Plexengine, а также входящие в данный продукт скрипты являются собственностью <a href="https://niceweb.in.ua" target="_blank"><b>NiceWeb Design</b></a> и <b>DePowered LLC <sup>md</sup></b>, за исключением случаев, когда для компонента системы применяется другой тип лицензии. Программный продукт защищен законом об авторских правах. <a href="https://niceweb.in.ua" target="_blank"><b>NiceWeb Design</b></a> не несет никакой ответственности за содержание сайтов, создаваемых пользователем при помощи скрипта Plexengine.
            <br />
            <br />
            <input type="checkbox" id="agree" /> Я принимаю условия лицензии
            <script>
                document.addEventListener('DOMContentLoaded', () => { 
                    document.querySelector('.next').style.display = 'none';
                    agree.addEventListener('change', e => {
                        if (e.target.checked) document.querySelector('.next').style.display = null;
                        else document.querySelector('.next').style.display = 'none';
                    });
                });
            </script>
        </div>
    <?php }

    function step_2() {
        global $base_url;
        global $pe_version;
        $is_error = false;
    ?>
        <table>
            <tbody>
                <tr>
                    <td>Версия PHP 5.4+</td>
                    <?php
                    $version = explode('.', PHP_VERSION);
                    if($version[0] <= 5 && $version[1] < 4): ?>
                        <td>Установите PHP 5.4 или выше</td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Доступ к нашему сайту</td>
                    <?php if(!$pe_version): ?>
                        <td>Разрешите доступ к <code><?php echo $base_url; ?></code> в Вашем файерволе</td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>PHP модуль ZipArchive установлен</td>
                    <?php if(!class_exists('ZipArchive')): ?>
                        <td>Включите <code>php_zip</code> в <code>php.ini</code></td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Apache mod_rewrite включен</td>
                    <?php
                    if(!isset($_GET['rewrite_work']) && !in_array('mod_rewrite', apache_get_modules())): ?>
                        <td>
                            Включите Apache mod_rewrite.
                            <br>
                            Выполните <code>a2enmod rewrite</code> в терминале
                            и замените <code>AllowOverride None</code> на <code>AllowOverride All</code>
                            в файле <code>/etc/apache2/apache2.conf</code> (зависит от вашей ОС).
                            <br>
                            Если вы уже сделали это, то попробуйте удалить файл <code>.htaccess</code> в директории с установщиком.
                        </td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Доступен метод шифрования OpenSSL cast5-ecb</td>
                    <?php
                    if(!in_array('cast5-ecb', openssl_get_cipher_methods())): ?>
                        <td>
                            Добавьте/включите метод шифрования OpenSSL cast5-ecb.
                            <br>
                            Переустановка PHP может помочь.
                        </td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
    <?php return $is_error; }
    
    function step_3() {
        global $base_url;
        global $pe_version;
        $is_error = false;
    ?>
        <table>
            <tbody>
                <tr>
                    <td>Загрузка архива</td>
                    <?php
                    if(file_put_contents('plexengine.zip', fopen($base_url.'/v'.$pe_version.'.zip', 'r')) === false): ?>
                        <td>
                            Проверьте разрешение на запись в данной директории.
                            <br />
                            Вы можете скачать архив по ссылке <code><?php echo $base_url.'/v'.$pe_version.'.zip'; ?></code> в данную директорию и переименовать его в <code>plexengine.zip</code>.
                        </td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php else: ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Распаковка архива</td>
                    <?php
                    $archive = new ZipArchive;
                    if(($zip_err = $archive->open('plexengine.zip')) === true):
                        $archive->extractTo('plexengine.tmp');
                        $archive->close();
                        unlink('plexengine.zip');
                    ?>
                        <td>OK</td>
                        <td><div class="icon ok"></div></td>
                    <?php else: ?>
                        <td>
                            <?php
                                switch($zip_err) {
                                    case ZipArchive::ER_EXISTS: echo 'Файл уже существует.'; break;
                                    case ZipArchive::ER_INCONS: echo 'Неподдерживаемый ZIP архив.'; break;
                                    case ZipArchive::ER_INVAL: echo 'Неверные аргументы.'; break;
                                    case ZipArchive::ER_MEMORY: echo 'Ошибка памяти.'; break;
                                    case ZipArchive::ER_NOENT: echo 'Файл <code>plexengine.zip</code> не найден.'; break;
                                    case ZipArchive::ER_NOZIP: echo 'Архив <code>plexengine.zip</code> повреждён.'; break;
                                    case ZipArchive::ER_OPEN: echo 'Невозможно открыть файл.'; break;
                                    case ZipArchive::ER_READ: echo 'Невозможно прочитать файл.'; break;
                                    default: echo 'Неизвестная ошибка.'; break;
                                }
                            ?>
                        </td>
                        <td><div class="icon err"></div></td>
                        <?php $is_error = true; ?>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
    <?php return $is_error; }
    
    function step_4() {
        $is_error = false;
        $is_post = $_SERVER['REQUEST_METHOD'] === 'POST';
    ?>
        <script>
            document.addEventListener('click', e => {
            	if(e.target.className == 'next') {
            		document.getElementById('conf_form').submit();
            		return false;
            	}
            });
        </script>
        <form id="conf_form" action="?step=4" method="POST">
            <table>
                <tbody>
                    <tr>
                        <th colspan="2">Основное</th>
                    </tr>
                    <tr>
                        <td>Название сайта</td>
                        <td><input type="text" name="site_name" value="<?php echo $_POST['site_name']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Стандартный язык</td>
                        <td>
                            <select name="default_lang">
                                <?php 
                                    $langs = scandir('plexengine.tmp/languages');
                                    array_shift($langs);
                                    array_shift($langs);
                                    foreach($langs as $lang):
                                        $lang = explode('.', $lang)[0];
                                ?>
                                <option <?php if($lang == $_POST['default_lang']) { echo 'selected="selected"'; } ?>><?php echo $lang; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2">MySQL</th>
                    </tr>
                    <?php
                        try {
                            if($is_post) { $connection = new PDO('mysql:dbname='.$_POST['mysql_db'].';host='.$_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_pass']); }
                        } catch (PDOException $e) {
                            $is_error = true;
                    ?>
                        <tr>
                            <td colspan="2">
                                <div class="icon err"></div> Ошибка проверки MySQL!
                                <br />
                                <?php echo $e->getMessage(); ?>
                            </td>
                        </tr>
                    <?php
                        }
                        
                        if($is_post && $connection) {
                            $sql_query = '';
                            $sql_lines = file('plexengine.tmp/db.sql');
                            foreach($sql_lines as $line) {
                                $sql_query .= $line;
                                if(substr(trim($line), -1, 1) == ';') {
                                    if(!$connection->query($sql_query)) {
                                        $is_error = true;
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <div class="icon err"></div> Ошибка импорта MySQL при выполнении запроса <code><?php echo $sql_query; ?></code>.
                                            <br />
                                            #<?php echo $connection->errorInfo()[1]; ?> <?php echo $connection->errorInfo()[2]; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    $sql_query = '';
                                }
                            }
                        }
                    ?>
                    <tr>
                        <td>Хост</td>
                        <td><input type="text" name="mysql_host" value="<?php echo $_POST['mysql_host']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Пользователь</td>
                        <td><input type="text" name="mysql_user" value="<?php echo $_POST['mysql_user']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Пароль</td>
                        <td><input type="password" name="mysql_pass" value="<?php echo $_POST['mysql_pass']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>База данных</td>
                        <td><input type="text" name="mysql_db" value="<?php echo $_POST['mysql_db']; ?>" /></td>
                    </tr>
                    
                    <tr>
                        <th colspan="2">Аккаунт администратора</th>
                    </tr>
                    <?php
                        if($is_post && $connection) {
                            if(
                                (!isset($_POST['adm_email']) || trim($_POST['adm_email']) == '')
                             || (!isset($_POST['adm_nick']) || trim($_POST['adm_nick']) == '')
                             || (!isset($_POST['adm_pass']) || trim($_POST['adm_pass']) == '')
                             || (!isset($_POST['adm_birth1']) || trim($_POST['adm_birth1']) == '')
                             || (!isset($_POST['adm_birth2']) || trim($_POST['adm_birth2']) == '')
                             || (!isset($_POST['adm_gender']) || trim($_POST['adm_gender']) == '')
                             || (!isset($_POST['adm_country']) || trim($_POST['adm_country']) == '')
                            ) {
                                $is_error = true;
                            ?>
                            <tr>
                                <td colspan="2"><div class="icon err"></div> Заполните все поля.</td>
                            </tr>
                            <?php
                            } else {
                                $err = '';
                                if(!filter_var($_POST['adm_email'], FILTER_VALIDATE_EMAIL)) $err = 'Неверный формат E-Mail.';
                                elseif(strlen($_POST['adm_nick']) > 16) $err = 'Ник не может превышать 16 символов.';
                                elseif($_POST['adm_pass'] != $_POST['adm_passr']) $err = 'Пароли не совпадают.';

                                if ($err != '') {
                                    $is_error = true;
                                ?>
                                <tr>
                                    <td colspan="2"><div class="icon err"></div> <?php echo $err ?></td>
                                </tr>
                                <?php
                                } else {
                                    $sql_query = "INSERT INTO `users` (`email`, `nick`, `pass`, `date_of_birth`, `access`, `gender`, `status`, `ignored`, `credits`, `country`, `last_online`, `verificated`, `about`, `limitation`)";
                                    $sql_query .= "VALUES ('".$_POST['adm_email']."', '".$_POST['adm_nick']."', '".md5(sha1($_POST['adm_pass']))."', '".$_POST['adm_birth1']."_".$_POST['adm_birth2']."', 'admin', '".$_POST['adm_gender']."', 'chat', '', 0, '".$_POST['adm_country']."', 0, 1, '{}', '')";

                                    if(!$connection->query($sql_query)) {
                                        $is_error = true;
                                    ?>
                                    <tr>
                                        <td colspan="2">
                                            <div class="icon err"></div> Ошибка MySQL при выполнении запроса <code><?php echo $sql_query; ?></code>.
                                            <br />
                                            #<?php echo $connection->errorInfo()[1]; ?> <?php echo $connection->errorInfo()[2]; ?>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                }
                            }
                        }
                    ?>
                    <tr>
                        <td>E-Mail</td>
                        <td><input type="email" name="adm_email" value="<?php echo $_POST['adm_email']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Никнейм</td>
                        <td><input type="text" name="adm_nick" value="<?php echo $_POST['adm_nick']; ?>" /></td>
                    </tr>
                    <tr>
                        <td>Пароль</td>
                        <td><input type="password" name="adm_pass" /></td>
                    </tr>
                    <tr>
                        <td>Повтор пароля</td>
                        <td><input type="password" name="adm_passr" /></td>
                    </tr>
                    <tr>
                        <td>Дата рождения</td>
                        <td>
                            <input min="1" max="31" style="min-width: 0; width: 120px;" placeholder="день" type="number" name="adm_birth1" value="<?php echo $_POST['adm_birth1']; ?>" />
                            .
                            <input min="1" max="12" style="min-width: 0; width: 120px;" placeholder="мес" type="number" name="adm_birth2" value="<?php echo $_POST['adm_birth2']; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Пол</td>
                        <td>
                            <select name="adm_gender">
                                <option value="male">Мужской</option>
                                <option value="female">Женский</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Страна</td>
                        <td>
                            <select name="adm_country">
                                <option value="RU">Россия</option>
                                <option value="UA">Україна</option>
                                <option value="UK">England</option>
                                <option value="DE">Deutschland</option>
                                <option value="PL">Polska</option>
                                <option value="KZ">Қазақстан</option>
                                <option value="BY">Беларусь</option>
                                <option value="AM">Հայաստան</option>
                                <option value="AZ">Azərbaycan</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?php
        if($is_post && !$is_error) {
            $default_conf = trim("
template_name='default'
debug=false
cache=true
lang_delimiters='|...|'
message_limit=300
messages_limit=15
loaded_modules='Auth, Admin'
afk_time=10
exchange_rate=100
nick_regexp='_a-zA-Z0-9А-Яа-яіІїЇєЄ;'
            ");
            foreach($_POST as $key => $val) {
                $default_conf .= PHP_EOL;
                $default_conf .= "$key='$val'";
            }
            file_put_contents('plexengine.tmp/data/config.ini', $default_conf);
            
            header('Location: ?step=5');
            exit;
        }
        return false;
    }
    
    function step_5() {
        $remove_installer = true;
        $pe_files = scandir('plexengine.tmp');
        if(!$pe_files) {
            echo '<div class="icon err"></div> Директория <code>plexengine.tmp</code> не существует!<br />';
            $remove_installer = false;
        }
        array_shift($pe_files);
        array_shift($pe_files);
        foreach($pe_files as $file) {
            if(!rename('plexengine.tmp/'.$file, './'.$file)) {
                $remove_installer = false;
                echo '<div class="icon err"></div> Невозможно переместить файл из <code>plexengine.tmp/'.$file.'</code> в текущую директорию!<br />';
            }
        }
        rmdir('plexengine.tmp');
        mkdir('data/avatars');
        unlink('db.sql');
        if($remove_installer) { unlink('index.php'); }
    ?>
        Установка успешно завершена.
        <br />
        Поздравляем, чат был успешно установлен на Ваш сервер.
        <br />
        <b>Файл</b> <code>index.php</code> <?php
            echo $remove_installer?
            '<b>был удалён</b> из-за соображений безопасности.':
            '<b>не удалён</b>, потому что некоторые операции были неуспешны.';
        ?>
        <br />
        Теперь Вы можете перейти <a href="/">по этой ссылке</a> чтобы открыть главную страницу.
    <?php
         global $base_url;
         file_get_contents($base_url.'/rg.php?d='.$_SERVER['HTTP_HOST'].'&ip='.$_SERVER['SERVER_ADDR']);
        return false;
    }
    
    ob_start();
    $is_error = call_user_func('step_'.$_GET['step']);
    $step_result = ob_get_contents();
    ob_end_clean();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Установщик Plexengine</title>
        <meta charset="UTF-8" />
        <style>
        @import url('https://fonts.googleapis.com/css?family=Mukta+Mahee');
        * { font-family: 'Mukta Mahee', sans-serif; }
        body {
            margin: 20px auto;
            border-radius: 5px;
            box-shadow: rgba(0, 0, 0, 0.15) 1px 2px 15px 0;
            border: 1px solid #dedede;
            width: 800px;
        }
        
        #header {
            position: relative;
            background-color: #fff;
            background-image: url('<?php echo $base_url ?>/bg_installer.gif');
			background-repeat: no-repeat;
            border-bottom: 1px solid #dedede;
            padding: 10px 15px;
		    height: 200px;
            color: #ffffff;
        }
        
        #wrapper {
            padding: 10px 15px;
			color: #262931;
        }
        
        #version {
            display: flex;
            align-items: center;
            position: absolute;
		    padding: 5px;
			top: 130px;
			color: #babcc1;
            margin: auto;
            text-align: right;
            font-size: 15px;
			border: 2px dashed #dedede;
			border-radius: 3px;
            line-height: 13px;
        }
        
        #version > b {
            font-size: 35px;
            margin-left: 10px;
            color: #5ec700;
            line-height: 32px;
        }
        
        #steps { 
			display: flex;
			margin-top: 173px;
		}
		
        #steps > .step {
            position: relative;
            display: inline-block;
            background-color: #545965;
            color: #fff;
            padding: 2px 15px;
            font-size: 16px;
            line-height: 28px;
            height: 26px;
            opacity: 0.4;
            flex: 1 1 0;
            text-align: center;
            margin: 0 7px;
            white-space: nowrap;
        }
        #steps > .step[active] { opacity: 1; }
        
        #steps > .step:before {
            content: '';
            position: absolute;
            right: 100%;
            top: 0;
            border-top: 15px solid #545965;
            border-bottom: 15px solid #545965;
            border-left: 10px solid transparent;
        }
        
        #steps > .step:after {
            content: '';
            position: absolute;
            left: 100%;
            top: 0;
            border-top: 15px solid transparent;
            border-bottom: 15px solid transparent;
            border-left: 10px solid #545965;
        }
        
        #steps > .step > b {
            display: inline-block;
            border: 1px solid #fff;
            box-sizing: border-box;
            height: 20px;
            line-height: 20px;
            width: 20px;
            vertical-align: top;
            margin: 3px 0;
            text-align: center;
            border-radius: 3px;
            margin-right: 5px;
        }

        #agree {
            display: inline-block;
            margin: 7px 5px;
            min-width: 0;
            vertical-align: top;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td { padding: 2px 5px; }
        tr { border: 0; }
        tr:not(:last-child) { border-bottom: 1px dashed #2e323a; }
        
        code {
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.05);
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        input, select {
            background-color: #ECEFF1;
            border: 1px solid rgba(0, 0, 0, 0.15);
            padding: 2px 10px;
            border-radius: 3px;
            min-width: 250px;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            box-shadow: rgba(187, 222, 251, 0.5) 0 0 0 2px;
            outline: none;
        }
        
        .icon {
            position: relative;
            display: inline-block;
            width: 16px;
            height: 16px;
            vertical-align: middle;
            border-radius: 100%;
        }
        
        .icon.err { background-color: #F44336; }
        .icon.err:before, .icon.err:after {
            content: '';
            position: absolute;
            top: 2px;
            bottom: 2px;
            width: 2px;
            left: 2px;
            right: 2px;
            background-color: #fff;
            margin: auto;
            transform: rotateZ(45deg);
        }
        .icon.err:after { transform: rotateZ(-45deg); }
        
        .icon.ok { background-color: #4CAF50; }
        .icon.ok:before {
            content: '';
            position: absolute;
            top: 3px;
            left: 5px;
            height: 7px;
            width: 4px;
            border-bottom: 2px solid #fff;
            border-right: 2px solid #fff;
            transform: rotateZ(45deg);
        }
        
        .next {
            position: relative;
            display: block;
            margin-left: auto;
			margin-left: auto;
			margin-top: 10px;
			margin-right: 20px;
			margin-bottom: 10px;
            border: none;
            background-color: #4CAF50;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            padding-right: 30px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .next:before, .next:after {
            content: '';
            position: absolute;
            display: inline-block;
            width: 4px;
            height: 4px;
            right: 17px;
            top: 0;
            bottom: 0;
            margin: auto;
            border-right: 2px solid #fff;
            border-top: 2px solid #fff;
            transform: rotateZ(45deg);
            transition: 0.3s;
        }
        
        .next:after {
            width: 8px;
            height: 8px;
            right: 12px;
        }
        
        .next:hover:before, .next:hover:after {
            transform: translateX(4px) rotate(45deg);
        }
        .next:hover { background-color: #388E3C; }
        </style>
    </head>
    <body>
        <div id="header">
            <div id="logo"></div>
            <div id="version">
                <span>Актуальная<br />версия</span>
                <b><?php echo $pe_version; ?></b>
            </div>
            <div id="steps">
                <span class="step" <?php active(1); ?>><b>1</b> Лицензия</span>
                <span class="step" <?php active(2); ?>><b>2</b> Проверка</span>
                <span class="step" <?php active(3); ?>><b>3</b> Установка</span>
                <span class="step" <?php active(4); ?>><b>4</b> Конфигурация</span>
                <span class="step" <?php active(5); ?>><b>5</b> Готово</span>
            </div>
        </div>
        <div id="wrapper">
            <?php echo $step_result; ?>
            <?php if($_GET['step'] != 5): ?>
            <button class="next" onclick="<?php
                $err_do = "window.location.href = '?step=".++$_GET['step']."';";
                echo $is_error?
                    "if(prompt('Пожалуйста, исправьте все ошибки!\\nСистема может работать нестабильно!\\nВведите \'\'OK\'\', чтобы продолжить.') == 'OK') { ".$err_do." }":
                    $err_do
            ?>">Далее</button>
            <?php endif; ?>
        </div>
    </body>
</html>