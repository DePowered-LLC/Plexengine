<html>  
    <head>
        <title>| away_title |</title>
        <meta charset="UTF-8" />
        <style>
            body {
                font-family: Tahoma;
                font-size: 12px;
                line-height: 200%;
                padding: 20px 180px;
            }
        </style>
    </head>
    <body>
        <h2>| away_title |</h2>
        {{ str_replace('{site_name}', $_CONFIG['site_name'], str_replace('{url}', $_GET['r'], View::lang('away_info'))) }}
    </body>
</html>