<?php
function encrypt($sData, $secretKey)
    {
        $sResult = '';
        for ($i = 0; $i < strlen($sData); $i++) {
            $sChar = substr($sData, $i, 1);
            $sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
            $sChar = chr(ord($sChar) + ord($sKeyChar));
            $sResult .= $sChar;
        }
        return encode_base64($sResult);
    }

    function encode_base64($sData)
    {
        $sBase64 = base64_encode($sData);
        return str_replace('=', '', strtr($sBase64, '+/', '-_'));
    }


function giua($a, $b, $c) {
	    $a = explode($a, $c);
	    $b = explode($b, $a[1]);
	    return $b[0];
	}
function s3_file_name($url)
  {
      $path_name = giua('/files/', '/products/', $url);
      
      $fileParts = pathinfo($url);
      //$filename = basename($url);
      $filename = $fileParts['filename'];
      $final = str_replace('/', '_', $path_name).'_'.$filename;

      return $final;
  }
  	
 //decode sku.
function decrypt($sData, $secretKey)
{
    $sResult = '';
    $sData = decode_base64($sData);
    for ($i = 0; $i < strlen($sData); $i++) {
        $sChar = substr($sData, $i, 1);
        $sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
        $sChar = chr(ord($sChar) - ord($sKeyChar));
        $sResult .= $sChar;
    }
    return $sResult;
}

function decode_base64($sData)
{
    $sBase64 = strtr($sData, '-_', '+/');
    return base64_decode($sBase64 . '==');
}

