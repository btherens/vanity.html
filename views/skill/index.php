<?php if ($skills) { ?>
    <hr>
    <h3><div class="icon baseline"><?php InlineAsset::svg( 'gear' ) ?></div>skills</h3>
    <?php foreach ( $skills as $skill ) { ?><p><?php echo $skill ?></p><?php } ?>
<?php } ?>