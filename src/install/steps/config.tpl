<table>
    <tbody>
        {% for $group, $fields in $vars->groups %}
            <tr>
                <th colspan="2">{{ $group }}</th>
            </tr>
            {% for $key, $field in $fields %}
                {% if isset($field['type']) && $field['type'] == 'status' %}
                    {% if !$field['result'] %}
                        <tr><td colspan="2"><div class="icon err"></div> {{ $field['fail'] }}</td></tr>
                    {% endif %}
                {% else %}
                    <?php $key = str_replace('.', '/', $key); ?>
                    <tr>
                        <td>{{ $field['name'] }}</td>
                        <td>
                            {% if isset($field['type']) %}
                                {% if $field['type'] == 'select' %}
                                    <select name="{{ $key }}">
                                        {% for $option in $field['options'] %}
                                            <option
                                                value="{{ $option['value'] }}"
                                                {{ isset($option['selected']) && $option['selected'] ? 'selected' : '' }}
                                            >
                                                {{ isset($option['placeholder']) ? $option['placeholder'] : $option['value'] }}
                                            </option>
                                        {% endfor %}
                                    </select>
                                {% elseif $field['type'] == 'date' %}
                                    <div class="input-flex">
                                        <select name="{{ $key }}[0]">
                                            <?php for ($d = 1; $d <= 31; $d++): ?>
                                                <option>{{ $d }}</option>
                                            <?php endfor; ?>
                                        </select>
                                        <select name="{{ $key }}[1]">
                                            <option value="1">Январь</option>
                                            <option value="2">Февраль</option>
                                            <option value="3">Март</option>
                                            <option value="4">Апрель</option>
                                            <option value="5">Май</option>
                                            <option value="6">Июнь</option>
                                            <option value="7">Июль</option>
                                            <option value="8">Август</option>
                                            <option value="9">Сентябрь</option>
                                            <option value="10">Октябрь</option>
                                            <option value="11">Ноябрь</option>
                                            <option value="12">Декабрь</option>
                                        </select>
                                        <select name="{{ $key }}[2]">
                                            <?php for ($y = $field['min_year']; $y <= $field['max_year']; $y++): ?>
                                                <option>{{ date('Y') + $y }}</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                {% endif %}
                            {% else %}
                                <input type="{{ isset($field['sub_type']) ? $field['sub_type'] : 'text' }}" name="{{ $key }}" value="{{ isset($_POST[$key]) ? $_POST[$key] : '' }}" />
                            {% endif %}
                        </td>
                    </tr>
                {% endif %}
            {% endfor %}
        {% endfor %}
    </tbody>
</table>