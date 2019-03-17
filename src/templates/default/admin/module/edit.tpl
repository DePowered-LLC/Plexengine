{% include admin.header %}
<h1>
    {{ $vars->module_name }}
    <span style="font-size: 14px;" class="muted">(v{{ $vars->module_version }})</span>
</h1>
<h3>{{ $vars->menu_info['name'] }}</h3>
<hr />

{% if $vars->menu_info['type'] == 'db_view' %}
<table>
    <thead>
        {% for $key, $field in $vars->scheme %}
            <th>{{ $field['name'] }}</th>
        {% endfor %}
    </thead>
    <tbody>
        {% for $row in $vars->data %}
            <tr>
                {% for $key, $field in $vars->scheme %}
                    <td>{{ $row[$key] }}</td>
                {% endfor %}
            </tr>
        {% endfor %}
    </tbody>
</table>
{% elseif $vars->menu_info['type'] == 'db_add' %}
<form action="?apply" method="POST">
    <table>
        {% for $key, $field in $vars->scheme %}
            {% if !isset($field['readonly']) || !$field['readonly'] %}
                <tr>
                    <td>{{ $field['name'] }}</td>
                    <td>
                        <input type="text" name="{{ $key }}" />
                    </td>
                </tr>
            {% endif %}
        {% endfor %}
    </table>
    <button class="btn" type="submit">| apply |</button>
</form>
{% endif %}
{% include admin.footer %}