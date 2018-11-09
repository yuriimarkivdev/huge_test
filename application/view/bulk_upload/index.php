<div class="container">
    <h1>Bulk upload</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Bulk upload</h3>
        <form method="post" action="<?php echo Config::get('URL'); ?>bulk_upload/post_action" enctype="multipart/form-data">
            <div class="form-group">
                <label for="pwd">Select csv file to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </form>
    </div>
</div>
