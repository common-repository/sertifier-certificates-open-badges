<div id="sertifier-nav">
    <a 
        href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_home'], admin_url('admin.php'))); ?>" 
        class="<?php echo esc_attr($_GET["page"] == "sertifier_home" ? "active" : ""); ?>"
    >
        Home
    </a>
    <a 
        href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_auto_issues'], admin_url('admin.php')));?>" 
        class="<?php echo esc_attr($_GET["page"] == "sertifier_auto_issues" || @$type == "auto" ? "active" : ""); ?>"
    >
        Automated sending
    </a>
    <a 
        href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_manual_issues'], admin_url('admin.php'))); ?>"
        class="<?php echo esc_attr($_GET["page"] == "sertifier_manual_issues" || @$type == "manual" ? "active" : ""); ?>"
    >
        Manual sending
    </a>
    <a 
        href="<?php echo esc_url(add_query_arg(['page' => 'sertifier_settings'], admin_url('admin.php'))); ?>" 
        class="<?php echo esc_attr($_GET["page"] == "sertifier_settings" ? "active" : ""); ?>"
    >
        Settings
    </a>
</div>