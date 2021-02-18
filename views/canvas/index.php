<!DOCTYPE html>
<html>
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
                        <hr>
                        <h3><div class="icon baseline"><?php InlineAsset::svg( 'gear' ) ?></div>languages</h3>
                        <p>English</p>
                        <p>Spanish</p>
                        <p>German</p>
                    </div>
                </div>
            </div>

            <div class="col-2">

                <div class="container card">
                    <h2><div class="icon baseline"><?php InlineAsset::svg( 'filedone' ) ?></div>work experience</h2>
                    <div>
                        <h5>Front End Developer / altavista.com</h5>
                        <h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div>Jan 2015 - <span class="highlight">Current</span></h6>
                        <p>Lorem ipsum dolor sit amet. Praesentium magnam consectetur vel in deserunt aspernatur est
                            reprehenderit sunt hic. Nulla tempora soluta ea et odio, unde doloremque repellendus iure,
                            iste.</p>
                        <hr>
                    </div>
                    <div>
                        <h5>Web Developer / something.com</h5>
                        <h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div>Mar 2012 - Dec 2014</h6>
                        <p>Consectetur adipisicing elit. Praesentium magnam consectetur vel in deserunt aspernatur est
                            reprehenderit sunt hic. Nulla tempora soluta ea et odio, unde doloremque repellendus iure,
                            iste.</p>
                        <hr>
                    </div>
                    <div>
                        <h5>Graphic Designer / designsomething.com</h5>
                        <h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div>Jun 2010 - Mar 2012</h6>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                    </div>
                </div>

                <div class="container card">
                    <h2><div class="icon baseline"><?php InlineAsset::svg( 'filesource' ) ?></div>education</h2>
                    <div>
                        <h5>Yale Law School</h5>
                        <h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div>2010 - 2014</h6>
                        <p>Web Development! All I need to know in one place</p>
                        <hr>
                    </div>
                    <div>
                        <h5>London Business School</h5>
                        <h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div>2006 - 2010</h6>
                        <p>Master Degree</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="canvas-footer">
        <p>powered by vanity.html | source code</p>
    </div>

    </body>

</html>
