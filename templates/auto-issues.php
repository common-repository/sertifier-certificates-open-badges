<div id="sertifier-container">
    <?php include(sprintf("%s/layouts/header.php", dirname(__FILE__))); ?>
    <div id="sertifier-body">
        <?php include(sprintf("%s/layouts/navbar.php", dirname(__FILE__))); ?>
        <div id="sertifier-content">
            <div class="head">
                <h1 class="page-title">Automated sending</h1>
                <a href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_auto_add_or_update'], admin_url('admin.php'))); ?>" class="add-new-button"><span class="dashicons dashicons-plus"></span> Create new integration</a>
            </div>
            <div class="content">
                <p style="margin-top: 0">You can automatically send badges or certificates that are created on your Sertifier account by connecting them to courses or lessons on your TutorLMS account. Please click “Create new integration” button above to start automate sending credentials upon course or lesson completion.</p>
                <?php if(@$_GET["message"] == 1){ ?>
                    <h4 class="success">Record saved succesfully.</h4>
                <?php } ?>
                <?php if(@$_GET["message"] == 2){ ?>
                    <h4 class="success">Record updated succesfully.</h4>
                <?php } ?>
                <table class="sertifier-table">
                    <thead>
                        <tr>
                            <td>Credential name</td>
                            <td>Course name</td>
                            <td style="text-align: center;">Date created</td>
                            <td style="text-align: center;">Actions</td>    
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($records as $record){
                        ?>
                        <tr>
                            <td><?php echo esc_html($record->delivery_title); ?></td>
                            <td><?php echo esc_html($record->course_title); ?></td>
                            <td style="text-align: center;"><?php echo esc_html(date_format(date_create($record->created_at),"d.m.Y, H:i")); ?></td>
                            <td style="text-align: center;">
                                <a title="Edit automated sending" href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_auto_add_or_update','id' => $record->id], admin_url('admin.php'))); ?>"><span class="dashicons dashicons-edit"></span></a>
                                <a titye="Delete" id="test" href="javascript:void(0);" onclick="deleteRecord(<?php echo esc_attr($record->id); ?>, this, 'delete_auto_issue')"><span class="dashicons dashicons-trash"></span></a>
                            </td>
                        </tr>
                        <?php } 
                            if(count($records) == 0){
                        ?>
                            <tr>
                                <td colspan="4" class="no-records">No records.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>