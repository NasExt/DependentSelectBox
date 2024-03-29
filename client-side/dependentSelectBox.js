/**
 * DependentSelectBox
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */

(function ($) {
	$.fn.dependentSelectBox = function (options, listener) {

		var callback = function () {};
		if(typeof( options ) === 'function' ) {
			callback = options;
			options = null;
		}
		if(typeof( listener ) === 'function' ) {
			callback = listener;
		}

		var dsb = this;
		dsb.timeout = [];
		dsb.settings = $.extend({
			suggestTimeout: 350,
			dataLinkName: 'dependentselectbox',
			dataParentsName: 'dependentselectboxParents',
			dataParamsName: 'dependentselectboxParams'
		}, options);


		/**
		 * Get link to signal
		 * @param element
		 * @returns {*}
		 */
		this.getSignalLink = function (element) {
			var signalLink = element.data(dsb.settings.dataLinkName);
			var params = element.data(dsb.settings.dataParamsName);

			if (signalLink === undefined) {
				return false;
			}

			$.each(params, function (name, id) {
				var paramElement = $('#' + id);

				if (paramElement.length > 0) {
					var val;

					if (paramElement.prop('type') === 'checkbox') {
						val = paramElement.prop('checked') ? 1 : 0;

					} else {
						val = $(paramElement).val();
						if (!val) {
							return;
						}
					}


					if (val instanceof Array) {
						$.each(val, function (key, value) {
							signalLink += '&' + name + '[]=' + value;
						});

					} else {
						signalLink += '&' + name + '=' + val;
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
			// skip cascaded data loading if parentEl is not yet set, empty dependentSelect & add empty prompt
			var parentElVal = parentElement.val();
			if (!parentElVal) {
				dependentSelect.empty().append('<option value="" />').change();
				dependentSelect.selectpicker && dependentSelect.selectpicker('refresh');
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

						// prompt set
						if (data.prompt !== false && data.prompt !== undefined) {
							$('<option>').attr('value', '').text(data.prompt).appendTo($select);
						}

						if (Object.keys(data.items).length > 0) {
							// set disabled, when control haven not choices
							if (data.disabledWhenEmpty === true) {
								$select.prop('disabled', false);
							}

							$.each(data.items, function (i, item) {

								if (typeof item.value === 'object') {
									var otpGroup = $('<optgroup>')
										.attr('label', item.key);

									$.each(item.value, function (objI, objItem) {
										var option = $('<option>')
											.attr('value', objI).text(objItem.value);
										if (data.value !== null && objI == data.value) {
											option.attr('selected', true);
										}
										otpGroup.append(option);
									});
									otpGroup.appendTo($select);
								}
								else {
									var option = $('<option>')
										.attr('value', item.key).text(item.value);

									if ('attributes' in item) {
										$.each(item.attributes, function (attr, attrValue) {
											option.attr(attr, attrValue);
										});
									}

									if (data.value !== null && item.key == data.value) {
										option.attr('selected', true);
									}

									option.appendTo($select);
								}



							});
						} else {
							if (data.disabledWhenEmpty) {
								$select.prop('disabled', true);
							}
						}

						$select.change();
					}
				},
				complete: callback
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
	};
})(jQuery);
