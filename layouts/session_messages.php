<?php if (isset($_SESSION['message'])) { ?>
    <br/>
    <div class="alert alert-<?php echo $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
        <strong><?php echo $_SESSION['message'] ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php
    unset($_SESSION['message']);
} ?>