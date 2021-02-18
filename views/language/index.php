<?php if ($languages) { ?>
    <hr>
    <h3><div class="icon baseline"><?php InlineAsset::svg( 'globe' ) ?></div>languages</h3>
    <?php foreach ( $languages as $language ) { ?><p><?php echo $language ?></p><?php } ?>
<?php } ?>