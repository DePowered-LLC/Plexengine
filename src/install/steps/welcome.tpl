<div id="welcome">
    <h1>Добро пожаловать в мастер установки</h1>
	Во время установки будет создана структура базы данных, аккаунт администратора а также конфигурационный файл с базовыми настройками системы.
    <br />
    <br />
    Прежде чем продолжить, рекомендуем ознакомиться с документацией движка. <a href="https://wiki.plexengine.ru/" target="_blank"><b>Документация</b></a>
    <br />
    <br />
    {% if $vars->version < $vars->actual_version %}
    Обратите внимание! Вы устанавливаете старую версию скрипта <b>({{ $vars->version }})</b>.
    <br />
    Рекомендуем скачать актуальную версию <b>({{ $vars->actual_version }})</b> с сайта разработчика <a href="https://plexengine.ru" target="_blank"><b>www.plexengine.ru</b></a>
    {% else %}
    Вы устанавливаете Plexengine v{{ $vars->version }}.
    {% endif %}
    <br />
</div>