{% include admin.header %}
<h1>{{ $vars->module_name }}</h1>
<h3>Информационная страница</h3>
<h5 class="muted">pe\modules\{{ $vars->module_id }}</h5>
<hr />
Версия: v{{ $vars->module_version }}
{% include admin.footer %}