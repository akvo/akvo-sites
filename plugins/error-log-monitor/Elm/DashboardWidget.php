<?php
class Elm_DashboardWidget {
	private $widgetId = 'ws_php_error_log';
	private $requiredCapability = 'manage_options';

	/**
	 * @var scbOptions $settings Plugin settings.
	 */
	private $settings;
	/**
	 * @var Elm_plugin $plugin A reference to the main plugin object.
	 */
	private $plugin;

	private function __construct($settings, $plugin) {
		$this->settings = $settings;
		$this->plugin = $plugin;
		add_action('wp_dashboard_setup', array($this, 'registerWidget'));
	}

	public function registerWidget() {
		if ( current_user_can($this->requiredCapability) ) {
			wp_add_dashboard_widget(
				$this->widgetId,
				'PHP Error Log',
				array($this, 'displayWidgetContents'),
				array($this, 'handleSettingsForm')
			);
		}
	}

	public function displayWidgetContents() {
		$log = Elm_PhpErrorLog::autodetect();

		if ( is_wp_error($log) ) {
			$this->displayConfigurationHelp($log->get_error_message());
			return;
		}

		$doClearLog =  isset($_GET['elm-action']) && ($_GET['elm-action'] = 'clear-log')
					&& check_admin_referer('clear-log') && current_user_can($this->requiredCapability);
		if ( $doClearLog ) {
			$log->clear();
			echo '<p><strong>Log cleared.</strong></p>';
		}

		$lines = $log->readLastLines($this->settings->get('widget_line_count'), true);
		if ( is_wp_error($lines) ) {
			printf('<p>%s</p>', $lines->get_error_message());
		} else if ( empty($lines) ) {
			echo '<p>The log file is empty.</p>';
		} else {
			echo '<table class="widefat"><tbody>';
			$isOddRow = false;
			foreach ($lines as $line) {
				$isOddRow = !$isOddRow;
				if ( $this->settings->get('strip_wordpress_path') ) {
					$line['message'] = $this->plugin->stripWpPath($line['message']);
				}

				printf(
					'<tr%s><td style="white-space:nowrap;">%s</td><td>%s</td></tr>',
					$isOddRow ? ' class="alternate"' : '',
					!empty($line['timestamp']) ? $this->plugin->formatTimestamp($line['timestamp']) : '',
					esc_html($line['message'])
				);
			}
			echo '</tbody></table>';

			echo '<p>';
			printf(
				'Log file: %s (%s) ',
				esc_html($log->getFilename()),
				$this->formatByteCount($log->getFileSize(), 2)
			);
			printf(
				'<a href="%s" class="button" onclick="return confirm(\'%s\');">%s</a>',
				wp_nonce_url(admin_url('/index.php?elm-action=clear-log'), 'clear-log'),
				'Are you sure you want to clear the error log?',
				'Clear Log'
			);

			echo '</p>';
		}
	}

	private function displayConfigurationHelp($problem) {

		$exampleCode = "ini_set('log_errors', 'On');\n" . "ini_set('error_log', '/full/path/to/php-errors.log');";
		?>
		<p>
			<strong><?php echo $problem; ?></strong>
		</p>

		<p>
			To enable error logging, create an empty file named "php-errors.log".
			Place it in a directory that is not publicly accessible (preferably outside
			your web root) and ensure it is writable by the web server.
			Then add the following code to <code>wp-config.php</code>:
		</p>

		<pre><?php echo $exampleCode; ?></pre>

		<p>
			See also: <a href="http://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Log">Editing wp-config.php</a>,
			<a href="http://digwp.com/2009/07/monitor-php-errors-wordpress/">3 Ways To Monitor PHP Errors</a>
		</p>
		<?php
	}

	public function handleSettingsForm() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) && is_array($_POST[$this->widgetId]) ) {
			$formInputs = $_POST[$this->widgetId];

			$this->settings->set('widget_line_count', intval($formInputs['widget_line_count']));
			if ( $this->settings->get('widget_line_count') <= 0 ) {
				$this->settings->set('widget_line_count', $this->settings->get_defaults('widget_line_count'));
			}

			$this->settings->set('strip_wordpress_path', isset($formInputs['strip_wordpress_path']));
			$this->settings->set('send_errors_to_email', trim(strval($formInputs['send_errors_to_email'])));

			$this->settings->set('email_interval', intval($formInputs['email_interval']));
			if ( $this->settings->get('email_interval') <= 60 ) {
				$this->settings->set('email_interval', $this->settings->get_defaults('email_interval'));
			}

			do_action('elm_settings_changed', $this->settings);
		}

		printf(
			'<p><label>%s <input type="text" name="%s[widget_line_count]" value="%s" size="5"></label></p>',
			'Number of lines to show:',
			esc_attr($this->widgetId),
			esc_attr($this->settings->get('widget_line_count'))
		);

		printf(
			'<p><label><input type="checkbox" name="%s[strip_wordpress_path]"%s> %s</label></p>',
			esc_attr($this->widgetId),
			$this->settings->get('strip_wordpress_path') ? ' checked="checked"' : '',
			'Strip WordPress root directory from log messages'
		);

		printf(
			'<p>
				<label for="%1$s-send_errors_to_email">%2$s</label>
				<input type="text" class="widefat" name="%1$s[send_errors_to_email]" id="%1$s-send_errors_to_email" value="%3$s">
			</p>',
			esc_attr($this->widgetId),
			'Periodically email logged errors to:',
			$this->settings->get('send_errors_to_email')
		);

		printf(
			'<p><label>%s <select name="%s[email_interval]">',
			'How often to send email (max):',
			esc_attr($this->widgetId)
		);
		$intervals = array(
			'Every 10 minutes' => 10*60,
			'Every 15 minutes' => 15*60,
			'Every 30 minutes' => 30*60,
			'Hourly'           => 60*60,
			'Daily'            => 24*60*60,
			'Weekly'           => 7*24*60*60,
		);
		foreach($intervals as $name => $interval) {
			printf(
				'<option value="%d"%s>%s</option>',
				$interval,
				($interval == $this->settings->get('email_interval')) ? ' selected="selected"' : '',
				$name
			);
		}
		echo '</select></label></p>';
	}

	/**
	 * Convert an amount of data in bytes to a more human-readable format like KiB or MiB.
	 *
	 * @link http://www.php.net/manual/en/function.filesize.php#91477
	 * @param int $bytes
	 * @param int $precision
	 * @return string
	 */
	private function formatByteCount($bytes, $precision = 2) {
		$units = array('bytes', 'KiB', 'MiB', 'GiB', 'TiB'); //SI units.

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$size = $bytes / pow(1024, $pow);

		return round($size, $precision) . ' ' . $units[$pow];
	}

	public static function getInstance($settings, $plugin) {
		static $instance = null;
		if ( $instance === null ) {
			$instance = new self($settings, $plugin);
		}
		return $instance;
	}
}