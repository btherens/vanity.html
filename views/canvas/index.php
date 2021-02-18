<!DOCTYPE html>
<html lang="en" >
    <?php
        include 'includes/head.php';
        include 'includes/style.php';
        // include 'includes/script.php';
    ?>
    <body>

        <div class="canvas-main" >
            <div class="pad">
                <div class="col-1">
                    <div class="container">
                            <?php echo $profile ?>
                            <?php echo $skill ?>
                            <?php echo $language ?>
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
            <p>
                powered by vanity.html<?php if ( $sourcecodeurl ) { ?> | <a href="<?php echo $sourcecodeurl ?>">source code</a><?php } ?> | © <?php echo date('Y'); ?> <?php echo $title; ?>
            </p>
        </div>
    </body>

</html>
