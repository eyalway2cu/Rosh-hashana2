<?php

use Core\Module;

require_once('units/mailer.php');


class pkatan extends Module {
	private static $_instance;
	private $mailer;

	/**
	 * Constructor
	 */
	protected function __construct() {
		parent::__construct(__FILE__);

		if (class_exists('contact_form')) {
			$contact_form = contact_form::getInstance();
			$this->mailer = new PostcardMailer();

			$contact_form->registerMailer('postcard', $this->mailer);
		}
	}

	/**
	 * Public function that creates a single instance
	 */
	public static function getInstance() {
		if (!isset(self::$_instance))
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Transfers control to module functions
	 *
	 * @param array $params
	 * @param array $children
	 */
	public function transferControl($params = array(), $children = array()) {
		if (isset($params['action']))
			switch ($params['action']) {
				case 'test':
					$this->mailer->set_variables(array(
						'version'	=> 'holiday',
						'name'		=> 'זרובבל אלמליאך',
						'blessing'	=> 'לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית קולהע צופעט למרקוח איבן איף, ברומץ כלרשט מיחוצים. קלאצי קולהע צופעט למרקוח איבן איף, ברומץ כלרשט מיחוצים. קלאצי סחטיר בלובק. תצטנפל בלינדו למרקל אס לכימפו, דול, צוט ומעיוט - לפתיעם ברשג - ולתיעם גדדיש. קוויז דומור ליאמום בלינך רוגצה.'
					));
					$image = $this->mailer->generate_image();

					header('Content-type: image/png');
					print file_get_contents($image);

					break;
			}
	}

	/**
	 * Event triggered upon module initialization
	 */
	public function onInit() {
	}

	/**
	 * Event triggered upon module deinitialization
	 */
	public function onDisable() {
	}
}
