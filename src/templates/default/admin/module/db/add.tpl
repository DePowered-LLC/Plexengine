{% include admin.module.header %}
<form onsubmit="return create(event)">
    <table>
        {% for $key, $field in $vars->scheme %}
            {% if !isset($field['readonly']) || !$field['readonly'] %}
                <tr>
                    <td>{{ $field['name'] }}</td>
                    <td>
                        <input type="text" name="{{ $key }}" {{ isset($field['default']) ? 'value="'.$field['default'].'"' : '' }} />
                    </td>
                </tr>
            {% endif %}
        {% endfor %}
    </table>
    <button class="btn" type="submit">| apply |</button>
</form>
<script>
    function create (e) {
        var data = {};
        $(e.target.elements).each((k, el) => {
            if (el.name) data[el.name] = el.value;
        });

        apply('add', data).then(res => {
            switch (res) {
                case 'fill-all':
                    alert('Заполните все поля');
                    break;
                case 'success':
                    alert('Успешно добавлено');
                    break;
            }
        });
        return false;
    }
</script>
{% include admin.footer %}