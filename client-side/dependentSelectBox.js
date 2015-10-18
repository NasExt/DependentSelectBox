/**
 * DependentSelectBox
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */

(function ($) {
	$.fn.dependentSelectBox = function (options) {

		var dsb = this;
		dsb.timeout = false;
		dsb.settings = $.extend({
			suggestTimeout: 350,
			dataLinkName: 'dependentselectbox',
			dataParentsName: 'dependentselectboxParents'
		}, options);


		/**
		 * Get link to signal
		 * @param element
		 * @returns {*}
		 */
		this.getSignalLink = function (element) {
			var signalLink = element.data(dsb.settings.dataLinkName);
			var parents = element.data(dsb.settings.dataParentsName);

			if (signalLink == undefined) {
				return false;
			}

			$.each(parents, function (name, id) {
				var parentElement = $('#' + id);
				if (parentElement.length > 0) {
					var val = $(parentElement).val();
					if (val) {
						signalLink = signalLink + '&' + name + '=' + val;
					}

				}
			});

			return signalLink;
		};


		/**
		 * process
		 * @param e
		 * @param childInput
		 */
		this.process = function (e, childInput) {

			// Validate if signalLink exist
			var signalLink = dsb.getSignalLink(childInput);
			if (signalLink == false) {
				return false;
			}

			// Send ajax request
			$.ajax(signalLink, {
				success: function (payload) {
					var data = payload.dependentselectbox;
					if (data !== undefined) {
						var $select = $('#' + data.id);
						$select.empty();

						if (data.prompt != false) {
							$('<option>')
								.attr('value', '').text(data.prompt)
								.appendTo($select);
						}

						if (Object.keys(data.items).length > 0) {

							if (data.disabledWhenEmpty) {
								$select.prop('disabled', false);
							}

							$.each(data.items, function (key, value) {
								$('<option>')
									.attr('value', key).text(value)
									.appendTo($select);

							});
						} else {
							if (data.disabledWhenEmpty) {
								$select.prop('disabled', true);
							}
						}

						$select.change();
					}
				}
			});
		};


		/**
		 * Event onChange
		 * @param e
		 * @param childInput
		 * @returns {boolean}
		 */
		this.onChange = function (e, childInput) {
			dsb.process(e, childInput);
		};


		/**
		 * Event onKeyup
		 * @param e
		 * @param childInput
		 * @returns {boolean}
		 */
		this.onKeyup = function (e, childInput) {
			// reset timeout
			if (dsb.timeout != false) clearTimeout(dsb.timeout);

			dsb.timeout = setTimeout(function () {
				dsb.process(e, childInput);

			}, dsb.settings.suggestTimeout);
		};


		/**
		 * Process
		 */
		return this.each(function () {
			var $input = $(this);

			var parents = $($input).data(dsb.settings.dataParentsName);
			$.each(parents, function (name, id) {
				var parentElement = $('#' + id);

				if (parentElement.length > 0) {
					if (parentElement.prop('type') === 'text' || parentElement.prop('nodeName').toLowerCase() === 'textarea') {
						$(parentElement).on("keyup", function (e) {
							dsb.onChange(e, $input);
						});
					} else {
						$(parentElement).on("change", function (e) {
							dsb.onChange(e, $input);
						});
					}
				}
			});
		});
	}
})(jQuery);
