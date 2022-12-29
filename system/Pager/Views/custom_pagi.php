<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(3);
?>


<!-- <div class="btn-group me-2" role="group" aria-label="First group">     -->

<?php 
$i=1;
foreach ($pager->links() as $link) : ?>

    
    <a data-link="<?= $link['title'] ?>" href="<?= $link['uri'] ?>" <?= $link['active'] ? 'class="btn pgin active"' : 'class="btn pgin"' ?> style="font-weight: bold;">
        
            <?= $link['title'] ?>
       
    </a>

<?php $i++;?>
<?php endforeach ?>
<!-- </div> -->