function add_name_on_tshirt_field()
{
    $product_json = get_post_meta(get_the_ID(), 'product_json', true);
    $content_data = json_decode($product_json, true);
    $attributes = $content_data['attributes'];
    $default_attributes = $content_data['default_attributes'];
    ?>
    <table class="variations" cellspacing="0">
        <tbody>
        <?php
        foreach ($attributes as $key => $attribute):
            if (in_array('Style', $attribute, true) || in_array('Pattern', $attribute, true)): ?>
                <tr>
                    <td class="label"><label for="pattern">pattern</label></td>
                    <td class="value">
                        <select id="pattern" class="" name="attribute_pattern" data-attribute_name="attribute_pattern"
                                data-show_option_none="yes">
                            <?php
                            if (in_array('style', $default_attributes, true)) {
                                echo ' <option value="' . $default_attributes['style'] . '">' . $default_attributes['style'] . '</option>';
                            }
                            $style = $attribute['value'];
                            $mang_style = explode('| ', $style);
                            foreach ($mang_style as $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            <?php endif; endforeach; ?>
        <?php
        foreach ($attributes as $key => $attribute):
            if (in_array('Color', $attribute, true)): ?>
                <tr>
                    <td class="label"><label for="color">color</label></td>

                    <td class="value">
                        <select id="color" class="" name="attribute_color" data-attribute_name="attribute_color"
                                data-show_option_none="yes">

                            <?php
                            if (in_array('style', $default_attributes, true)) {
                                echo ' <option value="' . $default_attributes['color'] . '">' . $default_attributes['color'] . '</option>';
                            }
                            $color = $attribute['value'];
                            $mang_color = explode('| ', $color);
                            foreach ($mang_color as $key => $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>

            <?php endif; endforeach; ?>


        <?php
        foreach ($attributes as $key => $attribute):
            if (in_array('Size', $attribute, true)): ?>
                <tr>
                    <td class="label"><label for="size">size</label></td>
                    <td class="value">
                        <select id="size" class="" name="attribute_size" data-attribute_name="attribute_size"
                                data-show_option_none="yes">
                            <?php
                            if (in_array('size', $default_attributes, true)) {
                                echo ' <option value="' . $default_attributes['size'] . '">' . $default_attributes['size'] . '</option>';
                            }

                            $size = $attribute['value'];
                            $mang_size = explode('| ', $size);
                            foreach ($mang_size as $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                            ?>
                        </select>
                        <a id="reset_option" class="reset_variations">Clear</a>
                    </td>
                </tr>
            <?php endif; endforeach; ?>

        <tr>
            <p class="no_options"></p>
            <input type="hidden" name="price_change" value="" id="input_price">
        </tr>
        <tr>
            <input type="hidden" name="name_change" value="" id="input_name">
            <input type="hidden" name="image_change" value="" id="input_image">
            <input type="hidden" name="sku" value="" id="sku">
        </tr>
        </tbody>
    </table>
    <div class="change_price"></div>
    <div id="no-options"></div>
<?php }

add_action('woocommerce_before_add_to_cart_button', 'add_name_on_tshirt_field');

//override single product image brand.
add_filter('woocommerce_single_product_image_thumbnail_html', 'wc_remove_link_on_thumbnails');
function wc_remove_link_on_thumbnails($img)
{	
	
	  
	$s3_done = get_post_meta(get_the_ID(), 's3_done', true);
	
    $product_json = get_post_meta(get_the_ID(), 'product_json', true);
    $content_data = json_decode($product_json, true);
    
    if ($s3_done == 'done') {
	        $img = 'https://s3.amazonaws.com/dtshops/onmytee/'.s3_file_name($content_data['image']).'.jpg';// strtok($value['image'], '?');
    } else {
        $img = strtok($content_data['image'], '?');
    }
    
    
    $variations = $content_data['variations'];
    $name = $content_data['name'];

//    ma hoa sku
    
	
	
    foreach ($variations as &$value) {
        $value['sku'] = encrypt($value['sku'], 'hk_test_key');
        if ($s3_done == 'done') {
	        $value['image'] = 'https://s3.amazonaws.com/dtshops/onmytee/'.s3_file_name($value['image']).'.jpg';// strtok($value['image'], '?');
        } else {
        	$value['image'] = strtok($value['image'], '?');
        }
    }

    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var name_product = <?php echo json_encode($name); ?>;
            var style = '';
            var color = '';
            var size = '';
            var sku = '';
            var src = '<?php echo $img; ?>';
            var variations = <?php echo json_encode($variations) ?>;
            //get default = variations[0];
            var default_price = '';
            var default_name = '';
            var default_name_dao = '';
            console.log(variations[0]);
            var default_src = variations[0].description;

            $('#input_price').val(default_price);
            default_name_dao = default_name_dao.replace(/\s/g, '');
            default_name = default_name.replace(/\s/g, '');
            var defalut_sku = '';

            $('.change_price').html('$' + variations[0].regular_price);
            $('.wp-post-image').attr('src', variations[0].description);
            $('#input_price').val(variations[0].regular_price);
            $('#input_image').val(variations[0].description);
            $('#sku').val(variations[0].sku);


            style = $(this).find(":selected").val();

            var price_total = [];
            for (var i = 0; i < variations.length; i++) {
                price_total.push(variations[i].regular_price);
            }
            var max = Math.max(...price_total
        )
            ;
            var min = Math.min(...price_total
        )
            ;
            var price = min.toFixed(2);

            $('#pattern').change(function () {
                style = $(this).find(":selected").val();
                color = $('#color').find(":selected").val();
                size = $('#size').find(":selected").val();

                var name = style + '/' + color + '/' + size;
                var name_dao = size + '/' + color + '/' + style;


                if (typeof style == 'undefined') {
                    name = color + '/' + size;
                    name_dao = size + '/' + color;
                }
                if (typeof color == 'undefined') {
                    name = style + '/' + size;
                    name_dao = size + '/' + style;
                }
                if (typeof size == 'undefined') {
                    name = style + '/' + color;
                    name_dao = color + '/' + style;
                }
                name_dao = name_dao.replace(/\s/g, '');
                name = name.replace(/\s/g, '');
                var disabled = true;

                for (var i = 0; i < variations.length; i++) {
                    if ((variations[i].name).replace(/\s/g, '') == name || (variations[i].name).replace(/\s/g, '') == name_dao) {
                        typeof variations[i].description != 'undefined' ? src = variations[i].description : src = variations[i].image;
                        price = variations[i].regular_price;
                        sku = variations[i].sku;
                        disabled = false;
                    }
                }
                if (disabled == true) {
                    $('.single_add_to_cart_button').prop('disabled', true);
                    $('#no-options').html('<p class="wc-no-matching-variations woocommerce-info">Sorry, no products matched your selection. Please choose a different combination.</p>');
                    $('.change_price').html('');
                }
                else {
                    $('.single_add_to_cart_button').prop('disabled', false);
                    $('#no-options').html('');
                    $('.change_price').html('$' + price);
                }


                $('.woocommerce-product-gallery__image').attr('data-thumb', src);
                $('.woocommerce-product-gallery__image a').href = src;
                $('.woocommerce-product-gallery__image img').attr('src', src);

                $('.change_price').html('$' + price);
                $('#input_price').val(price);
                $('#input_image').val(src);
                $('#input_name').val(name_product);
                $('#sku').val(sku);
            });


            $('#color').change(function () {
                color = $(this).find(":selected").val();
                style = $('#pattern').find(":selected").val();
                size = $('#size').find(":selected").val();

                var name = style + '/' + color + '/' + size;
                var name_dao = size + '/' + color + '/' + style;
                if (typeof style == 'undefined') {
                    name = color + '/' + size;
                    name_dao = size + '/' + color;
                }
                if (typeof color == 'undefined') {
                    name = style + '/' + size;
                    name_dao = size + '/' + style;
                }
                if (typeof size == 'undefined') {
                    name = style + '/' + color;
                    name_dao = color + '/' + style;
                }
                name_dao = name_dao.replace(/\s/g, '');
                name = name.replace(/\s/g, '');
                var disabled = true;
                for (var i = 0; i < variations.length; i++) {
                    if (((variations[i].name).replace(/\s/g, '') == name) || ((variations[i].name).replace(/\s/g, '') == name_dao)) {
                        typeof variations[i].description != 'undefined' ? src = variations[i].description : src = variations[i].image;
                        price = variations[i].regular_price;
                        sku = variations[i].sku;
                        disabled = false;
                    }
                }
                if (disabled == true) {
                    $('.single_add_to_cart_button').prop('disabled', true);
                    $('#no-options').html('<p class="wc-no-matching-variations woocommerce-info">Sorry, no products matched your selection. Please choose a different combination.</p>');
                    $('.change_price').html('');
                }
                else {
                    $('.single_add_to_cart_button').prop('disabled', false);
                    $('#no-options').html('');
                    $('.change_price').html('$' + price);
                }

                $('.woocommerce-product-gallery__image').attr('data-thumb', src);
                $('.woocommerce-product-gallery__image a').href = src;
                $('.woocommerce-product-gallery__image img').attr('src', src);

                $('.change_price').html('$' + price);
                $('#input_price').val(price);
                $('#input_image').val(src);
                $('#input_name').val(name_product);
                $('#sku').val(sku);
            });

            $('#size').change(function () {
                size = $(this).find(":selected").val();
                style = $('#pattern').find(":selected").val();
                color = $('#color').find(":selected").val();

                var name = style + '/' + color + '/' + size;
                var name_dao = size + '/' + color + '/' + style;
                if (typeof style == 'undefined') {
                    name = color + '/' + size;
                    name_dao = size + '/' + color;
                }
                if (typeof color == 'undefined') {
                    name = style + '/' + size;
                    name_dao = size + '/' + style;
                }
                if (typeof size == 'undefined') {
                    name = style + '/' + color;
                    name_dao = color + '/' + style;
                }
                name_dao = name_dao.replace(/\s/g, '');
                name = name.replace(/\s/g, '');
                var disabled = true;
                for (var i = 0; i < variations.length; i++) {
                    if (((variations[i].name).replace(/\s/g, '') == name) || ((variations[i].name).replace(/\s/g, '') == name_dao)) {
                        typeof variations[i].description != 'undefined' ? src = variations[i].description : src = variations[i].image;
                        price = variations[i].regular_price;
                        sku = variations[i].sku;
                        disabled = false;
                    }
                }
                if (disabled == true) {
                    $('.single_add_to_cart_button').prop('disabled', true);
                    $('#no-options').html('<p class="wc-no-matching-variations woocommerce-info">Sorry, no products matched your selection. Please choose a different combination.</p>');
                    $('.change_price').html('');
                }
                else {
                    $('.single_add_to_cart_button').prop('disabled', false);
                    $('#no-options').html('');
                    $('.change_price').html('$' + price);
                }
                $('.woocommerce-product-gallery__image').attr('data-thumb', src);
                $('.woocommerce-product-gallery__image a').href = src;
                $('.woocommerce-product-gallery__image img').attr('src', src);
                // $('.wp-post-image').attr('src', src);

                $('#input_price').val(price);
                $('#input_image').val(src);
                $('#input_name').val(name_product);
                $('#sku').val(sku);
            });

            //reset option.
            $('#reset_option').css('cursor', 'pointer').click(function () {
                $('#size').prop('selectedIndex', 0);
                $('#pattern').prop('selectedIndex', 0);
                $('#color').prop('selectedIndex', 0);
                $('.single_add_to_cart_button').prop('disabled', false);
            });
        });
    </script>
    <?php

    $html = '<div data-thumb="' . $img . '" class="woocommerce-product-gallery__image">';
    $html .= '<a href="' . $img . '">';
    $html .= '<img width="800" height="1200" src="' . $img . '" data-large_image_width="980" data-large_image_height="980" />';
    $html .= '</a></div>';
    return $html;
}

//thay doi ten san pham.
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
add_action('woocommerce_single_product_summary', 'woocommerce_my_single_title', 5);
if (!function_exists('woocommerce_my_single_title')) {
    function woocommerce_my_single_title()
    {
        $product_json = get_post_meta(get_the_ID(), 'product_json', true);
        $content_data = json_decode($product_json, true);
        $name = $content_data['name'];
        echo '<h1 class="product_title entry-title">' . $name . '</h1>';
    }
}

//change palceholder image.
add_action('init', 'custom_fix_thumbnail');
function custom_fix_thumbnail()
{
    add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');

    function custom_woocommerce_placeholder_img_src($src)
    {
        $product_json = get_post_meta(get_the_ID(), 'product_json', true);
        $content_data = json_decode($product_json, true);
        $s3_done = get_post_meta(get_the_ID(), 's3_done', true);
        if ($s3_done == 'done') {
	        $src = 'https://s3.amazonaws.com/dtshops/onmytee/'.s3_file_name($content_data['image']).'.jpg';// strtok($value['image'], '?');
	    } else {
	        $src = strtok($content_data['image'], '?');
	    }
        //$src = $content_data['image'];
        return $src;
    }
}

//change price display.
function sv_change_product_price_display($price)
{
    $product_json = get_post_meta(get_the_ID(), 'product_json', true);
    $content_data = json_decode($product_json, true);
    $variations = $content_data['variations'];

    $price_total = [];
    foreach ($variations as $key => $variation) {
        array_push($price_total, $variation['regular_price']);
    }
    $min = min($price_total);
    $max = max($price_total);
    $price = '$' . $min . ' - $' . $max;
    if ($min == $max) {
        $price = '$' . $min;
    }
    return $price;
}

add_filter('woocommerce_get_price_html', 'sv_change_product_price_display');
add_filter('woocommerce_cart_item_price', 'sv_change_product_price_display');

//store data.
function save_options_on_tshirt_field($cart_item_data, $product_id)
{
    if (isset($_REQUEST['attribute_pattern'])) {
        $cart_item_data['attribute_pattern'] = $_REQUEST['attribute_pattern'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['attribute_color'])) {
        $cart_item_data['attribute_color'] = $_REQUEST['attribute_color'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['attribute_size'])) {
        $cart_item_data['attribute_size'] = $_REQUEST['attribute_size'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['image_change'])) {
        $cart_item_data['image_change'] = $_REQUEST['image_change'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['price_change'])) {
        $cart_item_data['price_change'] = $_REQUEST['price_change'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    if (isset($_REQUEST['sku'])) {
        $cart_item_data['sku'] = $_REQUEST['sku'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5(microtime() . rand());
    }
    return $cart_item_data;
}

add_action('woocommerce_add_cart_item_data', 'save_options_on_tshirt_field', 10, 2);

//render cart options
function render_meta_on_cart_and_checkout($cart_data, $cart_item = null)
{

   

    $custom_items = array();
    /* Woo 2.4.2 updates */
    if (!empty($cart_data)) {
        $custom_items = $cart_data;
    }
    if (isset($cart_item['sku'])) {
        //$cart_item['sku'] = decrypt($cart_item['sku'], 'hk_test_key');
        $cart_item['sku'] = $cart_item['sku'];
        $custom_items[] = array("name" => 'sku', "value" => $cart_item['sku']);
    }
    if (isset($cart_item['attribute_pattern'])) {
        $custom_items[] = array("name" => 'Style', "value" => $cart_item['attribute_pattern']);
    }

    if (isset($cart_item['attribute_color'])) {
        $custom_items[] = array("name" => 'Color', "value" => $cart_item['attribute_color']);
    }

    if (isset($cart_item['attribute_size'])) {
        $custom_items[] = array("name" => 'Style', "value" => $cart_item['attribute_size']);
    }

    return $custom_items;
}

add_filter('woocommerce_get_item_data', 'render_meta_on_cart_and_checkout', 10, 2);


function tshirt_order_meta_handler($item_id, $values, $cart_item_key)
{
    

    if (isset($values['sku'])) {
        $values['sku'] = decrypt($values['sku'], 'hk_test_key');
        wc_add_order_item_meta($item_id, "sku", $values['sku']);
    }
    if (isset($values['attribute_pattern'])) {
        wc_add_order_item_meta($item_id, "style", $values['attribute_pattern']);
    }
    if (isset($values['attribute_color'])) {
        wc_add_order_item_meta($item_id, "color", $values['attribute_color']);
    }
    if (isset($values['attribute_size'])) {
        wc_add_order_item_meta($item_id, "size", $values['attribute_size']);
    }
    if (isset($values['image_change'])) {
        $product_get_image = '<img alt="cart img" src="' . $values['image_change'] . '">';
        apply_filters('woocommerce_admin_order_item_thumbnail', $product_get_image, $item_id, $item_id);
    }
}

add_action('woocommerce_add_order_item_meta', 'tshirt_order_meta_handler', 1, 3);

//change product picture
function filter_woocommerce_cart_item_thumbnail($product_get_image, $cart_item, $cart_item_key)
{
    if (isset($cart_item['image_change'])) {
	    /* $s3_done = get_post_meta(get_the_ID(), 's3_done', true);
        if ($s3_done == 'done') {
	        $src = 'https://s3.amazonaws.com/dtshops/onmytee/'.s3_file_name($cart_item['image_change']).'.jpg';// strtok($value['image'], '?');
	    } else {
	        $product_get_image = '<img alt="cart img2" src="' . $cart_item['image_change'] . '">';
	    }
        */
        $product_get_image = '<img alt="cart img2" src="' . $cart_item['image_change'] . '">';
    }
    return $product_get_image;
}

add_filter('woocommerce_cart_item_thumbnail', 'filter_woocommerce_cart_item_thumbnail', 10, 3);


//change price cart
function filter_woocommerce_cart_item_price($wc, $cart_item, $cart_item_key)
{
    if (isset($cart_item['price_change'])) {
        $wc = $cart_item['price_change'];
    }
    return '$' . $wc;
}

;
add_filter('woocommerce_cart_item_price', 'filter_woocommerce_cart_item_price', 10, 3);


// define the woocommerce_cart_item_subtotal callback
function filter_woocommerce_cart_item_subtotal($wc, $cart_item, $cart_item_key)
{
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();
    $quantity = 1;
    foreach ($items as $item => $values) {
        $quantity = $values['quantity'];
    }

    if (isset($cart_item['price_change'])) {
        $wc = $cart_item['price_change'] * $quantity;

    }
    return '$' . $wc;
}

add_filter('woocommerce_cart_item_subtotal', 'filter_woocommerce_cart_item_subtotal', 10, 3);


add_action('woocommerce_before_calculate_totals', 'update_custom_price', 1, 1);
function update_custom_price($cart_object)
{

    foreach ($cart_object->cart_contents as $cart_item_key => $value) {
        // Version 2.x
        //$value['data']->price = $value['_custom_options']['custom_price'];
        // Version 3.x / 4.x
        $value['data']->set_price($value['price_change']);
    }
}

//custom search wordpress.
function list_searcheable(){
    $list_searcheable = array("title", "sub_title", "excerpt_short", "excerpt_long", "xyz", "product");
    return $list_searcheable;
}

function advanced_custom_search( $where, &$wp_query ) {
    global $wpdb;
    if ( empty( $where ))
        return $where;

    // get search expression
    $terms = $wp_query->query_vars[ 's' ];

    // explode search expression to get search terms
    $exploded = explode( ' ', $terms );
    if( $exploded === FALSE || count( $exploded ) == 0 )
        $exploded = array( 0 => $terms );

    // reset search in order to rebuilt it as we whish
    $where = '';

    // get searcheable_acf, a list of advanced custom fields you want to search content in
    $list_searcheable = list_searcheable();
    foreach( $exploded as $tag ) :
        $where .= " 
          AND (
            (wp_posts.post_title LIKE '%$tag%')
            OR (wp_posts.post_content LIKE '%$tag%')
            OR EXISTS (
              SELECT * FROM wp_postmeta
	              WHERE post_id = wp_posts.ID
	                AND (";
        foreach ($list_searcheable as $searcheable) :
            if ($searcheable == $list_searcheable[0]):
                $where .= " (meta_key LIKE '%" . $searcheable . "%' AND meta_value LIKE '%$tag%') ";
            else :
                $where .= " OR (meta_key LIKE '%" . $searcheable . "%' AND meta_value LIKE '%$tag%') ";
            endif;
        endforeach;
        $where .= ")
            )
            OR EXISTS (
              SELECT * FROM wp_comments
              WHERE comment_post_ID = wp_posts.ID
                AND comment_content LIKE '%$tag%'
            )
            OR EXISTS (
              SELECT * FROM wp_terms
              INNER JOIN wp_term_taxonomy
                ON wp_term_taxonomy.term_id = wp_terms.term_id
              INNER JOIN wp_term_relationships
                ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
              WHERE (
          		taxonomy = 'post_tag'
            		OR taxonomy = 'category'          		
            		OR taxonomy = 'myCustomTax'
          		)
              	AND object_id = wp_posts.ID
              	AND wp_terms.name LIKE '%$tag%'
            )
        )";
    endforeach;
    return $where ;
}
add_filter( 'posts_search', 'advanced_custom_search', 500, 2 );


?>