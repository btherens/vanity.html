<!DOCTYPE html>
<html lang="en" >
    <?php
        include 'includes/head.php';
        include 'includes/style.php';
        include 'includes/script.php';
    ?>
    <body>

        <div class="canvas-main" >
            <div class="pad">
                <div class="col-1">
                    <div class="container">
                        <img src="/asset/avatar.jpg" >
                        <div class="panel">
                            <?php echo $profile ?>
                            <?php echo $skill ?>
                            <?php echo $method ?>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <?php echo $work ?>
                    <?php echo $education ?>
                </div>
            </div>
        </div>
        
        <div class="canvas-footer">
            <p>powered by <span style="font-weight: 700;">vanity.html</span><?php if ( $sourcecodeurl ) { ?> | <a href="<?php echo $sourcecodeurl ?>">source code</a><?php } ?> | © <?php echo date('Y'); ?> <?php echo $title; ?></p>
        </div>
    </body>

</html>
