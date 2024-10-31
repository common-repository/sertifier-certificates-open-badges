<div id="sertifier-container">
    <?php include(sprintf("%s/layouts/header.php", dirname(__FILE__))); ?>
    <div id="sertifier-body">
        <?php include(sprintf("%s/layouts/navbar.php", dirname(__FILE__))); ?>
        <div id="sertifier-content">
            <div class="head">
                <h1 class="page-title">
                    <?php
                        if($action == "add"){
                            echo "New " . ($type == "auto" ? "automated sending" : "manual sending");
                        }else {
                            echo "Edit " . ($type == "auto" ? "automated sending" : "manual sending");
                        }
                    ?>
                </h1>
            </div>
            <div class="content">
                <p style="margin-top: 0">
                    <?php
                    if($type == "auto") { 
                        if($action == "add"){
                    ?>
                        Please select the credential and the course/lesson you’d like to connect.
                    <?php 
                        } else {
                    ?>
                        You can edit the credential and the course/lesson you’ve connected.
                    <?php 
                        } 
                    } else { 
                        if($action == "add"){
                    ?> 
                        Please select a credential to start sending to your students. Select the students from the list below and click “Save changes” to bulk send your credential.
                    <?php 
                        } else {
                    ?>
                        You can edit this manual sending by selecting or unselecting students. Newly selected students will receive the credentials after saving this page. If you unselect an already selected student, the previously sent credential will be deleted after saving this page.
                    <?php
                        }
                    }
                    ?>
                </p>
                <form method="POST">
                <?php if($msg = wp_cache_get("sertifier_error_message")){ ?>
                    <h4 class="error"><?php echo esc_html($msg); ?></h4>
                <?php } ?>
                <?php if($msg = wp_cache_get("sertifier_success_message")){ ?>
                    <h4 class="success"><?php echo esc_html($msg); ?></h4>
                <?php } ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">Credential</th>
                                <td>
                                    <?php if($action=="edit" && $type=="manual"){ ?>
                                        <input hidden type="text" name="delivery_id" value="<?php echo esc_attr($record->delivery_id); ?>">
                                    <?php } ?>
                                    <select name="delivery_id" <?php echo esc_attr($action=="edit" && $type=="manual" ? "disabled" : NULL); ?>>
                                        <option disabled selected>Please select a credential</option>
                                        <?php
                                            foreach ($deliveryfilter as $id => $title) {
                                        ?>
                                            <option value="<?php echo esc_attr($id); ?>" <?php echo esc_attr(@$record->delivery_id==$id ? "selected": (array_search($id,$usedDeliveryIds) !== false ? "disabled" : NULL)); ?> ><?php echo esc_html($title); ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>                    
                                </td>
                            </tr>
                            <?php if($type == "auto"){ ?>
                            <tr>
                                <th scope="row">Course</th>
                                <td>
                                    <select name="course_id">
                                        <option disabled selected>Please select a course</option>
                                        <?php
                                            foreach ($courses as $course) {
                                        ?>
                                            <option value="<?php echo esc_attr($course->ID); ?>" <?php echo esc_attr(@$record->course_id==$course->ID ? "selected": ""); ?>><?php echo esc_html($course->post_title); ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>                    
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Lesson
                                    <span class="helper">If you select a lesson from the dropdown, we will track if the lesson is completed and send your credential if the student meets the lesson criteria. If you leave this field empty, we will only track if the selected course is completed and send your credential if the student meets the course criteria.</span>
                                </th>
                                <td>
                                    <select name="lesson_id">
                                    </select>        
                                </td>
                            </tr>
                            <?php }else if($type == "manual"){ ?> 
                            <tr>
                                <th scope="row">
                                    Students
                                    <span class="helper" id="users-helper"></span>
                                </th>
                                <td>
                                    <div class="user-list">
                                        <div class="search">
                                            <input type="text" id="search" placeholder="Search students">
                                            <select id="search_course">
                                                <option value="" selected>Filter by course</option>
                                                <?php
                                                    foreach ($courses as $course) {
                                                ?>
                                                    <option value="<?php echo esc_attr($course->ID); ?>"><?php echo esc_html($course->post_title); ?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div id="result"></div>
                                    </div>            
                                </td>
                            </tr>
                            <?php } ?> 
                        </tbody>
                    </table>
                    <?php if($type=="manual"){ ?>
                        <input type="hidden" name="usersNew" >
                    <?php } ?>
                    <?php submit_button("Save changes"); ?>  
                </form> 
            </div>
        </div>
    </div>
</div>