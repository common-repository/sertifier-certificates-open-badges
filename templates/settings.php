<div id="sertifier-container">
    <?php include(sprintf("%s/layouts/header.php", dirname(__FILE__))); ?>
    <div id="sertifier-body">
        <?php include(sprintf("%s/layouts/navbar.php", dirname(__FILE__))); ?>
        <div id="sertifier-content">
            <div class="head">
                <h1 class="page-title">Settings</h1>
            </div>
            <div class="content">
                <p style="margin-top: 0">Please enter your API key to activate the plugin. You can find your API Key at Settings > API & Integrations page of your Sertifier account.</p>
                <form method="POST" action="">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">API Key</th>
                                <td>
                                    <input type="text" name="api_key" value="<?php echo esc_attr(get_option("sertifier_api_key")); ?>" />               
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button("Save Changes"); ?> 
                </form> 
            </div>
        </div>
    </div>
</div>