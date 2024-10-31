<div id="sertifier-header">
    <a href="https://sertifier.com">
        <img src="<?php echo plugins_url( "/assets/images/logo.svg", SERTIFIER_FILE); ?>" alt="">
    </a>
</div>
<?php
    if(empty(get_option("sertifier_api_key"))){
?>
    <h4 class="warning">
        Please enter your API Key in the Settings tab to activate the plugin.
    </h4>
<?php } ?>