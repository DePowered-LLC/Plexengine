<table>
    <tbody>
        {% for $row in $vars->checklist %}
            <tr>
                <td>{{ $row['name'] }}</td>
                {% if $row['result'] %}
                    <td>OK</td>
                    <td align="right"><i class="icon ok"></i></td>
                {% else %}
                    <td>{{ $row['fail'] }}</td>
                    <td align="right"><i class="icon err"></i></td>
                {% endif %}
            </tr>
        {% endfor %}
    </tbody>
</table>