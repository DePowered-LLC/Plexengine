<!DOCTYPE html>
<html>
    <head>
        <title>Установщик Plexengine</title>
        <meta charset="UTF-8" />
        <style>
        @import url('https://fonts.googleapis.com/css?family=Mukta+Mahee');
        * { font-family: 'Mukta Mahee', sans-serif; }
        body {
            margin: 20px auto;
            border-radius: 5px;
            box-shadow: rgba(0, 0, 0, 0.15) 1px 2px 15px 0;
            border: 1px solid #dedede;
            width: 800px;
        }
        
        #header {
            position: relative;
            background-color: #fff;
            background-image: url('http://plexengine.ru/cdn/installer/bg_installer.gif');
			background-repeat: no-repeat;
            border-bottom: 1px solid #dedede;
            padding: 10px 15px;
		    height: 200px;
            color: #ffffff;
        }
        
        #wrapper {
            display: flex;
            flex-direction: column;
            padding: 10px 15px;
            color: #262931;
            align-items: flex-end;
        }
        
        #version {
            position: absolute;
            top: 130px;
            display: flex;
            align-items: center;
            line-height: 13px;
            padding: 5px;
            color: #6c7077;
            text-align: right;
            font-size: 15px;
            border: 2px dashed #dedede;
            border-radius: 3px;
            line-height: 13px;
            margin-right: 5px;
        }
        
        #version > b {
            font-size: 35px;
            margin-left: 10px;
            color: #5ec700;
            line-height: 32px;
        }
        
        #steps { 
			display: flex;
			margin-top: 173px;
		}
		
        #steps > .step {
            position: relative;
            display: inline-block;
            background-color: #545965;
            color: #fff;
            padding: 2px 15px;
            font-size: 16px;
            line-height: 28px;
            height: 26px;
            opacity: 0.4;
            flex: 1 1 0;
            text-align: center;
            margin: 0 7px;
            white-space: nowrap;
        }
        #steps > .step[active] { opacity: 1; }
        
        #steps > .step:before {
            content: '';
            position: absolute;
            right: 100%;
            top: 0;
            border-top: 15px solid #545965;
            border-bottom: 15px solid #545965;
            border-left: 10px solid transparent;
        }
        
        #steps > .step:after {
            content: '';
            position: absolute;
            left: 100%;
            top: 0;
            border-top: 15px solid transparent;
            border-bottom: 15px solid transparent;
            border-left: 10px solid #545965;
        }
        
        #steps > .step > b {
            display: inline-block;
            border: 1px solid #fff;
            box-sizing: border-box;
            height: 20px;
            line-height: 20px;
            width: 20px;
            vertical-align: top;
            margin: 3px 0;
            text-align: center;
            border-radius: 3px;
            margin-right: 5px;
        }

        #agree {
            display: inline-block;
            margin: 7px 5px;
            min-width: 0;
            vertical-align: top;
        }
        
        table { width: 100%; }
        td { padding: 2px 5px; }
        td:first-child { padding-left: 0; }
        td:last-child { padding-right: 0; }
        
        code {
            display: inline-block;
            background-color: rgba(0, 0, 0, 0.05);
            padding: 2px 5px;
            border-radius: 3px;
        }

        input[type="checkbox"] { width: auto; }
        input, select {
            width: 100%;
            background-color: #ECEFF1;
            border: 1px solid rgba(0, 0, 0, 0.15);
            padding: 2px 10px;
            border-radius: 3px;
            min-width: 250px;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            box-shadow: rgba(187, 222, 251, 0.5) 0 0 0 2px;
            outline: none;
        }
        
        .icon {
            position: relative;
            display: inline-block;
            width: 16px;
            height: 16px;
            vertical-align: middle;
            border-radius: 100%;
        }
        
        .icon.err { background-color: #F44336; }
        .icon.err:before, .icon.err:after {
            content: '';
            position: absolute;
            top: 2px;
            bottom: 2px;
            width: 2px;
            left: 2px;
            right: 2px;
            background-color: #fff;
            margin: auto;
            transform: rotateZ(45deg);
        }
        .icon.err:after { transform: rotateZ(-45deg); }
        
        .icon.ok { background-color: #4CAF50; }
        .icon.ok:before {
            content: '';
            position: absolute;
            top: 3px;
            left: 5px;
            height: 7px;
            width: 4px;
            border-bottom: 2px solid #fff;
            border-right: 2px solid #fff;
            transform: rotateZ(45deg);
        }
        
        .btn {
            position: relative;
            margin-left: auto;
            margin-top: 10px;
            margin-bottom: 10px;
            padding: 0 10px;
            border: none;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            padding-right: 30px;
            cursor: pointer;
            outline: none;
            transition: 0.3s;
        }

        .btn.disabled {
            filter: grayscale(1);
            opacity: 0.6;
        }

        .btn-retry { background-color: #03A9F4; }
        .btn-retry:hover { background-color: #0288D1; }
        .btn-retry:before {
            content: '';
            position: absolute;
            display: inline-block;
            width: 10px;
            height: 10px;
            right: 8px;
            top: 0;
            bottom: 0;
            margin: auto;
            border: 2px solid #fff;
            border-radius: 100%;
            border-left-color: transparent;
            transition: 0.3s;
        }

        .btn-retry:after {
            content: '';
            position: absolute;
            display: inline-block;
            right: 16px;
            top: 15px;
            border-right: 4px solid transparent;
            border-bottom: 4px solid white;
            border-left: 4px solid transparent;
            transform: rotateZ(-45deg);
            transition: 0.3s;
        }
        
        .btn-retry:hover:before { transform: rotateZ(45deg); }
        .btn-retry:hover:after {
            top: 10px;
            right: 17px;
            transform: rotateZ(0);
        }

        .btn-next { background-color: #4CAF50; }
        .btn-next:hover { background-color: #388E3C; }       
        .btn-next:before, .btn-next:after {
            content: '';
            position: absolute;
            display: inline-block;
            width: 4px;
            height: 4px;
            right: 17px;
            top: 0;
            bottom: 0;
            margin: auto;
            border-right: 2px solid #fff;
            border-top: 2px solid #fff;
            transform: rotateZ(45deg);
            transition: 0.3s;
        }
        
        .btn-next:after {
            width: 8px;
            height: 8px;
            right: 12px;
        }
        
        .btn-next:hover:before, .btn-next:hover:after {
            transform: translateX(4px) rotate(45deg);
        }

        .input-flex { display: flex; }
        .input-flex > * { flex: 1; min-width: auto; }
        .input-flex > *:not(:first-child) { margin-left: 5px; }
        .input-flex > *:not(:last-child) { margin-right: 5px; }
        </style>
    </head>
    <body>
        <div id="header">
            <div id="logo"></div>
            {% if $vars->actual_version %}
            <div id="version">
                <span>Актуальная<br />версия</span>
                <b>{{ $vars->actual_version }}</b>
            </div>
            {% endif %}

            <div id="steps">
                {% for $step, $name in $vars->steps %}
                    <span class="step" {{ $vars->step >= ++$step ? 'active' : '' }}><b>{{ $step }}</b> {{ $name }}</span>
                {% endfor %}
            </div>
        </div>
        <form method="POST" id="wrapper">
            {{ $vars->step_result }}
            <div id="buttons">
                {% if $vars->step != count($vars->steps) %}
                    {% if in_array('error', $vars->status) %}
                        <button type="submit" class="btn btn-retry">Повторить</button>
                    {% else %}
                        {% if in_array('submit', $vars->status) %}
                            <button type="submit" class="btn btn-next">Далее</button>
                        {% else %}
                            <a href="?step={{ $vars->step + 1 }}" class="btn btn-next">Далее</a>
                        {% endif %}
                    {% endif %}
                {% endif %}
            </div>
        </form>
    </body>
</html>