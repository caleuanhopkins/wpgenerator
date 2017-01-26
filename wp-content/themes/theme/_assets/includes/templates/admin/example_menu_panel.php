<div class="wrap">
    
    <h2>Hello World</h2>
    <hr/>
    <form id="table-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $crmTable->display() ?>
    </form>
</div>