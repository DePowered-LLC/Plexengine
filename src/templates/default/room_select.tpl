<!DOCTYPE html>
<html>
    <head>
        <title>{{ $_CONFIG['site_name'] }}</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="/public/css/room_select.css" />
   </head>
    <body>
        <div id="room_select" class="box">
            <div heading>
                <img src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
                <b>{{ $_SESSION['userdata']['nick'] }}</b>
                <hr />

                <span id="wallet">
                    <i class="chat_icon_wallet"></i>
                    {{ $_SESSION['userdata']['credits'] }} | coins |
                </span>

                <span id="points">
                    {{ $_SESSION['userdata']['points'] }} | points |
                </span>
            </div>
            <hr />
            <div content>
                <div class="item" onclick="select_room(1)">
                    <b>0</b>
                    <span>Главная</span>
                </div>
            </div>

            <span class="muted">| rooms_select |</span>
            <a class="btn" href="/auth/logout">| logout |</a>
        </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        function select_room(i) {
            $.get('/rooms/apply?id=' + i, () => window.location.href = '/');
        }
    </script>
</html>