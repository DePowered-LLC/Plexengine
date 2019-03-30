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
                <b tooltip="| rooms_total_number |">{{ $vars->total_online }}</b>
                <h1>{{ $_SESSION['userdata']['nick'] }}</h1>
                <span class="muted">| rooms_select |</span>
            </div>
            <div content>
                {% for $room in $vars->rooms %}
                <div class="item" onclick="select_room({{ $room['id'] }})">
                    <b>{{ $room['online'] }} | rooms_users |</b>
                    <span>{{ $room['name'] }}</span>
                </div>
                {% endfor %}
            </div>

            <a class="btn" href="/auth/logout">| logout |</a>
        </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script defer src="/public/js/main.js"></script>
    <script>
        function select_room (i) {
            {% if isset($_SESSION['userdata']['room']) %}
            if (i == {{ $_SESSION['userdata']['room'] }}) return close_modal('room-select');
            {% endif %}
            $.get('/rooms/apply?id=' + i, res => {
                if (res == 'refulled') alert('| rooms_max_users |');
                else window.location.href = '/';
            });
        }
    </script>
</html>