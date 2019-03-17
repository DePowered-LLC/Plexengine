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
                <b style="float: right; font-size: 24px; margin-top: 5px;">
                    <?php
                        $total_online = 0;
                        foreach ($vars->rooms as $room) $total_online += $room['online'];
                        echo $total_online;
                    ?>
                </b>
                
                <img src="/uploads/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
                <h1>{{ $_SESSION['userdata']['nick'] }}</h1>
                <span class="muted">| rooms_select |</span>
            </div>
            <hr />
            <div content>
                {% for $room in $vars->rooms %}
                <div class="item" onclick="select_room({{ $room['id'] }})">
                    <b>{{ $room['online'] }}</b>
                    <span>{{ $room['name'] }}</span>
                </div>
                {% endfor %}
            </div>

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