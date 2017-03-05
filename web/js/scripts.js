if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.cart = {
    init: function () {

        $cartElementsCount = '[data-role=cart-element-count]';
        $buyElementButton = '[data-role=cart-buy-button]';
        $deleteElementButton = '[data-role=cart-delete-button]';
        $truncateCartButton = '[data-role=truncate-cart-button]';

        pistol88.cart.csrf = jQuery('meta[name=csrf-token]').attr("content");
        pistol88.cart.csrf_param = jQuery('meta[name=csrf-param]').attr("content");

        $(document).on('change', $cartElementsCount, function () {

            var self = this,
                url = $(self).data('href');

            if ($(self).val() < 0) {
                $(self).val('0');
                return false;
            }

            cartElementId = $(self).data('id');
            cartElementCount = $(self).val();

            pistol88.cart.changeElementCount(cartElementId, cartElementCount, url);

        });

        $(document).on('click', $buyElementButton, function () {

            var self = this,
                url = $(self).data('url');

            $itemModelName = $(self).data('model');
            $itemId = $(self).data('id');
            $itemCount = $(self).data('count');
            $itemPrice = $(self).data('price');
            $itemOptions = $(self).data('options');

            pistol88.cart.addElement($itemModelName, $itemId, $itemCount, $itemPrice, $itemOptions, url);

        });

        $(document).on('click', $truncateCartButton, function () {

            var self = this,
                url = $(self).data('url');

            pistol88.cart.truncate(url);
        });

        $(document).on('click', $deleteElementButton, function (e) {

            e.preventDefault();

            var self = this,
                url = $(self).data('url'),
                elementId = $(self).data('id');

            pistol88.cart.deleteElement(elementId, url);

            if (lineSelector = $(self).data('line-selector')) {
                $(self).parents(lineSelector).last().hide('slow');
            }

        });
        $(document).on('click', '.pistol88-arr', this.changeInputValue);
        $(document).on('change', '.pistol88-cart-element-before-count', this.changeBeforeElementCount);
        $(document).on('change', '.pistol88-option-values-before', this.changeBeforeElementOptions);
        $(document).on('change', '.pistol88-option-values', this.changeElementOptions);

        return true;
    },
    elementsListWidgetParams: [],
    jsonResult: null,
    csrf: null,
    csrf_param: null,
    changeElementOptions: function () {
        jQuery(document).trigger("changeCartElementOptions", this);

        var id = $(this).data('id');

        var options = {};

        if ($(this).is('select')) {
            var els = $('.pistol88-cart-option' + id);
        }
        else {
            var els = $('.pistol88-cart-option' + id + ':checked');
            console.log('radio');
        }

        $(els).each(function () {
            var name = $(this).data('id');

            options[id] = $(this).val();
        });

        var data = {};
        data.CartElement = {};
        data.CartElement.id = id;
        data.CartElement.options = JSON.stringify(options);

        pistol88.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    changeBeforeElementOptions: function () {
        var id = $(this).data('id');
        var filter_id = $(this).data('filter-id');
        var buyButton = $('.pistol88-cart-buy-button' + id);

        var options = $(buyButton).data('options');
        if (!options) {
            options = {};
        }

        options[filter_id] = $(this).val();

        $(buyButton).data('options', options);
        $(buyButton).attr('data-options', options);

        $(document).trigger("beforeChangeCartElementOptions", options);

        return true;
    },
    deleteElement: function (elementId, url) {

        pistol88.cart.sendData({elementId: elementId}, url);

        return false;
    },
    changeInputValue: function () {
        var val = parseInt(jQuery(this).siblings('input').val());
        var input = jQuery(this).siblings('input');

        if (jQuery(this).hasClass('pistol88-downArr')) {
            if (val <= 0) {
                return false;
            }
            jQuery(input).val(val - 1);
        }
        else {
            jQuery(input).val(val + 1);
        }

        jQuery(input).change();

        return false;
    },
    changeBeforeElementCount: function () {
        if ($(this).val() <= 0) {
            $(this).val('0');
        }

        var id = $(this).data('id');
        var buyButton = $('.pistol88-cart-buy-button' + id);
        $(buyButton).data('count', $(this).val());
        $(buyButton).attr('data-count', $(this).val());

        return true;
    },
    changeElementCount: function (cartElementId, cartElementCount, url) {

        var data = {};
        data.CartElement = {};
        data.CartElement.id = cartElementId;
        data.CartElement.count = cartElementCount;

        pistol88.cart.sendData(data, url);

        return false;
    },
    addElement: function (itemModelName, itemId, itemCount, itemPrice, itemOptions, url) {

        var data = {};
        data.CartElement = {};
        data.CartElement.model = itemModelName;
        data.CartElement.item_id = itemId;
        data.CartElement.count = itemCount;
        data.CartElement.price = itemPrice;
        data.CartElement.options = itemOptions;

        pistol88.cart.sendData(data, url);

        return false;
    },
    truncate: function (url) {
        pistol88.cart.sendData({}, url);
        return false;
    },
    sendData: function (data, link) {
        if (!link) {
            link = '/cart/element/create';
        }

        jQuery(document).trigger("sendDataToCart", data);

        data.elementsListWidgetParams = pistol88.cart.elementsListWidgetParams;
        data[pistol88.cart.csrf_param] = pistol88.cart.csrf;

        jQuery('.pistol88-cart-block').css({'opacity': '0.3'});
        jQuery('.pistol88-cart-count').css({'opacity': '0.3'});
        jQuery('.pistol88-cart-price').css({'opacity': '0.3'});

        jQuery.post(link, data,
            function (json) {
                jQuery('.pistol88-cart-block').css({'opacity': '1'});
                jQuery('.pistol88-cart-count').css({'opacity': '1'});
                jQuery('.pistol88-cart-price').css({'opacity': '1'});

                if (json.result == 'fail') {
                    console.log(json.error);
                }
                else {
                    pistol88.cart.renderCart(json);
                }

            }, "json");

        return false;
    },
    renderCart: function (json) {
        if (!json) {
            var json = {};
            jQuery.post('/cart/default/info', {},
                function (answer) {
                    json = answer;
                }, "json");
        }

        jQuery('.pistol88-cart-block').replaceWith(json.elementsHTML);
        jQuery('.pistol88-cart-count').html(json.count);
        jQuery('.pistol88-cart-price').html(json.price);

        jQuery(document).trigger("renderCart", json);

        return true;
    },
};

pistol88.cart.init();
