<?php
/**
 * Plugin Name: Custom Partdo WC Plugin
 * Description: Muestra el stock numérico en los productos de la tienda, más información de como editar el plugin en la documentación.
 * Author: Abraham Peña
 * Author URI: https://github.com/tonycito
 * Version: 1.0.0
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

 add_filter('plugin_row_meta', 'custom_plugin_row_meta', 10, 2);

function custom_plugin_row_meta($links, $file) {
    // Reemplaza esto con el nombre de tu archivo principal del plugin
    if ($file === plugin_basename(__FILE__)) {
        $new_links = array(
            '<a href="https://github.com/tonycito/plugin-wp-stock" target="_blank">Documentación</a>'
        );
        return array_merge($links, $new_links);
    }

    return $links;
}


if ( ! defined( 'ABSPATH' ) ) exit;

// NUEVO SHOP BOX
function custom_partdo_shop_box () {
    if(get_theme_mod('partdo_product_box_type') == 'type3'){
        echo custom_partdo_product_type3();
    } elseif (get_theme_mod('partdo_product_box_type') == 'type2'){
        echo custom_partdo_product_type2();
    } else {
        echo custom_partdo_product_type1();
    }
}
// Tu versión personalizada de type1
function custom_partdo_product_type1() {
	global $product;
	global $post;
	global $woocommerce;
	
	
	$output = '';
	
	$id = get_the_ID();
	$allproduct = wc_get_product( get_the_ID() );

	$cart_url = wc_get_cart_url();
	$price = $allproduct->get_price_html();
	$weight = $product->get_weight();
	$stock_status = $product->get_stock_status();
	$stock_text = $product->get_availability();
	$short_desc = $product->get_short_description();
	$rating = wc_get_rating_html($product->get_average_rating());
	$ratingcount = $product->get_review_count();
	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );
	$compare = get_theme_mod( 'partdo_compare_button', '0' );
	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );
	$sale_price = $product->get_sale_price();
  $igvmostrar = get_field('mostrar_igv', $product->get_id());
	$managestock = $product->managing_stock();
	$stock_quantity = $product->get_stock_quantity();
	$stock_format = esc_html__('Only %s left in stock','partdo');
	$stock_poor = '';
	$igv = '<span style="font-size: 11px; color: var(--e-global-color-primary); font-weight:500; ">con IGV</span>';


	if($managestock && $stock_quantity < 10) {
		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';
	}
	
	$total_sales = $product->get_total_sales();
	$total_stock = $total_sales + $stock_quantity;
	
	if($managestock && $stock_quantity > 0) {
	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama
	}
	
	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';

	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';

	if(partdo_shop_view() == 'list_view' || $postview == 'list_view') {
		$output .= '<div class="product">'; 
		$output .= '<div class="product-wrapper"> ';
		$output .= '<div class="product-content">';				
		$output .= '<div class="thumbnail-wrapper entry-media">';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
							
		$output .= partdo_wishlist_shortcode();
		
		$output .= partdo_compare_shortcode();
							
		if($quickview == '1'){
		$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="content-wrapper">';		
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		
		
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
			
		$output .= '<span class="price">'; 
		// if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';
		
    if ($stock_status == 'instock') {
    $output .= '<div class="product-stock in-stock">';
    $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
    if ($managestock && $stock_quantity !== null && $stock_quantity > 0) {
        $output .= sprintf(__('Cantidad disponible: %d', 'partdo'), esc_html($stock_quantity));
    } else {
        $output .= __('In Stock', 'partdo');
    }
    $output .= '</span></div>';
    } else {
        $output .= '<div class="product-stock outof-stock">';
        $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
        $output .= __('Producto agotado', 'partdo');
        $output .= '</span></div>';
    }

		$output .= '</div>';
		$output .= '</div>';
		
		$output .= '<div class="product-footer">';
		$output .= '<div class="product-footer-inner"> ';
		$output .= '<div class="product-footer-details">'; 
		$output .= '<p>'.partdo_limit_words(get_the_excerpt(), '20').'</p>';
		$output .= '</div>';
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$output .= ob_get_clean();
		$output .= '</div>';
		$output .= '</div>';																												
		
		$output .= '</div>';
		$output .= '<div class="product-content-fade"></div>';
		$output .= '</div>';

	} else {
		$output .= '<div class="product product-type-1">';
		$output .= ' <div class="product-wrapper">'; 
			
		$output .= '<div class="product-content">';		
		$output .= '<div class="thumbnail-wrapper entry-media"> ';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
			
		$output .= partdo_wishlist_shortcode();
					
		$output .= partdo_compare_shortcode();
					
		if($quickview == '1'){
			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
					
		$output .= ' </div>';
		$output .= ' </div>';
		
		$output .= '<div class="content-wrapper">';
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
		$output .= '<span class="price">'; 
		// 		if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';

		if ($stock_status == 'instock') {
    $output .= '<div class="product-stock in-stock">';
    $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
    if ($managestock && $stock_quantity !== null && $stock_quantity > 0) {
        $output .= sprintf(__('Cantidad disponible: %d', 'partdo'), esc_html($stock_quantity));
    } else {
        $output .= __('In Stock', 'partdo');
    }
    $output .= '</span></div>';
    } else {
        $output .= '<div class="product-stock outof-stock">';
        $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
        $output .= __('Producto agotado', 'partdo');
        $output .= '</span></div>';
    }
		$output .= '</div>';
		$output .= '</div>';
		

		$output .= '<div class="product-footer">';	
		$output .= '<div class="product-footer-inner"> ';
		if($short_desc){
		$output .= '<div class="product-footer-details"> ';
		$output .= $short_desc;
		$output .= '</div>';
		}
		ob_start();
		woocommerce_template_loop_add_to_cart();
		$output .= ob_get_clean();
		$output .= '</div>';
		$output .= '</div>';
	
		
		$output .= '</div>';	
		$output .= '<div class="product-content-fade"></div>';
		$output .= '</div>';

	}
	
	return $output;
}

// Tu versión personalizada de type2
function custom_partdo_product_type2() {
	global $product;
	global $post;
	global $woocommerce;
	
	$output = '';
	
	$id = get_the_ID();
	$allproduct = wc_get_product( get_the_ID() );

	$cart_url = wc_get_cart_url();
	$price = $allproduct->get_price_html();
	$weight = $product->get_weight();
	$stock_status = $product->get_stock_status();
	$stock_text = $product->get_availability();
	$short_desc = $product->get_short_description();
	$rating = wc_get_rating_html($product->get_average_rating());
	$ratingcount = $product->get_review_count();
	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );
	$compare = get_theme_mod( 'partdo_compare_button', '0' );
	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );
	$igv = '<span style="font-size: 11px; color: var(--e-global-color-primary); font-weight:500; ">con IGV</span>';
  $sale_price = $product->get_sale_price();
	$managestock = $product->managing_stock();
	$igvmostrar = get_field('mostrar_igv', $product->get_id());
	$stock_quantity = $product->get_stock_quantity();
	$stock_format = esc_html__('Only %s left in stock','partdo');
	$stock_poor = '';

	if($managestock && $stock_quantity < 10) {
		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';
	}
	
	$total_sales = $product->get_total_sales();
	$total_stock = $total_sales + $stock_quantity;
	
	if($managestock && $stock_quantity > 0) {
	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama
	}
	
	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';
	
	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';
	
	if(partdo_shop_view() == 'list_view') {
		$output .= '<div class="product-wrapper"> ';
		$output .= '<div class="product-content">';				
		$output .= '<div class="thumbnail-wrapper entry-media">';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
							
		$output .= partdo_wishlist_shortcode();
		
		$output .= partdo_compare_shortcode();
							
		if($quickview == '1'){
		$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="content-wrapper">';		
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
		
		$output .= '<div class="product-cart-form">';
		$output .= '<span class="price">'; 
		// 		if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$output .= ob_get_clean();;
		$output .= '</div>';
		if ($stock_status == 'instock') {
    $output .= '<div class="product-stock in-stock">';
    $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
    if ($managestock && $stock_quantity !== null && $stock_quantity > 0) {
        $output .= sprintf(__('Cantidad disponible: %d', 'partdo'), esc_html($stock_quantity));
    } else {
        $output .= __('In Stock', 'partdo');
    }
    $output .= '</span></div>';
    } else {
        $output .= '<div class="product-stock outof-stock">';
        $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
        $output .= __('Producto agotado', 'partdo');
        $output .= '</span></div>';
    }
	
		$output .= '</div>';
		$output .= '</div>';																												
		
		$output .= '</div>';

	} else {
		$output .= ' <div class="product-wrapper product-type-2">'; 	
		$output .= '<div class="product-content">';		
		$output .= '<div class="thumbnail-wrapper entry-media"> ';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
			
		$output .= partdo_wishlist_shortcode();
					
		$output .= partdo_compare_shortcode();
					
		if($quickview == '1'){
			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
					
		$output .= ' </div>';
		$output .= ' </div>';
		
		$output .= '<div class="content-wrapper">';
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
		
		$output .= '<div class="product-cart-form">';
		$output .= '<span class="price" style="flex-direction: row; align-items: end; gap: 6px;">';
		// if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$output .= ob_get_clean();;
		$output .= '</div>';
		if ($stock_status == 'instock') {
    $output .= '<div class="product-stock in-stock">';
    $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
    if ($managestock && $stock_quantity !== null && $stock_quantity > 0) {
        $output .= sprintf(__('Cantidad disponible: %d', 'partdo'), esc_html($stock_quantity));
    } else {
        $output .= __('In Stock', 'partdo');
    }
    $output .= '</span></div>';
    } else {
        $output .= '<div class="product-stock outof-stock">';
        $output .= '<i class="klbth-icon-ecommerce-package-ready"></i><span>';
        $output .= __('Producto agotado', 'partdo');
        $output .= '</span></div>';
    }
		
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';	

	}

	return $output;
}
// Tu versión personalizada de type3
function custom_partdo_product_type3(){
	global $product;
	global $post;
	global $woocommerce;
	
	$output = '';
	
	$id = get_the_ID();
	$allproduct = wc_get_product( get_the_ID() );

	$cart_url = wc_get_cart_url();
	$price = $allproduct->get_price_html();
	$weight = $product->get_weight();
	$stock_status = $product->get_stock_status();
	$stock_text = $product->get_availability();
	$short_desc = $product->get_short_description();
	$rating = wc_get_rating_html($product->get_average_rating());
	$ratingcount = $product->get_review_count();
	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );
	$compare = get_theme_mod( 'partdo_compare_button', '0' );
	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );
	$sale_price = $product->get_sale_price();
  $igvmostrar = get_field('mostrar_igv', $product->get_id());
	$managestock = $product->managing_stock();
	$stock_quantity = $product->get_stock_quantity();
	$stock_format = esc_html__('Only %s left in stock','partdo');
	$stock_poor = '';
	$igv = '<span style="font-size: 11px; color: var(--e-global-color-primary); font-weight:500; ">con IGV</span>';

	if($managestock && $stock_quantity < 10) {
		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';
	}
	
	$total_sales = $product->get_total_sales();
	$total_stock = $total_sales + $stock_quantity;
	
	if($managestock && $stock_quantity > 0) {
	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama
	}

	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';
	
	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';

	if(partdo_shop_view() == 'list_view' || $postview == 'list_view') {
		$output .= '<div class="product">'; 
		$output .= '<div class="product-wrapper"> ';
		$output .= '<div class="product-content">';				
		$output .= '<div class="thumbnail-wrapper entry-media">';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
							
		$output .= partdo_wishlist_shortcode();
		
		$output .= partdo_compare_shortcode();
							
		if($quickview == '1'){
		$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div class="content-wrapper">';		
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
		
		$output .= '<span class="price">'; 
		// if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';
		
		if($managestock && $stock_quantity > 0) {
			$output .= '<div class="product-progress-wrapper">';
			$output .= '<div class="product-progress"> <span class="progress" style="width:'.esc_attr($progress_percentage).'%"> </span><span class="not-progress"></span></div>';
			$output .= '<div class="product-progress-count"> ';
			$output .= '<div class="available">'.esc_html__('Available:','partdo').'<strong>'.esc_html($product->get_stock_quantity()).'</strong>';
			$output .= '</div>';
			$output .= '<div class="sold">'.esc_html__('Sold:','partdo').' <strong>'.esc_html($product->get_total_sales()).'</strong>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}
		
		$output .= '</div>';
		$output .= '</div>';
		
		$output .= '<div class="product-footer">';
		$output .= '<div class="product-footer-inner"> ';
		$output .= '<div class="product-footer-details">'; 
		$output .= '<p>'.partdo_limit_words(get_the_excerpt(), '20').'</p>';
		$output .= '</div>';
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$output .= ob_get_clean();
		$output .= '</div>';
		$output .= '</div>';																												
		
		$output .= '</div>';
		$output .= '<div class="product-content-fade"></div>';
		$output .= '</div>';

	} else {
		$output .= '<div class="product product-type-3">';
		$output .= ' <div class="product-wrapper">'; 
			
		$output .= '<div class="product-content">';		
		$output .= '<div class="thumbnail-wrapper entry-media"> ';
		$output .= partdo_sale_percentage();
		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';
			ob_start();
			$output .= partdo_product_second_image();
			$output .= ob_get_clean();
		$output .= '</a>';
		$output .= '<div class="product-buttons"> ';
			
		$output .= partdo_wishlist_shortcode();
					
		$output .= partdo_compare_shortcode();
					
		if($quickview == '1'){
			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';
		}	
					
		$output .= ' </div>';
		$output .= ' </div>';
		
		$output .= '<div class="content-wrapper">';
		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
		
		if(partdo_vendor_name()){
			$output .= '<div class="content-switcher">';
			$output .= '<div class="switcher-wrapper">';			  
			$output .= partdo_vendor_name();
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				  $output .= $rating;
				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}				  
			$output .= '</div>';
			$output .= '</div>';
		} else {
			if($ratingcount){	
				$output .= '<div class="product-rating">';
				$output .= $rating;
				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';
				$output .= '</div>';
			}
		}
		
		$output .= '<span class="price">';
		// if (is_user_logged_in()) {
    // 		$output .= ($igvmostrar == "Mostrar IGV") ? $price . $igv : $price;
		// }else {
		// 		$output .= $price;
		// }
		$output .= $price;
		$output .= '</span>';
		
		if($managestock && $stock_quantity > 0) {
			$output .= '<div class="product-progress-wrapper">';
			$output .= '<div class="product-progress"> <span class="progress" style="width:'.esc_attr($progress_percentage).'%"> </span><span class="not-progress"></span></div>';
			$output .= '<div class="product-progress-count"> ';
			$output .= '<div class="available">'.esc_html__('Available:','partdo').'<strong>'.esc_html($product->get_stock_quantity()).'</strong>';
			$output .= '</div>';
			$output .= '<div class="sold">'.esc_html__('Sold:','partdo').' <strong>'.esc_html($product->get_total_sales()).'</strong>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}
		
		$output .= '</div>';
		$output .= '</div>';
		
		if($short_desc){
			$output .= '<div class="product-footer">';	
			$output .= '<div class="product-footer-inner"> ';
			$output .= '<div class="product-footer-details"> ';
			$output .= $short_desc;
			$output .= '</div>';
			ob_start();
			woocommerce_template_loop_add_to_cart();
			$output .= ob_get_clean();
			$output .= '</div>';
			$output .= '</div>';
		}
		
		$output .= '</div>';	
		$output .= '<div class="product-content-fade"></div>';
		$output .= '</div>';

	}
	
	return $output;
}


// Stock personalizado pagina del producto
add_filter('woocommerce_get_stock_html', function($html, $product) {

    $stock_status   = $product->get_stock_status();
    $managestock    = $product->managing_stock();
    $stock_quantity = $product->get_stock_quantity();

    if ( $stock_status == 'instock' ) {
        $output = '<div class="product-stock stock in-stock">';
        if ( $managestock && $stock_quantity !== null && $stock_quantity > 0 ) {
						$unidad = get_field('unidad_de_medida', $product->get_id());
						$output .= sprintf( __( 'Cantidad disponible: %d%s', 'partdo' ), esc_html( $stock_quantity ),$unidad ? ' - ' . esc_html( $unidad ) : '');
        } else {
            $output .= __( 'In Stock', 'partdo' );
        }
        $output .= '</div>';
    } else {
        $output = '<div class="product-stock stock out-of-stock">';
        $output .= sprintf( __( 'Producto agotado', 'partdo' ));
        $output .= '</div>';
    }
    return $output;

}, 10, 2);


add_action('woocommerce_single_product_summary', function() {
		 if ( ! is_user_logged_in() ) {
        return '';
    }
    global $product;
    $ficha = get_field('ficha_tecnica', $product->get_id());
    if ($ficha) {
        echo '<div class="button-ficha-tecnica" style="margin-top:10px;">';
        echo '<a href="' . esc_url($ficha) . '" class="btn-ficha-tecnica" target="_blank" rel="noopener noreferrer">Ver Ficha técnica</a>';
        echo '</div>';
    }
}, 31);

// Enqueue custom CSS for the plugin
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'custom-partdo-wp-style',
        plugin_dir_url(__FILE__) . 'custom-partdo-wp.css',
        [],
        '1.0.0'
    );
});