<div id="wallet-top-block">
    <img src="/public/avatars/id{{ $_SESSION['userdata']['id'] }}.jpg" />
    <h1>{{ $_SESSION['userdata']['nick'] }}, | points_iya | {{ $_SESSION['userdata']['credits'] }} | coins |</h1>
    <span>| points_info |</span>
    <b>| points_yh | <i>{{ $_SESSION['userdata']['points'] }}</i> | points |</b>
</div>
<div id="wallet-footer">
    | points_info2 |
    <br />
    <a href="javascript:void(0);">| points_more |</a>
    <br />
    <br />
    <div id="wallet-exchange">
        <div>| points_enter |: <input name="points" value="{{ 5 * $_CONFIG['exchange_rate'] }}" /></div>
        <div>| points_ywg | | coins |: <input name="coins" value="5" /></div>
    </div>
    <button onclick="exchange()" class="btn">| points_exch |</button>
</div>
<script>
    var ex_rate = {{ $_CONFIG['exchange_rate'] }};
    $('#wallet-footer input[name="points"]').on('input', e => {
        $('#wallet-footer input[name="coins"]').val(parseFloat(e.target.value / ex_rate));
    });
    $('#wallet-footer input[name="coins"]').on('input', e => {
        $('#wallet-footer input[name="points"]').val(parseFloat(e.target.value * ex_rate));
    });

    function exchange() {
        $('#wallet-footer > button').addClass('loading');
        var val = $('#wallet-footer input[name="points"]').val();
        $.get('/modules/Auth/exchange?v=' + val, res => {
            if (res == 'no_points') alert('| no_points |');
            else if (res == 'success') {
                load_modal('wallet', null, $('[load-modal="wallet"]'));
                $('#balance > t').html(parseFloat($('#balance > t').html()) + val / ex_rate);
                $('#balance > span').html((parseInt($('#balance > span').html()) - val) + ' points');
            } else alert('Server error');
            $('#wallet-footer > button').removeClass('loading');
        });
    }
</script>