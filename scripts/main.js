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
	self.forms = null;
	self.button = null;

	/**
	 * Complete object initialization.
	 */
	self._init = function() {
		self.container = $('div#postcard_container');
		self.button = $('div.postcard_bottom button[type=button]');
		self.forms = self.container.children('div');

		self.forms.click(self._handleFormClick);
		self.button.click(self._handleSubmit);
	}

	/**
	 * Handle clicking on form.
	 *
	 * @param object event
	 */
	self._handleFormClick = function(event) {
		var form_container = $(this);

		// remove active class
		var affected_forms = self.forms.not(form_container);

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

		var container = self.forms.filter('.active');
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
