;(function(jQuery) {
	jQuery.fn.notifySubscribe = function(options) {
		var defaults = {};
		var options = jQuery.extend(defaults, options);
		return this.each(function() {
			var element = jQuery(this);
            var url = element.attr('data-rel');
            var onClickedElement = function() {
                jQuery.get(url + '&subscriber=' + element.parents('.subscribe-parent').find('.subscriber').hide().val(), function(data) {
                    jQuery('.subscriber').val(element.parents('.subscribe-parent').find('.subscriber').val()).hide();
                    var responseElement = jQuery(data).find('.tx-notify.subscribe.component');
                    if (element.hasClass('button')) {
                        element.html(responseElement.html().trim()).attr('class', responseElement.attr('class'));
                    } else if (element.hasClass('splitbutton')) {
                        var subscribeButton = element.parent().find('[data-role="subscribe"]');
                        var unsubscribeButton = element.parent().find('[data-role="unsubscribe"]');
                        if (element.attr('data-role') == 'subscribe') {
                            subscribeButton.addClass('btn-success');
                            unsubscribeButton.removeClass('btn-danger');
                        } else {
                            unsubscribeButton.addClass('btn-danger');
                            subscribeButton.removeClass('btn-success');
                        };
                    } else if (element.hasClass('image')) {
                        if (element.attr('data-role') == 'subscribe') {
                            element.attr('data-role', 'unsubscribe');
                        } else {
                            element.attr('data-role', 'subscribe');
                        };
                        element.find('.current').addClass('alt-image');
                        element.find('.alt-image').not('.current').removeClass('alt-image').addClass('current').show();
                        element.find('.current.alt-image').removeClass('current').hide();
                    } else if (element.hasClass('checkbox')) {
                        responseElement = jQuery(data).find('.tx-notify.subscribe.component').parent();
                        var parent = element.parent();
                        parent.html(responseElement.html()).attr('class', responseElement.attr('class'));
                        element = parent.find('.tx-notify.subscribe.component');
                        element.click(onClickedElement);
                        parent.append(element);
                    } else if (element.hasClass('radio')) {
                            // suppress; radios do not require an update since both states are exposed
                    } else {
                        element.html(responseElement.html()).attr('class', responseElement.attr('class'));
                    };
                });
            };
            element.live('click', onClickedElement);
		});
	};
    jQuery(document).ready(function($) {
        $('.tx-notify.subscribe.component').notifySubscribe();
    });
})(jQuery);

