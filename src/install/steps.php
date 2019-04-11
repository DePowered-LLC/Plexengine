<?php
namespace pe\engine;
use \PDO;
use \PDOException;

function set_error (&$is_error, $expr) {
    if (!$expr) $is_error = true;
    return $expr;
}

return [
    function ($ctx) {
        View::load('*steps.welcome', $ctx);
        return ['success'];
    },
    function () {
        View::load('*steps.license');
        return ['success'];
    },
    function () {
        $is_error = false;
        $mk_error = '';
        $mk_map = [
            CACHE => 777,
            UPLOADS.'/avatars' => 775,
            UPLOADS.'/covers' => 775
        ];

        foreach ($mk_map as $dir => $ch) {
            $chmod = intval('0'.$ch, 8);
            if (file_exists($dir)) {
                if (!chmod($dir, $chmod)) {
                    if (!$mk_error) $mk_error = 'Произошла ошибка во время структурирования:';
                    $mk_error .= '<br />'.str_replace(ROOT, '', $dir).' -> <b>'.$ch.'</b>';
                }
            } elseif (!mkdir($dir, $chmod)) {
                if (!$mk_error) $mk_error = 'Произошла ошибка во время структурирования:';
                $mk_error .= '<br />'.str_replace(ROOT, '', $dir).' -> <b>'.$ch.'</b>';
            }
        }

        View::load('*steps.checking', [
            'checklist' => [
                [
                    'name' => 'Версия PHP 5.4+',
                    'fail' => 'Установите PHP 5.4 или выше',
                    'result' => set_error($is_error, PHP_VERSION >= '5.4.0')
                ],
                [
                    'name' => 'PHP модуль ZipArchive установлен и включен',
                    'fail' => 'Включите <code>php_zip</code> в <code>php.ini</code>',
                    'result' => set_error($is_error, class_exists('ZipArchive'))
                ],
                [
                    'name' => 'PHP модуль PDO включен',
                    'fail' => 'Включите системный модуль PHP PDO.<br />Более подробно это описано в <a href="https://php.net/manual/ru/ref.pdo-mysql.php#ref.pdo-mysql.installation" target="_blank">официальной статье</a>.',
                    'result' => set_error($is_error, class_exists('PDO'))
                ],
                [
                    'name' => 'Проверка структуры скрипта',
                    'fail' => $mk_error,
                    'result' => set_error($is_error, !$mk_error)
                ]
            ]
        ]);
        return [$is_error ? 'error' : 'success'];
    },
    function () {
        $is_error = false;
        $is_done = false;

        $mysql_error = '';
        $admin_error = '';

        if ($_POST != []) {
            $connection = null;
            try {
                $connection = new PDO('mysql:dbname='.$_POST['cfg']['mysql_db'].';host='.$_POST['cfg']['mysql_host'], $_POST['cfg']['mysql_user'], $_POST['cfg']['mysql_pass']);
            } catch (PDOException $err) {
                $is_error = true;
                $mysql_error = 'Ошибка проверки соединения с сервером MySQL.<br />'.$err->getMessage();
            }

            if($connection) {
                $sql_query = '';
                $sql_lines = file(__DIR__.'/db.sql');
                foreach ($sql_lines as $line) {
                    $sql_query .= $line;
                    if (substr(trim($line), -1, 1) == ';') {
                        if (!$connection->query($sql_query)) {
                            $is_error = true;
                            $mysql_error .= 'Ошибка импорта MySQL при выполнении запроса <code>'.$sql_query.'</code>';
                            $mysql_error .= '<br />'.$connection->errorInfo()[1].' '.$connection->errorInfo()[2];
                        }
                        $sql_query = '';
                    }
                }

                if (
                    (!isset($_POST['admin']['email']) || trim($_POST['admin']['email']) == '')
                 || (!isset($_POST['admin']['nick']) || trim($_POST['admin']['nick']) == '')
                 || (!isset($_POST['admin']['pass']) || trim($_POST['admin']['pass']) == '')
                 || (!isset($_POST['admin']['birth']))
                 || (!isset($_POST['admin']['gender']) || trim($_POST['admin']['gender']) == '')
                 || (!isset($_POST['admin']['country']) || trim($_POST['admin']['country']) == '')
                ) $admin_error = 'Заполните все поля';
                elseif (!filter_var($_POST['admin']['email'], FILTER_VALIDATE_EMAIL)) $admin_error = 'Неверный формат E-Mail.';
                elseif (strlen($_POST['admin']['nick']) > 16) $admin_error = 'Ник не может превышать 16 символов.';
                elseif ($_POST['admin']['pass'] != $_POST['admin']['passr']) $admin_error = 'Пароли не совпадают.';
                elseif (!$mysql_error) {
                    $sql_query = "INSERT INTO `users` (`email`, `nick`, `pass`, `date_of_birth`, `access`, `gender`, `status`, `ignored`, `credits`, `country`, `last_online`, `verificated`, `about`, `limitation`)";
                    $sql_query .= "VALUES ('".$_POST['admin']['email']."', '".$_POST['admin']['nick']."', '".md5(sha1($_POST['admin']['pass']))."', '".$_POST['admin']['birth'][0]."_".$_POST['admin']['birth'][1]."_".$_POST['admin']['birth'][2]."', 'admin', '".$_POST['admin']['gender']."', 'chat', '', 0, '".$_POST['admin']['country']."', 0, 1, '{}', '')";

                    if(!$connection->query($sql_query)) {
                        $is_error = true;
                        $admin_error = '#'.$connection->errorInfo()[1].' '.$connection->errorInfo()[2];
                    } else {    
                        $config = trim("
template_name='default'
debug=false
cache=true
message_limit=300
messages_limit=15
enabled_modules='Rooms, Admin, System'
afk_time=10
exchange_rate=100
nick_regexp='_a-zA-Z0-9А-Яа-яіІїЇєЄ;'
                        ");
                        foreach ($_POST['cfg'] as $key => $val) $config .= PHP_EOL."$key='$val'";
                        file_put_contents(DATA.'/config.ini', $config);
                        $is_done = true;
                    }
                }
            }
        }

        $langs = scandir(LANG);
        array_shift($langs);
        array_shift($langs);
        foreach ($langs as $key => $lang) {
            $lang = explode('.', $lang)[0];
            $langs[$key] = [
                'value' => $lang,
                'placeholder' => $lang,
                'selected' => isset($_POST['cfg.default_lang']) ? $lang == $_POST['cfg.default_lang'] : false
            ];
        }

        View::load('*steps.config', [
            'groups' => [
                'Основное' => [
                    'cfg[site_name]' => ['name' => 'Название сайта'],
                    'cfg[default_lang]' => [
                        'name' => 'Стандартный язык',
                        'type' => 'select',
                        'options' => $langs
                    ]
                ],
                'MySQL' => [
                    [
                        'type' => 'status',
                        'fail' => $mysql_error,
                        'result' => !$mysql_error
                    ],
                    'cfg[mysql_host]' => ['name' => 'Хост'],
                    'cfg[mysql_user]' => ['name' => 'Пользователь'],
                    'cfg[mysql_pass]' => ['name' => 'Пароль', 'sub_type' => 'password'],
                    'cfg[mysql_db]' => ['name' => 'База данных']
                ],
                'Аккаунт администратора' => [
                    [
                        'type' => 'status',
                        'fail' => $admin_error,
                        'result' => !$admin_error
                    ],
                    'admin[email]' => ['name' => 'E-Mail'],
                    'admin[nick]' => ['name' => 'Никнейм'],
                    'admin[pass]' => ['name' => 'Пароль', 'sub_type' => 'password'],
                    'admin[passr]' => ['name' => 'Повтор пароля', 'sub_type' => 'password'],
                    'admin[birth]' => ['name' => 'Дата рождения', 'type' => 'date', 'min_year' => -59, 'max_year' => -14],
                    'admin[gender]' => [
                        'name' => 'Пол',
                        'type' => 'select',
                        'options' => [
                            ['value' => 'male', 'placeholder' => 'Мужской'],
                            ['value' => 'female', 'placeholder' => 'Женский']
                        ]
                    ],
                    'admin[country]' => [
                        'name' => 'Страна',
                        'type' => 'select',
                        'options' => [
                            ['value' => 'RU', 'placeholder' => 'Россия'],
                            ['value' => 'UA', 'placeholder' => 'Україна'],
                            ['value' => 'UK', 'placeholder' => 'England'],
                            ['value' => 'DE', 'placeholder' => 'Deutschland'],
                            ['value' => 'PL', 'placeholder' => 'Polska'],
                            ['value' => 'KZ', 'placeholder' => 'Қазақстан'],
                            ['value' => 'BY', 'placeholder' => 'Беларусь'],
                            ['value' => 'AM', 'placeholder' => 'Հայաստան'],
                            ['value' => 'AZ', 'placeholder' => 'Azərbaycan']
                        ]
                    ]
                ]
            ]
        ]);
        return [$is_error ? 'error' : 'success', $is_done ? 'done' : 'submit'];
    },
    function () {
        // Переименовывание в шаблоне из-за синхронности
        View::load('*steps.done');
        return ['done'];
    }
];
