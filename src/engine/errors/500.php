<!DOCTYPE html>
<html>
    <head>
        <title>500 :: Plexengine</title>
        <meta charset="UTF-8" />
        <style><?php echo file_get_contents(ENGINE.'/style.css'); ?></style>
    </head>
    <body>
        <div class="container">
            <div class="box">
                <h1>500</h1>
                <h2>Internal Server Error</h2>
                <hr />
                <p>An unexpected error occurred on this page.</p>
                <?php if($_CONFIG['debug']): ?>
                <br />
                Debug log:
                <span id="full_size" onclick="full_size();" style="cursor: pointer; float: right; background-color: #efefef; padding: 2px 5px; border-radius: 20px;"><< >></span>
                <script>
                    var is_full = false;
                    function full_size() {
                        document.getElementsByClassName('container')[0].style.maxWidth = is_full?'':'80%';
                        document.getElementById('full_size').innerHTML = is_full?'<< >>                                                                                                                                                 ':'>> <<';
                        console.log(this);
                        is_full = !is_full;
                    }
                </script>
                <pre><?php echo $debug ?></pre>
                <?php endif; ?>
                <b>Contact administrator and make them aware of this issue</b>
            </div>
        </div>
        <div class="copyright">
            &copy; 2018 <a href="http://dp.mayerdev.ru">DePowered LLC<sup>md</sup></a> & <a href="https://plexengine.com">Plexengine</a>
            <br />
            All Rights Reserved.
        </div>
    </body>
</html>
