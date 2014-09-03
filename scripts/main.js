/**
 * Main JavaScript
 * Site Name
 *
 * Copyright (c) 2014. by Way2CU, http://way2cu.com
 * Authors:
 */

var Caracal = Caracal || {};


function PostCards() {
	var self = this;

	self.container = null;
	self.form_containers = null;
	self.button = null;
	self.checkbox = null;

	self.iframe = null;
	self.background = null;

	/**
	 * Complete object initialization.
	 */
	self._init = function() {
		self.container = $('div#postcard_container');
		self.button = $('div.postcard_bottom button[type=button]');
		self.checkbox = $('div.postcard_bottom input[type=checkbox]');
		self.form_containers = self.container.children('div');
		self.forms = self.container.find('form');

		// connect events
		self.form_containers.click(self._handleFormClick);
		self.button.click(self._handleSubmit);
		self.forms.on('dialog-show', self._handleDialogShow);
	}

	/**
	 * Show tranzilla donation page.
	 */
	self.showTranzilla = function() {
		var form = $(this);
		var data = {};
		var url = 'https://direct.tranzila.com/<account>/iframe.php?';

		if (self.iframe == null) {
			self.iframe = $('<iframe>');
			self.background = $('<div>');

			// configure background
			self.background
				.addClass('iframe-background')
				.click(self._handleBackgroundClick)
				.appendTo($('body'));

			// configure iframe
			self.iframe
				.attr('id', 'tranzila')
				.attr('frameborder', 0)
				.appendTo($('body'));
		}

		// prepare params for tranzilla
		var params = {
			lang: 'he',
			sum: 100,
			contact: '',
			phone: '',
			cred_type: 8,
			currency: 1
		};

		self.iframe.attr('src', url + $.param(params));

		// show iframe
		setTimeout(function() {
			self.background.addClass('visible');
			self.iframe.addClass('visible');
		}, 50);
	};

	/**
	 * Handle clicking outside of I frame.
	 */
	self._handleBackgroundClick = function(event) {
		event.preventDefault();

		// hide elements
		self.iframe.removeClass('visible');
		self.background.removeClass('visible');
	};

	/**
	 * Handle successful submission.
	 *
	 * @param object event
	 * @return boolean
	 */
	self._handleDialogShow = function(event) {
		var result = true;  // allow showing dialog by default

		if (self.checkbox.is(':checked')) {
			result = false;  // we are showing tranzilla, no need for thank you
			self.showTranzilla();
		}

		return result;
	};

	/**
	 * Handle clicking on form.
	 *
	 * @param object event
	 */
	self._handleFormClick = function(event) {
		var form_container = $(this);

		// remove active class
		var affected_forms = self.form_containers.not(form_container);

		affected_forms.removeClass('active left right');
		affected_forms.eq(0).addClass('left');
		affected_forms.eq(1).addClass('right');
		form_container
			.removeClass('left right')
			.addClass('active');
	};

	/**
	 * Handle clicking on send button.
	 *
	 * @param object event
	 */
	self._handleSubmit = function(event) {
		// prevent default behavior
		event.preventDefault();

		var container = self.form_containers.filter('.active');
		var current_form = container.find('form');
		var field = current_form.find('input[name=version]');

		field.val(container.data('name'));
		current_form.submit();
	};

	// finalize object
	self._init();
}

function on_site_load() {
	Caracal.postcards = new PostCards();
}

$(on_site_load);
