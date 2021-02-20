<?php if ($methods) { ?>
    <hr>
    <h3><div class="icon baseline"><?php InlineAsset::svg( 'globe' ) ?></div>methods</h3>
    <?php foreach ( $methods as $method ) { ?><p><?php echo $method ?></p><?php } ?>
<?php } ?>