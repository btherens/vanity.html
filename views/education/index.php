<?php if ( $educations ) { ?>
    <div class="container card">
        <h2><div class="icon baseline"><?php InlineAsset::svg( 'filesource' ) ?></div>education</h2>
        <?php foreach ( $educations as $i => $education ) { ?>
            <div>
                <h5><?php echo $education->subject ?> / <?php echo $education->organization ?></h5>
                <?php if ( $education->startdate   )       { ?><h6><div class="icon baseline"><?php InlineAsset::svg( 'calendar' ) ?></div><?php echo date( 'F Y', strtotime( $education->startdate ) ) ?> – <?php if ( $education->enddate ) { echo date( 'F Y', strtotime( $education->enddate ) ); } else { ?><span class="highlight">Current</span><?php } ?></h6><?php } ?>
                <?php if ( $education->description )       { ?><p><?php echo $education->description ?></p><?php } ?>
                <?php if ( $i < count( $educations ) - 1 ) { ?><hr><?php } else { ?><br><?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
