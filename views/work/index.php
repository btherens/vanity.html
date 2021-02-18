<?php if ( $works ) { ?>
    <div class="container card">
        <h2><div class="icon baseline"><?php InlineAsset::svg( 'filedone' ) ?></div>work experience</h2>
        <?php foreach ( $works as $i => $work ) { ?>
            <div>
                <h5><?php echo $work->role ?> / <?php echo $work->organization ?></h5>
                <?php if ( $work->startdate   )       { ?><h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div><?php echo date( 'F Y', strtotime( $work->startdate ) ) ?> – <?php if ( $work->enddate ) { echo date( 'F Y', strtotime( $work->enddate ) ); } else { ?><span class="highlight">Current</span><?php } ?></h6><?php } ?>
                <?php if ( $work->description )       { ?><p><?php echo $work->description ?></p><?php } ?>
                <?php if ( $i < count( $works ) - 1 ) { ?><hr><?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
