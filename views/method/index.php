<?php if ($methods) { ?>
    <hr>
    <h3><div class="icon baseline"><?php InlineAsset::svg( 'globe' ) ?></div>methods</h3>
    <?php foreach ( $methods as $method ) { ?>
        <div class="panelrow labelbar" >
            <div class="label"><?php echo $method->label ?></div>
            <?php if ( $method->aptitude ) { ?><div class="progressbar" ><div style="width:<?php echo $method->aptitude * 100 ?>%"></div></div><?php } ?>
        </div>
    <?php } ?>
<?php } ?>