if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.cart = {
    init: function() {
        $(document).on('blur', '.pistol88-cart-element-count', this.changeElementCount);
        $(document).on('click', '.pistol88-cart-buy-button', this.addElement);
        $(document).on('click', '.pistol88-cart-delete-button', this.deleteElement);

        $(document).on('deleteCartElement', function(e, el) {
            $(el).parents('li.pistol88-cart-row').hide('slow');
        });

        $(document).on('changeCartElementCount', function(e, el) {
            $(el).css('opacity', '0.3');
        });
        
        $(document).on('deleteElement', function(e, el) {
            $(el).css('opacity', '0.3');
        });
        
        $(document).on('renderCart', function() {
            $('.pistol88-cart input').css('opacity', '1');
            $('.pistol88-cart').css('opacity', '1');
        });

        return true;
    },
    deleteElement: function() {
        $(document).trigger("deleteCartElement", this);
        
        var link = this;
        var elementId = $(this).data('id');
        
        $.post(
            $(link).attr('href'),
            {elementId: elementId},
            function(answer) {
                var json = $.parseJSON(answer);
                if(json.result == 'success') {
                    pistol88.cart.renderCart(json);
                }
                else {
                    alert(json.error);
                }
            }
        );

        return false;
    },
    changeElementCount: function() {
        $(document).trigger("changeCartElementCount", this);
        
        var input = $(this);
        
        var data = {};
        data.CartElement = {};
        data.CartElement.id = $(this).data('id');
        data.CartElement.count = $(this).val();

        $.post($(this).data('href'), data,
            function(json) {
                pistol88.cart.renderCart(json);
            }, "json");

        return false;
    },
    addElement: function() {
        $(document).trigger("addCartElement", this);

        var data = {};
        data.CartElement = {};
        data.CartElement.model = $(this).data('model');
        data.CartElement.item_id = $(this).data('id');

        pistol88.cart.sendData(data, $(this).attr('href'));
        
        return false;
    },
    sendData: function(data, link) {
        $(document).trigger("sendDataToCart", data);
        
        $.post(link, data,
        function(json) {
            pistol88.cart.renderCart(json);
        }, "json");
        
        return false;
    },
    renderCart: function(json) {
        $(document).trigger("renderCart", json);
        
        $('.pistol88-empty-cart, .pistol88-cart.dropdown').replaceWith(json.elementsHTML);
        $('.pistol88-cart-count').html(json.count);
        $('.pistol88-cart-price').html(json.price);

        return true;
    },
};

pistol88.cart.init();