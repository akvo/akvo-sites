<?php
class Elm_Plugin {
	/**
	 * @var scbOptions $settings Plugin settings.
	 */
	private $settings;
	private $emailCronJob = null;

	public function __construct($pluginFile) {
		$this->settings = new scbOptions(
			'ws_error_log_monitor_settings',
			$pluginFile,
			array(
				'widget_line_count' => 20,
				'strip_wordpress_path' => false,
				'send_errors_to_email' => '',
				'email_line_count' => 100,
				'email_interval' => 3600, //seconds
				'email_last_line_timestamp' => 0,
				'timestamp_format' => 'M d, H:i:s',
			)
		);

		Elm_DashboardWidget::getInstance($this->settings, $this);
		add_action('elm_settings_changed', array($this, 'updateEmailSchedule'));

		$this->emailCronJob = new scbCron(
			$pluginFile,
			array(
				'interval' => $this->settings->get('email_interval'),
				'callback' => array($this, 'emailErrors'),
			)
		);
	}

	/**
	 * @param scbOptions $newSettings
	 */
	public function updateEmailSchedule($newSettings) {
		if ( $newSettings->get('send_errors_to_email') == '' ) {
			$this->emailCronJob->unschedule();
		} else {
			$this->emailCronJob->reschedule(array('interval' => $newSettings->get('email_interval')));
		}
	}

	public function emailErrors() {
		if ( $this->settings->get('send_errors_to_email') == '' ) {
			//Can't send errors to email if no email address is specified.
			return;
		}

		$log = Elm_PhpErrorLog::autodetect();
		if ( is_wp_error($log) ) {
			trigger_error('Error log not detected', E_USER_WARNING);
			return;
		}

		$lines = $log->readLastLines($this->settings->get('email_line_count'), true);
		if ( is_wp_error($lines) ) {
			trigger_error('Error log is not accessible', E_USER_WARNING);
			return;
		}

		//Only include messages logged since the previous email.
		$logEntries = array();
		$foundNewMessages = false;
		$lastEmailTimestamp = $this->settings->get('email_last_line_timestamp');
		foreach($lines as $line) {
			$foundNewMessages = $foundNewMessages || ($line['timestamp'] > $lastEmailTimestamp);
			if ( $foundNewMessages ) {
				$logEntries[] = $line;
			}
		}

		if ( !empty($logEntries) ) {
			$subject = sprintf(
				'PHP errors logged on %s',
				site_url()
			);
			$body = sprintf(
				"New PHP errors have been logged on %s\nHere are the last %d lines from %s:\n\n",
				site_url(),
				count($logEntries),
				$log->getFilename()
			);

			$stripWordPressPath = $this->settings->get('strip_wordpress_path');
			$lastEntryTimestamp = time();//Fall-back value in case none of the new entries have a timestamp.
			foreach($logEntries as $logEntry) {
				if ( $stripWordPressPath ) {
					$logEntry['message'] = $this->stripWpPath($logEntry['message']);
				}
				if ( !empty($logEntry['timestamp']) ) {
					$body .= '[' . $this->formatTimestamp($logEntry['timestamp']). '] ';
					$lastEntryTimestamp = $logEntry['timestamp'];
				}
				$body .= $logEntry['message'] . "\n";
			}

			if ( wp_mail($this->settings->get('send_errors_to_email'), $subject, $body) ) {
				$this->settings->set('email_last_line_timestamp', $lastEntryTimestamp);
			} else{
				trigger_error('Failed to send an email, wp_mail() returned FALSE', E_USER_WARNING);
			}
		}
	}

	public function stripWpPath($string) {
		return str_replace(rtrim(ABSPATH, '/\\'), '', $string);
	}

	public function formatTimestamp($timestamp) {
		return gmdate($this->settings->get('timestamp_format'), $timestamp);
	}
}