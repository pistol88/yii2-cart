if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.cart = {
    init: function() {
        pistol88.cart.csrf = jQuery('meta[name=csrf-token]').attr("content");
        pistol88.cart.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
        
        jQuery(document).on('change', '.pistol88-cart-element-count', this.changeElementCount);
        jQuery(document).on('click', '.pistol88-cart-buy-button', this.addElement);
        jQuery(document).on('click', '.pistol88-cart-delete-button', this.deleteElement);
        jQuery(document).on('click', '.pistol88-arr', this.changeInputValue);
        jQuery(document).on('change', '.pistol88-cart-element-before-count', this.changeBeforeCartCount);

        jQuery(document).on('change', '.pistol88-cart-element-description', function() {
            console.log('changeElementDesription');
            jQuery(document).trigger("changeCartElementDescription", this);

            var input = jQuery(this);

            var data = {};
            data.CartElement = {};
            data.CartElement.id = jQuery(this).data('id');
            data.CartElement.description = jQuery(this).val();

            pistol88.cart.sendData(data, jQuery(input).data('href'));
        });

        return true;
    },
    jsonResult: null,
    csrf: null,
    csrf_param: null,
    changeBeforeCartCount: function() {
        var id = $(this).data('id');
        $('.pistol88-cart-buy-button'+id).data('count', $(this).val());
        $('.pistol88-cart-buy-button'+id).attr('count', $(this).val());
    },
    deleteElement: function() {
        jQuery(document).trigger("deleteCartElement", this);

        var link = this;
        var elementId = jQuery(this).data('id');

        pistol88.cart.sendData({elementId: elementId}, jQuery(this).attr('href'));

        if(lineSelector = jQuery(this).data('line-selector')) {
            jQuery(link).parents(lineSelector).last().hide('slow');
        }

        return false;
    },
    changeInputValue: function() {
        var val = parseInt(jQuery(this).siblings('input').val());
        var input = jQuery(this).siblings('input');
        
        if(jQuery(this).hasClass('pistol88-downArr')) {
            if(val <= 0) {
                return false;
            }
            jQuery(input).val(val-1);
        }
        else {
            jQuery(input).val(val+1);
        }
        
        jQuery(input).change();
        
        return false;
    },
    changeElementCount: function() {
        jQuery(document).trigger("changeCartElementCount", this);
        
        var input = jQuery(this);

        var data = {};
        data.CartElement = {};
        data.CartElement.id = jQuery(this).data('id');
        data.CartElement.count = jQuery(this).val();

        pistol88.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    addElement: function() {
        jQuery(document).trigger("addCartElement", this);

        var data = {};
        data.CartElement = {};
        data.CartElement.model = jQuery(this).data('model');
        data.CartElement.item_id = jQuery(this).data('id');
        data.CartElement.count = jQuery(this).data('count');
        data.CartElement.description = jQuery(this).data('description');

        pistol88.cart.sendData(data, jQuery(this).attr('href'));
        
        return false;
    },
    sendData: function(data, link) {
        if(!link) {
            link = '/cart/element/create';
        }

        jQuery(document).trigger("sendDataToCart", data);

        data[pistol88.cart.csrf_param] = pistol88.cart.csrf;

        jQuery.post(link, data,
            function(json) {
                if(json.result == 'fail') {
                    console.log(json.error);
                }
                else {
                    pistol88.cart.renderCart(json);
                }

            }, "json");
        
        return false;
    },
    renderCart: function(json) {
        jQuery('.pistol88-empty-cart, .pistol88-cart').replaceWith(json.elementsHTML);
        jQuery('.pistol88-cart-count').html(json.count);
        jQuery('.pistol88-cart-price').html(json.price);

        jQuery(document).trigger("renderCart", json);
        
        return true;
    },
};

pistol88.cart.init();