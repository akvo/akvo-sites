<?php
if (!class_exists('OutoftheBox_Settings')) {

  class OutoftheBox_settings {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $general_settings_key = 'out_of_the_box_settings';
    private $advanced_settings_key = 'out_of_the_box_advancedsettings';
    private $plugin_options_key = 'OutoftheBox_settings';
    private $plugin_settings_tabs = array();

    /**
     * Construct the plugin object
     */
    public function __construct() {

      //Check if plugin can be used
      if ((version_compare(PHP_VERSION, '5.3.0') < 0) || (!function_exists('curl_init'))) {
        add_action('admin_notices', array(&$this, 'OutoftheBox_AdminNotice'));
        return;
      } else {
        // register actions
        add_action('admin_enqueue_scripts', array(&$this, 'OutoftheBox_LoadAdmin'));
        add_action('admin_menu', array(&$this, 'OutoftheBox_AddMenu'));
        add_action('init', array(&$this, 'OutoftheBox_LoadSettings'));
        add_action('admin_init', array(&$this, 'OutoftheBox_RegisterGeneralSettings'));
        add_action('admin_init', array(&$this, 'OutoftheBox_RegisterAdvancedSettings'));

        //add TinyMCE button
        add_action('init', array(&$this, 'OutoftheBox_ShortcodeButtonInit'));

        require_once 'includes/OutoftheBox_Processor.php';
      }
    }

    public function OutoftheBox_LoadAdmin($hook) {

      if (!isset($this->settingspage) && !isset($this->filebrowserpage)) {
        return;
      }

      if ($hook == $this->settingspage || $hook == $this->filebrowserpage) {
        require_once 'includes/OutoftheBox_Dropbox.php';
        $this->OutoftheBox_Dropbox = new OutoftheBox_Dropbox;
      }

      if ($hook == $this->filebrowserpage) {
        global $OutoftheBox;
        $OutoftheBox->OutoftheBox_LoadScripts();
        $OutoftheBox->OutoftheBox_LoadStyles();
      }
    }

    /**
     * add a menu
     */
    public function OutoftheBox_AddMenu() {
      // Add a page to manage this plugin's settings
      add_menu_page('Out-of-the-Box', 'Out-of-the-Box', 'manage_options', $this->plugin_options_key, array(&$this, 'OutoftheBox_SettingsPage'), plugin_dir_url(__FILE__) . 'css/images/dropbox_logo_blue_small.png');
      $this->settingspage = add_submenu_page($this->plugin_options_key, 'Out-of-the-Box ' . __('Settings'), __('Settings'), 'manage_options', $this->plugin_options_key, array(&$this, 'OutoftheBox_SettingsPage'));
      $this->filebrowserpage = add_submenu_page($this->plugin_options_key, __('File browser', 'outofthebox'), __('File browser', 'outofthebox'), 'manage_options', $this->plugin_options_key . '_filebrowser', array(&$this, 'OutoftheBox_Filebrowser'));
    }

    /*
     * Register our settings
     */

    public function OutoftheBox_RegisterGeneralSettings() {
      $this->plugin_settings_tabs[$this->general_settings_key] = 'Out-of-the-Box';

      register_setting($this->general_settings_key, $this->general_settings_key);

      add_settings_section(
              'out-of-the-box-section', // ID
              __('Own Dropbox App?', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionGeneralDesc'), // Callback
              $this->general_settings_key // Page
      );

      add_settings_field(
              'dropbox_app_key', // ID
              'Dropbox App key', // Title
              array($this, 'settings_field_input_text'), // Callback
              $this->general_settings_key, // Page
              'out-of-the-box-section', // Section
              array('field' => 'dropbox_app_key', 'name' => $this->general_settings_key, 'values' => &$this->general_settings) //Arguments for callback
      );

      add_settings_field(
              'dropbox_app_secret', // ID
              'Dropbox App secret', // Title
              array($this, 'settings_field_input_text'), // Callback
              $this->general_settings_key, // Page
              'out-of-the-box-section', // Section
              array('field' => 'dropbox_app_secret', 'name' => $this->general_settings_key, 'values' => &$this->general_settings) //Arguments for callback
      );

      add_settings_field(
              'dropbox_app_token', // ID
              '', // Title
              array($this, 'settings_hidden_field'), // Callback
              $this->general_settings_key, // Page
              'out-of-the-box-section', // Section
              array('field' => 'dropbox_app_token', 'name' => $this->general_settings_key, 'values' => &$this->general_settings) //Arguments for callback
      );

      add_settings_section(
              'out-of-the-box-update-section', // ID
              __('Enable auto-updater', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionUpdaterDesc'), // Callback
              $this->general_settings_key // Page
      );


      add_settings_field(
              'purcasecode', // ID
              'Purchase Code', // Title
              array($this, 'settings_field_purchasecode'), // Callback
              $this->general_settings_key, // Page
              'out-of-the-box-update-section', // Section
              array('field' => 'purcasecode', 'name' => $this->general_settings_key, 'values' => &$this->general_settings) //Arguments for callback
      );
    }

    function OutoftheBox_RegisterAdvancedSettings() {
      $this->plugin_settings_tabs[$this->advanced_settings_key] = __('Advanced settings', 'outofthebox');

      register_setting($this->advanced_settings_key, $this->advanced_settings_key);

      add_settings_section(
              'out-of-the-box-links-section', // ID
              __('Shortlinks', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionAdvancedDesc'), // Callback
              $this->advanced_settings_key // Page
      );

      add_settings_section(
              'out-of-the-box-images-section', // ID
              __('Image gallery thumbnails', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionAdvancedDesc'), // Callback
              $this->advanced_settings_key // Page
      );

      add_settings_section(
              'out-of-the-box-folders-section', // ID
              __('User folders', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionAdvancedDesc'), // Callback
              $this->advanced_settings_key // Page
      );

      add_settings_section(
              'out-of-the-box-css-section', // ID
              __('Custom CSS', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionAdvancedDesc'), // Callback
              $this->advanced_settings_key // Page
      );
      add_settings_section(
              'out-of-the-box-notifying-section', // ID
              __('Notify email templates', 'outofthebox'), // Title
              array(&$this, 'OutoftheBox_SectionAdvancedDesc'), // Callback
              $this->advanced_settings_key // Page
      );

      add_settings_field(
              'shortlinks', // ID
              'Shortlinks API', // Title
              array($this, 'settings_field_shortlink'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-links-section', // Section
              array('field' => 'shortlinks', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );

      add_settings_field(
              'bitly_login', // ID
              'Bitly login', // Title
              array($this, 'settings_field_input_text'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-links-section', // Section
              array('field' => 'bitly_login', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );

      add_settings_field(
              'bitly_apikey', // ID
              'Bitly apiKey', // Title
              array($this, 'settings_field_shortlink_key'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-links-section', // Section
              array('field' => 'bitly_apikey', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );

      add_settings_field(
              'thumbnails', // ID
              'Generate thumbnails via', // Title
              array($this, 'settings_field_thumbnails'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-images-section', // Section
              array('field' => 'thumbnails', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );

      add_settings_field(
              'userfolder_name', // ID
              'User folder name', // Title
              array($this, 'settings_field_userfoldername'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-folders-section', // Section
              array('field' => 'userfolder_name', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );

      add_settings_field(
              'userfolder_oncreation', // ID
              'Create user folder on user registration', // Title
              array($this, 'settings_field_userfolder'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-folders-section', // Section
              array('field' => 'userfolder_oncreation', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'userfolder_onfirstvisit', // ID
              'Create all user folders on first visit (takes ~1 sec/user)', // Title
              array($this, 'settings_field_userfolder'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-folders-section', // Section
              array('field' => 'userfolder_onfirstvisit', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'userfolder_update', // ID
              'Update user folders after they update their profile', // Title
              array($this, 'settings_field_userfolder'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-folders-section', // Section
              array('field' => 'userfolder_update', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'userfolder_remove', // ID
              'Try to remove user folders after they are deleted', // Title
              array($this, 'settings_field_userfolder'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-folders-section', // Section
              array('field' => 'userfolder_remove', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'custom_css', // ID
              'Your custom CSS', // Title
              array($this, 'settings_field_css'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-css-section', // Section
              array('field' => 'custom_css', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'download_template', // ID
              'Template download notification ', // Title
              array($this, 'settings_field_emailtemplate'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-notifying-section', // Section
              array('field' => 'download_template', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
      add_settings_field(
              'upload_template', // ID
              'Template upload notification ', // Title
              array($this, 'settings_field_emailtemplate'), // Callback
              $this->advanced_settings_key, // Page
              'out-of-the-box-notifying-section', // Section
              array('field' => 'upload_template', 'name' => $this->advanced_settings_key, 'values' => &$this->advanced_settings) //Arguments for callback
      );
    }

    function OutoftheBox_LoadSettings() {
      $this->general_settings = (array) get_option($this->general_settings_key);
      $this->advanced_settings = (array) get_option($this->advanced_settings_key);
    }

    function OutoftheBox_SectionGeneralDesc() {
      echo __('If you created your own Dropbox App, please enter your settings below.', 'outofthebox');
      echo ' <a href="http://goo.gl/dsT71e" target="_blank">' . __('How do I create a Dropbox App?', 'outofthebox') . '</a>';
    }

    function OutoftheBox_SectionUpdaterDesc() {
      echo __('If you would like to receive updates, please insert your Purchase code', 'outofthebox') . '. ' .
      '<a href="http://support.envato.com/index.php?/Knowledgebase/Article/View/506/54/where-can-i-find-my-purchase-code">' .
      __('Where do I find the purchase code?', 'outofthebox') . '</a>.';
    }

    function OutoftheBox_SectionAdvancedDesc() {

    }

    /**
     * This function provides text inputs for settings fields
     */
    public function settings_field_input_text($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<input type="text" name="%s[%s]" id="%s" value="%s" />', $name, $field, $field, esc_attr($values[$field]));
    }

    /**
     * This function provides text inputs for settings fields
     */
    public function settings_hidden_field($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<input type="hidden" name="%s[%s]" id="%s" value="%s" />', $name, $field, $field, esc_attr($values[$field]));
    }

    /**
     * This function provides text inputs for the purchase code
     */
    public function settings_field_purchasecode($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<input type="text" name="%s[%s]" id="%s" value="%s" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" maxlength="37" style="width:300px"/>', $name, $field, $field, @esc_attr($values[$field]));
    }

    /*
     *
     */

    public function settings_field_css($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      $value = (isset($values[$field])) ? esc_attr($values[$field]) : '';
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<textarea name="%s[%s]" id="%s" cols="" rows="10" style="width:%s">%s</textarea>', $name, $field, $field, '90%', $value);
    }

    public function settings_field_emailtemplate($args) {
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      $value = (isset($values[$field])) ? esc_attr($values[$field]) : '';
      echo sprintf('<textarea name="%s[%s]" id="%s" cols="" rows="5" style="width:%s">%s</textarea>', $name, $field, $field, '90%', $value);
    }

    /**
     * This function provides dropdown for shortlink
     */
    public function settings_field_shortlink($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<select type="text" name="%s[%s]" id="%s"/>', $name, $field, $field);
      echo "<option value='Dropbox' " . (($values[$field] === 'Dropbox') ? 'selected="selected"' : '') . "/>Dropbox</option>";
      echo "<option value='Bitly' " . (($values[$field] === 'Bitly') ? 'selected="selected"' : '') . "/>Bitly</option>";
      echo "</select>";
    }

    /**
     * This function provides text inputs for the user folders
     */
    public function settings_field_shortlink_key($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<input type="text" name="%s[%s]" id="%s" value="%s" style="width:%s"/>', $name, $field, $field, esc_attr($values[$field]), '90%');
      echo '<br/><a href="https://bitly.com/a/sign_up" target="_blank">' . __('Sign up by Bitly', 'outofthebox') . '</a> ' . __('and', 'outofthebox') . ' <a href="https://bitly.com/a/your_api_key‎" target="_blank">' . __('get your apiKey', 'outofthebox') . '</a>';
    }

    /**
     * This function provides dropdown for thumbnails
     */
    public function settings_field_thumbnails($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<select type="text" name="%s[%s]" id="%s"/>', $name, $field, $field);
      echo "<option value='Dropbox' " . (($values[$field] === 'Dropbox') ? 'selected="selected"' : '') . "/>Dropbox</option>";
      echo "<option value='Out-of-the-Box' " . (($values[$field] === 'Out-of-the-Box') ? 'selected="selected"' : '') . "/>Out-of-the-Box</option>";
      echo "</select>";
    }

    /**
     * This function provides text inputs for the user folders
     */
    public function settings_field_userfoldername($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<input type="text" name="%s[%s]" id="%s" value="%s" style="width:%s"/>', $name, $field, $field, esc_attr($values[$field]), '90%');
      echo "<br/>" . __('You can use') . ': <em>' . '%user_login%, %user_email%, %display_name%, %ID% </em>';
    }

    public function settings_field_userfolder($args) {
      // Get the field name from the $args array
      $field = $args['field'];
      $name = $args['name'];
      $values = $args['values'];
      // Get the value of this setting
      // echo a proper input type="text"
      echo sprintf('<select type="text" name="%s[%s]" id="%s"/>', $name, $field, $field);
      echo "<option value='Yes' " . (($values[$field] === 'Yes') ? 'selected="selected"' : '') . "/>Yes</option>";
      echo "<option value='No' " . (($values[$field] === 'No') ? 'selected="selected"' : '') . "/>No</option>";
      echo "</select>";
    }

    /**
     * Menu Callback
     */
    public function OutoftheBox_SettingsPage() {
      if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'outofthebox'));
      }

      if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        update_option('out_of_the_box_lists', array());
      }

      $this->plugin_settings_tabs['systemsettings'] = __('System information', 'outofofthebox');

      $tab = isset($_GET['tab']) ? $_GET['tab'] : $this->general_settings_key;
      ?>
      <div class="wrap">
        <div class='left' style="min-width:400px; max-width:650px; padding: 0 20px 0 0; float:left">
          <?php
          $this->OutoftheBox_SettingsPage_Tabs();

          if ($tab === $this->general_settings_key) {

            $this->OutoftheBox_CheckDropboxApp();
          }

          if ($tab === $this->general_settings_key || $tab === $this->advanced_settings_key) {
            ?>

            <form method="post" action="options.php">
              <?php wp_nonce_field('update-options'); ?>
              <?php settings_fields($tab); ?>
              <?php do_settings_sections($tab); ?>
              <?php submit_button(); ?>
            </form>
            <?php if ($tab === $this->general_settings_key) { ?>

              <h3><?php _e('Shortcode', 'outofthebox'); ?></h3>
              <p><?php _e('Out-of-the-Box uses the following shortcode:', 'outofthebox'); ?> [outofthebox]. <?php _e('To make things easier for you, we added a <u>Shortcode button</u> in the MCE editor', 'outofthebox'); ?>.</p>

              <h3><?php _e('Documentation', 'outofthebox'); ?></h3>
              <p><a href='http://goo.gl/FxM4QN' title='Out of the Box documentation' target="_blank"><?php _e('Visit the Out-of-the-Box website', 'outofthebox'); ?></a> <?php _e('for documentation and installation details', 'outofthebox'); ?>.</p>

              <h3><?php _e('Support', 'outofthebox'); ?></h3>
              <p><?php _e('Discovered a bug or just need some help with the plugin?', 'outofthebox'); ?> <a href='http://goo.gl/rjuqhv' title='Out of the Box support' target="_blank"><?php _e('Visit the support page', 'outofthebox'); ?></a>.</p>

              <?php
            }
          } else {
            echo '<h3>' . __('Overview of your system settings', 'outofthebox') . '</h3>';
            echo $this->OutoftheBox_checkDependencies();
          }
          ?>
        </div>
        <?php
        if ($tab === $this->general_settings_key) {
          ?>

          <div class='right' style='float:left; width: 266px;'>
            <a href="http://goo.gl/JbV7pK" target="_blank">
              <img src="<?php echo plugins_url('css/images/Out-of-the-Box-Logo.png', __FILE__); ?>" title="Out-of-the-Box: a Dropbox plugin for Wordpress" width="266"/>
              <img src="<?php echo plugins_url('css/images/Use-your-Drive-Logo.png', __FILE__); ?>" title="Use-your-Drive: a Google Drive plugin for Wordpress" width="266" style="margin-top: -4px;"/>
            </a>
          </div>
          <?php
        }
        ?>
        <script type="text/javascript" >
          jQuery(document).ready(function($) {
            $('#shortlinks').change(function() {
              if ($(this).val() === 'Dropbox') {
                $(this).parent().parent().next().hide().next().hide();
              } else {
                $(this).parent().parent().next().show().next().show();
              }
            });
            $('#shortlinks').trigger('change');
          });
        </script>
      </div>
      <?php
    }

    function OutoftheBox_Filebrowser() {
      ?>
      <div class="wrap adminfilebrowser">
        <?php
        screen_icon('outofthebox');
        echo '<h2>' . __('File browser', 'outofthebox') . '</h2>';
        echo $this->OutoftheBox_Dropbox->createFromShortcode(array('mode' => 'files', 'upload' => '1', 'rename' => '1', 'delete' => '1', 'addfolder' => '1'));
        ?>
      </div>
      <?php
    }

    /**
     * Menu Tabs
     */
    function OutoftheBox_SettingsPage_Tabs() {
      $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->general_settings_key;

      screen_icon();
      echo '<h2 class="nav-tab-wrapper">';
      foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
      }
      echo '</h2>';
    }

    public function OutoftheBox_CheckDropboxApp() {
      $authorize = true;

      $current_url = parse_url(admin_url('admin.php?page=OutoftheBox_settings'));
      $can_do_own_auth = ($current_url['scheme'] === 'https' || $current_url['host'] === 'localhost') ? true : false;
      $use_own_app = ((!empty($this->general_settings['dropbox_app_key'])) && (!empty($this->general_settings['dropbox_app_secret']))) ? true : false;

      $appInfo = $this->OutoftheBox_Dropbox->setAppConfig();
      if (is_wp_error($appInfo)) {
        echo "<div id='message' class='error'><p>" . $appInfo->get_error_message() . "</p></div>";
        return false;
      }

      $page = isset($_GET["page"]) ? '?page=' . $_GET["page"] : '';
      $location = get_admin_url(null, 'admin.php' . $page);
      $redirectUrl = $this->OutoftheBox_Dropbox->setRedirectUri($location);
      $redirectMsg = '';
      if ($use_own_app) {
        if ($can_do_own_auth) {
          $redirectMsg = "<ul>
            <li>Add <strong><em>$redirectUrl</em></strong> to the <strong>OAuth redirect URIs</strong> in the <a href='https://www.dropbox.com/developers/apps/' target='_blank'>App Console</a></li>
          </ul>";
        } else {
          $redirectMsg = "<p>Because you don't mind using Out-of-the-Box without a SSL certificate, we will direct you via our site.</p><p><ul>
            <li>Add <strong><em>https://www.florisdeleeuw.nl:443/out-of-the-box/index.php</em></strong> to the <strong>OAuth redirect URIs</strong> in the <a href='https://www.dropbox.com/developers/apps/' target='_blank'>App Console</a></li>
          </ul></p>";
        }
      } else {
        $redirectMsg = "<p>We will direct you to Dropbox via our site.</p>";
      }
      $authorizebutton = "<input id='authorizeDropbox_button' type='submit' value='" . __('(Re) Authorize the Plugin!', 'useyourdrive') . "' class='button-primary'/>";
      $revokebutton = "<input id='revokeDropbox_button' type='submit' value='" . __('Revoke authorization', 'useyourdrive') . "' class='button-secondary'/>&nbsp;";

      // are we coming from dropbox's auth page?
      if (!empty($_GET['code'])) {
        $createToken = $this->OutoftheBox_Dropbox->createToken();

        if (is_wp_error($createToken)) {
          echo "<div id='message' class='error'><p>" . $createToken->get_error_message() . '</p><p>' . $redirectMsg . $authorizebutton . "</p></div>";
        } else {
          echo "<script type='text/javascript'>window.location.href = '" . $location . "';</script>";
        }

        $this->OutoftheBox_LoadSettings();
      } elseif (!empty($_GET['_token'])) {

        $newtoken = $_GET['_token'];
        $this->general_settings['dropbox_app_token'] = $newtoken;
        update_option($this->general_settings_key, $this->general_settings);

        $this->OutoftheBox_LoadSettings();
        $this->OutoftheBox_Dropbox->settings = get_option($this->general_settings_key);
        echo "<script type='text/javascript'>window.location.href = '" . $location . "';</script>";
      }

      $authUrl = $this->OutoftheBox_Dropbox->startWebAuth();

      if ($use_own_app && $can_do_own_auth) {
        $authUrl = $this->OutoftheBox_Dropbox->startWebAuth();
      } else {
        $encodedredirect = strtr(base64_encode($location), '+/=', '-_~');
        $authUrl = 'https://www.florisdeleeuw.nl:443/out-of-the-box/index.php?app_key=' . $appInfo['appInfo']->getKey() . '&app_secret=' . $appInfo['appInfo']->getSecret() . '&wp_redirect=' . $encodedredirect;
      }

      $hasToken = $this->OutoftheBox_Dropbox->loadToken();

      if (is_wp_error($hasToken)) {
        echo "<div id='message' class='error'><p>" . $hasToken->get_error_message() . '</p><p>' . $redirectMsg . $authorizebutton . "</p></div>";
      } else {

        $client = $this->OutoftheBox_Dropbox->startClient();
        $accountInfo = $this->OutoftheBox_Dropbox->getAccountInfo();

        if ($accountInfo === false) {
          $error = new WP_Error('broke', __("Plugin isn't linked to your Dropbox anymore... Please Reauthorize!", 'outofthebox'));
          echo "<div id='message' class='error'><p>" . $error->get_error_message() . '</p><p>' . $redirectMsg . $authorizebutton . "</p></div>";
        } else if (is_wp_error($accountInfo)) {
          $error = $accountInfo;
          echo "<div id='message' class='error'><p>" . $error->get_error_message() . '</p><p>' . $redirectMsg . $authorizebutton . "</p></div>";
        } else {
          $user = $accountInfo['display_name'];
          $email = $accountInfo['email'];
          $authorize = false;
          echo "<div id='message' class='updated'>
        <p>" . __('Out-of-the-Box is succesfully authorized and linked with dropbox account:', 'outofthebox') . " <strong>$user ($email)</strong></p><p>" . $revokebutton . $authorizebutton . "</p></div>";
        }
      }
      ?>
      <script type="text/javascript" >
        jQuery(document).ready(function($) {
          $('#authorizeDropbox_button').click(function() {
            window.location = '<?php echo $authUrl; ?>';
          });

          $('#revokeDropbox_button').click(function() {
            $.ajax({type: "POST",
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              data: {
                action: 'outofthebox-revoke'
              },
              success: function(response) {
                location.reload(true)
              },
              dataType: 'json'
            });
          });
        });
      </script>
      <?php
    }

    public function OutoftheBox_AdminNotice() {
      global $pagenow;
      if ($pagenow == 'index.php' || $pagenow == 'plugins.php') {
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
          echo '<div id="message" class="error"><p><strong>Out-of-the-Box - Error: </strong>' . __('You need at least PHP 5.3 if you want to use Out-of-the-Box', 'outofthebox') . '. ' .
          __('You are using:', 'outofthebox') . ' <u>' . phpversion() . '</u></p></div>';
        } elseif (!function_exists('curl_init')) {
          echo '<div id="message" class="error"><p><strong>Out-of-the-Box - Error: </strong>' . __("You don't have the cURL PHP extension installed (couldn't find function \"curl_init\"), please enable or install this extension", 'outofthebox') . '. ' .
          '</p></div>';
        }
      }
    }

    public function OutoftheBox_checkDependencies() {
      $check = array();

      //Check if we can use oAuth 2 authentication, we need SSL or localhost
      $current_url = parse_url(admin_url('admin.php?page=OutoftheBox_settings'));
      $can_do_auth = ($current_url['scheme'] === 'https' || $current_url['host'] === 'localhost') ? true : false;
      if ($can_do_auth) {
        if ($current_url['scheme'] === 'https')
          array_push($check, array('success' => true, 'warning' => false, 'value' => __('Using SSL', 'outofthebox'), 'description' => __('You are using a secure connection', 'outofthebox')));

        if ($current_url['host'] === 'localhost')
          array_push($check, array('success' => true, 'warning' => true, 'value' => __('Using SSL', 'outofthebox'), 'description' => __("You are using running a server on 'localhost'.", 'outofthebox') . ' ' . __("If your using this Plugin somewhere else and would like to have a secure connection, you probably need a SSL certificate.", 'outofthebox')));
      } else {
        array_push($check, array('success' => false, 'warning' => true, 'value' => __('Using SSL', 'outofthebox'), 'description' => __('SSL is required for authentication with the Dropbox API.', 'outofthebox') . " <a href='http://goo.gl/FxM4QN' title='Out of the Box documentation' target='_blank'>" . __('See our documentation how to obtain a SSL certificate or use Out-of-the-Box without one.', 'outofthebox') . "</a>"));
      }

      //Check if we can use CURL
      if (function_exists('curl_init')) {
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('cURL PHP extension', 'outofthebox'), 'description' => __('You have the cURL PHP extension installed', 'outofthebox')));
      } else {
        array_push($check, array('success' => false, 'warning' => false, 'value' => __('cURL PHP extension', 'outofthebox'), 'description' => __("You don't have the cURL PHP extension installed (couldn't find function \"curl_init\"), please enable or install this extension", 'outofthebox')));
      }

      //Check if temp dir is writeable
      $uploadir = wp_upload_dir();

      if (!is_writable($uploadir['path'])) {
        array_push($check, array('success' => false, 'warning' => false, 'value' => __('Is TMP directory writable?', 'outofthebox'), 'description' => __('TMP directory', 'outofthebox') . ' \'' . $uploadir['path'] . '\' ' . __('isn\'t writable. You are not able to upload files to Dropbox.', 'outofthebox') . ' ' . __('Make sure TMP directory is writable', 'outofthebox')));
      } else {
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('Is TMP directory writable?', 'outofthebox'), 'description' => __('TMP directory is writable', 'outofthebox')));
      }

      //Check if cache dir is writeable
      if (!file_exists(OUTOFTHEBOX_CACHEDIR)) {
        @mkdir(OUTOFTHEBOX_CACHEDIR, 0755);
      }

      if (!is_writable(OUTOFTHEBOX_CACHEDIR)) {
        @chmod(OUTOFTHEBOX_CACHEDIR, 0755);

        if (!is_writable(OUTOFTHEBOX_CACHEDIR)) {
          array_push($check, array('success' => false, 'warning' => false, 'value' => __('Is CACHE directory writable?', 'outofthebox'), 'description' => __('CACHE directory', 'outofthebox') . ' \'' . OUTOFTHEBOX_CACHEDIR . '\' ' . __('isn\'t writable. The gallery will load very slowly.', 'outofthebox') . ' ' . __('Make sure CACHE directory is writable', 'outofthebox')));
        } else {
          array_push($check, array('success' => true, 'warning' => false, 'value' => __('Is CACHE directory writable?', 'outofthebox'), 'description' => __('CACHE directory is now writable', 'outofthebox')));
        }
      } else {
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('Is CACHE directory writable?', 'outofthebox'), 'description' => __('CACHE directory is writable', 'outofthebox')));
      }

      //Check if cache index-file is writeable
      if (!is_readable(OUTOFTHEBOX_CACHEDIR . 'index')) {
        @file_put_contents(OUTOFTHEBOX_CACHEDIR . 'index', json_encode(array()));

        if (!is_readable(OUTOFTHEBOX_CACHEDIR . 'index')) {
          array_push($check, array('success' => false, 'warning' => false, 'value' => __('Is CACHE-index file writable?', 'outofthebox'), 'description' => __('-index file', 'outofthebox') . ' \'' . OUTOFTHEBOX_CACHEDIR . 'index' . '\' ' . __('isn\'t writable. The gallery will load very slowly.', 'outofthebox') . ' ' . __('Make sure CACHE-index file is writable', 'outofthebox')));
        } else {
          array_push($check, array('success' => true, 'warning' => false, 'value' => __('Is CACHE-index file writable?', 'outofthebox'), 'description' => __('CACHE-index file is now writable', 'outofthebox')));
        }
      } else {
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('Is CACHE-index file writable?', 'outofthebox'), 'description' => __('CACHE-index file is writable', 'outofthebox')));
      }

      //Check if we can use 64 bits integers
      if (strlen((string) PHP_INT_MAX) < 19) {
        $message = __("The Dropbox SDK uses 64-bit integers, but it looks like we're running on a version of PHP that doesn't support 64-bit integers", 'outofthebox') . " (PHP_INT_MAX=" . ((string) PHP_INT_MAX) . ").";
        $message .= __("Because you can still use the Dropbox SDK with 32-bit integers we disabled the 64-bit check", 'outofthebox');
        array_push($check, array('success' => true, 'warning' => true, 'value' => __('64-bit integers', 'outofthebox'), 'description' => $message));
      } else {
        $message = __("It looks like we're running on a version of PHP that does support 64-bit integers", 'outofthebox') . " (PHP_INT_MAX=" . ((string) PHP_INT_MAX) . ").";
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('64-bit integers', 'outofthebox'), 'description' => $message));
      }


      // Supported images
      $mime_types = array('image/jpeg', 'image/png', 'image/bmp', 'image/gif');
      $supported = '';
      $success = true;

      foreach ($mime_types as $mime_type) {
        $arg = array('mime_type' => $mime_type, 'methods' => array('resize', 'save'));
        $img_editor_test = false;

        if (function_exists('wp_image_editor_supports')) {
          $img_editor_test = wp_image_editor_supports($arg);
        }

        if ($img_editor_test === true) {
          $success = false;
        }

        $supported .= $mime_type . ': ' . (($img_editor_test === true) ? 'Yes' : 'No') . '<br/>';
      }

      array_push($check, array('success' => $success, 'warning' => true, 'value' => __('Can resize the following images', 'outofthebox'), 'description' => $supported . '<br/>' . __("If your server doesn't support resizing an image type, we try to use Dropbox own thumbnails", 'outofthebox')));

      //Check if we can use ZIP class
      if (class_exists('ZipArchive')) {
        $message = __("You can use the ZIP function", 'outofthebox');
        array_push($check, array('success' => true, 'warning' => false, 'value' => __('Download files as ZIP', 'outofthebox'), 'description' => $message));
      } else {
        $message = __("You cannot download files as ZIP", 'outofthebox');
        array_push($check, array('success' => true, 'warning' => true, 'value' => __('Download files as ZIP', 'outofthebox'), 'description' => $message));
      }

      // Create Table
      $html = '<table border="0" cellspacing="0" cellpadding="0">';

      foreach ($check as $row) {

        $color = ($row['success']) ? 'green' : 'red';
        $color = ($row['warning']) ? 'orange' : $color;

        $html .= '<tr style="vertical-align:top;"><td width="200" style="padding: 5px; color:' . $color . '"><strong>' . $row['value'] . '</strong></td><td style="padding: 5px;">' . $row['description'] . '</td></tr>';
      }

      $html .= '</table>';

      return $html;
    }

    /*
     * Add MCE buttons and script
     */

    public function OutoftheBox_ShortcodeButtonInit() {

      //Abort early if the user will never see TinyMCE
      if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
        return;

      //Add a callback to regiser our tinymce plugin
      add_filter("mce_external_plugins", array(&$this, "OutoftheBox_RegisterTinymcePlugin"));

      // Add a callback to add our button to the TinyMCE toolbar
      add_filter('mce_buttons', array(&$this, 'OutoftheBox_AddTinymceButton'));
    }

    //This callback registers our plug-in
    function OutoftheBox_RegisterTinymcePlugin($plugin_array) {
      $plugin_array['outofthebox'] = OUTOFTHEBOX_ROOTPATH . "/includes/OutoftheBox_tinymce.js";
      return $plugin_array;
    }

    //This callback adds our button to the toolbar
    function OutoftheBox_AddTinymceButton($buttons) {
      //Add the button ID to the $button array
      $buttons[] = "outofthebox";
      $buttons[] = "outofthebox_embedded";
      $buttons[] = "outofthebox_links";
      return $buttons;
    }

  }

}