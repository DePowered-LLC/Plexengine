<!DOCTYPE html>
<html>
    <head>
        <title>403 :: Plexengine</title>
        <meta charset="UTF-8" />
        <style><?php echo file_get_contents(ENGINE.'/errors/style.css'); ?></style>
    </head>
    <body>
        <div class="container">
            <div class="box">
                <h1>403</h1>
                <h2>You haven`t access to this page</h2>
                <hr />
                <p>Might have been changed access to this page or you not authorized.</p>
                <p>Please try the following:</p>
                <ul>
                    <li>If you type the page address in the <strong>Address bar</strong>, make sure that it is spelled correctly.</li>
                    <li>Click the <strong>Back button</strong> to return to your previously visited page</li>
                    <li>If you were linked to this page, contact the administrator and make them aware of this issue.</li>
                </ul>
                <?php if($_CONFIG['debug']): ?>
                <br />
                Debug data:
                <pre><?php echo $debug; ?></pre>
                <?php endif; ?>
            </div>
        </div>
        <div class="copyright">
        &copy; 2018 <a href="http://dp.mayerdev.ru">DePowered LLC<sup>md</sup></a> & <a href="https://plexengine.ru">Plexengine</a>
            <br />
            All Rights Reserved.
        </div>
    </body>
</html>