<?php

class PostcardMailer extends ContactForm_Mailer {
	private $mailer;
	private $variables;

	public function __construct() {
		$this->mailer = contact_form::getInstance()->getMailerByName('mandrill');
	}
	
	/**
	 * Get localized name.
	 *
	 * @return string
	 */
	public function get_title() {
		return 'Postcards';
	}

	/**
	 * Prepare mailer for sending new message. This function
	 * is ideal place to prepare to initialize internal storage
	 * variables. No connections should be established at this
	 * point to avoid potential timeouts.
	 */
	public function start_message() {
		$this->mailer->start_message();
	}

	/**
	 * Generate image for specified variables.
	 *
	 * @return string
	 */
	public function generate_image() {
		$versions = array(
			'pomegranate'		=> _BASEPATH.'/site/images/postcard-1.jpg',
			'holiday'			=> _BASEPATH.'/site/images/postcard-2.jpg',
			'honey and apples'	=> _BASEPATH.'/site/images/postcard-3.jpg',
		);
		$variation = $this->variables['version'];
		$image = imagecreatefromjpeg($versions[$variation]);
		$font_regular = _BASEPATH.'/site/font/open_sans_regular.ttf';
		$font_bold = _BASEPATH.'/site/font/open_sans_bold.ttf';

		if (mb_strpos($this->variables['name'], ' ') == false)
			$name = array(' ', $this->variables['name']); else
			$name = explode(' ', $this->variables['name']);
		$text = explode(' ', $this->variables['blessing']);
		$text_pos_x = 38;
		$text_pos_y = 83;
		$text_box_width = 219;
		$text_box_height = 195;
		$size_text = 14;
		$size_name = 20;

		// create image to hold text
		$text_image = imagecreatetruecolor($text_box_width, $text_box_height);

		// allocate colors
		$color_text = imagecolorallocate($text_image, 0, 0, 0);
		$color_name = imagecolorallocate($image, 0, 0, 0);
		$color_back = imagecolorallocatealpha($text_image, 255, 255, 255, 127);

		// fill text area
		imagefill($text_image, 0, 0, $color_back);

		// calculate line height
		$data = imagettfbbox($size_text, 0, $font_regular, 'Test!');
		$line_height = $data[1] - $data[7];

		$pos_y = $line_height;
		$line = '';

		// draw text
		$painted = false;
		while (count($text) > 0) {
			$word = (empty($line) ? '' : ' ').array_shift($text);
			$box = imagettfbbox($size_text, 0, $font_regular, $line.$word);

			if ($box[4] > $text_box_width) {
				$box = imagettfbbox($size_text, 0, $font_regular, $line);
				imagettftext($text_image, $size_text, 0, $text_box_width - $box[4] - 5, $pos_y, $color_text, $font_regular, utf8_strrev($line));
				$pos_y += $line_height + 8;
				$line = trim($word);
				$painted = true;
			} else {
				$line .= $word;
				$painted = false;
			}
		}

		if (!$painted)
			imagettftext($text_image, $size_text, 0, $text_box_width - $box[4] - 5, $pos_y, $color_text, $font_regular, utf8_strrev($line));

		imagecopy($image, $text_image, $text_pos_x, $text_pos_y, 0, 0, $text_box_width, $text_box_height);
		imagedestroy($text_image);

		// draw name
		$box = imagettfbbox($size_name, 0, $font_bold, $name[0]);
		imagettftext($image, $size_name, 0, 530 - $box[4], 285, $color_name, $font_bold, utf8_strrev($name[0]));

		$box = imagettfbbox($size_name, 0, $font_bold, $name[1]);
		imagettftext($image, $size_name, 0, 530 - $box[4], 335, $color_name, $font_bold, utf8_strrev($name[1]));

		// print out the image
		$file_name = tempnam(sys_get_temp_dir(), 'postcard-');
		imagepng($image, $file_name);
		imagedestroy($image);

		return $file_name;
	}

	/**
	 * Finalize message and send it to specified addresses.
	 * 
	 * Note: Before sending, you *must* check if contact_form
	 * function detectBots returns false.
	 *
	 * @return boolean
	 */
	public function send() {
		$image = $this->generate_image();
		$this->mailer->attach_file($image, $this->variables['name'].'.png');
		unlink($image);

		return $this->mailer->send();
	}

	/**
	 * Set sender of message.
	 *
	 * @param string $address
	 * @param string $name
	 */
	public function set_sender($address, $name=null) {
		$this->mailer->set_sender($address, $name);
	}

	/**
	 * Add recipient for the message. Recipient name is optional.
	 *
	 * @param string $address
	 * @param string $name
	 */
	public function add_recipient($address, $name=null) {
		$this->mailer->add_recipient($address, $name);
	}

	/**
	 * Add recipient to carbon copy (CC) field. Name is optional.
	 *
	 * @param string $address
	 * @param string $name
	 */
	public function add_cc_recipient($address, $name=null) {
		$this->mailer->add_cc_recipient($address, $name);
	}

	/**
	 * Add recipient to blind carbon copy (BCC) field. Name is optional.
	 *
	 * @param string $address
	 * @param string $name
	 */
	public function add_bcc_recipient($address, $name=null) {
		$this->mailer->add_bcc_recipient($address, $name);
	}

	/**
	 * Set message subject.
	 *
	 * @param string $subject
	 */
	public function set_subject($subject) {
		$this->mailer->set_subject($subject);
	}

	/**
	 * Set variables to be replaced in subject and body.
	 *
	 * @param array $variables
	 */
	public function set_variables($variables) {
		$this->variables = $variables;
		$this->mailer->set_variables($variables);
	}

	/**
	 * Set message body. HTML body is optional.
	 *
	 * @param string $plain_body
	 * @param string $html_body
	 */
	public function set_body($plain_body, $html_body=null) {
		$this->mailer->set_body($plain_body, $html_body);
	}

	/**
	 * Attach file to message. Inline attachments will have image name
	 * set as "Content-ID". Inline files can be addressed in HTML body
	 * like this:
	 *
	 * <img src="cid:example_file.png">
	 *
	 * @param string $file_name
	 * @param string $attached_name
	 * @param boolean $inline
	 */
	public function attach_file($file_name, $attached_name=null, $inline=false) {
		$this->mailer->attach_file($file_name, $attached_name, $inline);
	}
}

?>
