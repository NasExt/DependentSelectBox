/**
 * DependentSelectBox
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */

(function ($) {
	$.fn.dependentSelectBox = function (options) {

		var dsb = this;
		dsb.timeout = [];
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
		 * @param parentElement
		 */
		this.process = function (e, parentElement, dependentSelect) {

			// Validate if signalLink exist
			var signalLink = dsb.getSignalLink(dependentSelect);
			if (signalLink == false) {
				return false;
			}

			// Send ajax request
			$.ajax(signalLink, {
				async: false,
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

							$.each(data.items, function (i, item) {
								var option = $('<option>')
									.attr('value', item.key).text(item.value);

								if (data.value !== null && item.key == data.value) {
									option.attr('selected', true);
								}

								option.appendTo($select);

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
		 * @param parentElement
		 * @returns {boolean}
		 */
		this.onChange = function (e, parentElement, dependentSelect) {
			dsb.process(e, parentElement, dependentSelect);
		};


		/**
		 * Event onKeyup
		 * @param e
		 * @param parentElement
		 * @returns {boolean}
		 */
		this.onKeyup = function (e, parentElement, dependentSelect) {
			// reset timeout
			var timeoutKey = dependentSelect.attr('id');
			if (dsb.timeout[timeoutKey] != undefined && dsb.timeout[timeoutKey] != false) {
				clearTimeout(dsb.timeout[timeoutKey]);
			}

			dsb.timeout[timeoutKey] = setTimeout(function () {
				dsb.process(e, parentElement, dependentSelect);
			}, dsb.settings.suggestTimeout);
		};

		/**
		 * Process
		 */
		return this.each(function () {
			var $dependentSelect = $(this);

			var parents = $($dependentSelect).data(dsb.settings.dataParentsName);
			$.each(parents, function (name, id) {
				var parentElement = $('#' + id);

				if (parentElement.length > 0) {
					if (parentElement.prop('type') === 'text' || parentElement.prop('nodeName').toLowerCase() === 'textarea') {
						$(parentElement).on("keyup", function (e) {
							dsb.onKeyup(e, $(this), $dependentSelect);
						});
					} else {
						$(parentElement).on("change", function (e) {
							dsb.onChange(e, $(this), $dependentSelect);
						});
					}
				}
			});
		});
	}
})(jQuery);
