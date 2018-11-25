<!DOCTYPE html>
<html>
    <head>
        <title>Plexengine</title>
        <meta charset="UTF-8" />
        <style>
            <?php include_once ENGINE.'/style.css'; ?>
            [b] {
                display: inline-block;
                margin: 0 10px;
            }

            [b] > img {
                display: block;
                width: 42px;
                height: 42px;
                margin-bottom: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container" style="text-align: center;">
            <div class="box">
                <h2>Вы используете устаревший браузер</h2>
                <h3>Пожалуйста, установите любой из предложенных ниже.</h3>
                <hr />
                <p>
                    <a b href="https://www.mozilla.org/ru/firefox/download">
                        <img src="https://www.mozilla.org/media/img/logos/firefox/logo-quantum.9c5e96634f92.png" />
                        FireFox
                    </a>
                    <a b href="https://www.google.com/chrome">
                        <img src="https://google.com/chrome/static/images/chrome-logo.svg" />
                        Chrome
                    </a>
                    <a b href="https://www.opera.com">
                        <img src="https://www-static.operacdn.com/static-heap/6e/6eeaecd153e69883e2429e4755f5361048cfac89/opera-mobile-apps.png" />
                        Opera
                    </a>
                </p>
            </div>
        </div>
        <div class="copyright">
            &copy; 2018 <a href="https://depowered.ru">DePowered LLC</a> & Plexengine
            <br />
            All Rights Reserved.
        </div>
    </body>
</html>