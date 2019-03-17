<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $_CONFIG['site_name'] }}</title>
        <link rel="stylesheet" href="/public/css/admin.css" />
    </head>
    <body>
        <div id="box">
            <h2>Успех</h2>
            <p>Операция была успешно выполнена.</p>
            <button class="btn" onclick="window.history.back()">Назвад</button>
            <a class="btn btn-green" href="/admin">На главную</a>
        </div>
    </body>

    <style>
    #box {
        display: block;
        width: 480px;
        background-color: #fff;
        padding: 20px 30px;
        border-radius: 10px;
        margin: auto;
        box-shadow: rgba(0, 0, 0, 0.05) 0 0 40px 0;
    }
    
    #box > h2 { margin: 0; }
    #box > p { margin: 5px 0 20px 0; }
    </style>
</html>