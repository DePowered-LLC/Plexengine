{% include admin.module.header %}
<table>
    <thead>
        {% for $key, $field in $vars->scheme %}
            <th>{{ $field['name'] }}</th>
        {% endfor %}
        <th></th>
    </thead>
    <tbody>
        {% for $row in $vars->data %}
            <tr id="data-row-{{ $row['id'] }}">
                {% for $key, $field in $vars->scheme %}
                    <td>{{ $row[$key] }}</td>
                {% endfor %}
                <td>
                    <a onclick="remove({{ $row['id'] }})" class="btn btn-small btn-red">
                        <svg><use xlink:href="#truncateIcon"></use></svg>
                    </a>
                </td>
            </tr>
        {% endfor %}
    </tbody>
</table>
<script>
    function remove (id) {
        apply('remove', { id }).then(res => {
            if (res == 'success') {
                $('#data-row-' + id).remove();
                alert('Успешно удалено');
            } else {
                alert('Непредвиденная ошибка на сервере');
                console.error(res);
            }
        });
    }
</script>
{% include admin.footer %}