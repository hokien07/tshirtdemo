jQuery('.single_add_to_cart_button').click(function(e) {
	e.preventDefault();
	// jQuery(this).addClass('adding-cart');
	var product_id = jQuery(this).val();
	var quantity = jQuery('input[name="quantity"]').val();
  var color = jQuery('select[name="attribute_color"]').val();
  var size = jQuery('select[name="attribute_size"]').val();
  var price = jQuery('input[name="price_change"]').val();
  var img = jQuery('input[name="image_change"]').val();
  var product_title = jQuery('input[name="name_change"]').val();
	// jQuery('.cart-dropdown-inner').empty();

	jQuery.ajax ({
		url: crispshop_ajax_object.ajax_url,
		type:'POST',
		data:{
    'action': 'crispshop_add_cart_single',
    'product_id': product_id,
    'quantity' : quantity,
    'color': color,
    'price' :price,
    'img': img,
    'product_title': product_title
    },
		success:function(results) {
      console.log(results);
			// jQuery('.cart-dropdown-inner').append(results);
			// var cartcount = jQuery('.item-count').html();
			// jQuery('.cart-totals span').html(cartcount);
			// jQuery('.single_add_to_cart_button').removeClass('adding-cart');
			// jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
			// jQuery('.cart-dropdown').addClass('show-dropdown');
      //       setTimeout(function () {
      //           jQuery('.cart-dropdown').removeClass('show-dropdown');
      //       }, 3000);
		}
	});
});
