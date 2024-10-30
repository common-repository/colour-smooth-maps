<?php
/**
 * Plugin Name: Smooth Maps
 * Description: Easily add a Google Map to your website
 * Version: 1.1
 * Author: Wojciech Borowicz
 * Author URI: https://borowicz.me
 */
 
 
 
 /****************************************************************** Load google maps JS script asynchronously (called in draw function) ************************************************************************************************/
 
function smoothMaps_loadGooglemapsScript() {
	
  $smoothMaps_options = get_option('smoothMaps_options');
	wp_enqueue_script('google-maps', 'http://maps.google.com/maps/api/js?key='.esc_attr($smoothMaps_options['smoothMapsAPI']).'&callback=smoothMaps_initMap#asyncload', array(), null, true);
 }

 
 
 function smoothMaps_add_async_forscript($url)
{
    if (strpos($url, '#asyncload')===false)
        return $url;

    else
        return str_replace('#asyncload', '', $url)."' async  defer"; 
}
add_filter('clean_url', 'smoothMaps_add_async_forscript', 11, 1);
 /************************************************************ Register Custom post type for smooth maps *****************************************************************************************************/
function smoothMaps_custom_post() {
    $args = array(
        'public'    => true,
        'label'     => __( 'Smooth Maps', 'textdomain' ),
		 'supports'           => array( 'title'),
        'menu_icon' => 'dashicons-location-alt',
		'publicly_queryable' => false,
    );
    register_post_type( 'smoothmaps', $args );
}
add_action( 'init', 'smoothMaps_custom_post' );


add_action( 'admin_menu', 'smoothMaps_SettingsPage' );
function smoothMaps_SettingsPage() {
    add_submenu_page(
        'edit.php?post_type=smoothmaps',
        __( 'Smooth Maps Settings', 'textdomain' ),
        __( 'Settings', 'textdomain' ),
        'manage_options',
        'smooth-maps-setttings',
        'smoothMaps_Callback'
    );
}

add_filter( 'manage_edit-smoothmaps_columns', 'smoothMaps_columns' ) ;

function smoothMaps_columns( $columns ) {

	$columns = array(
		'cb' => '&lt;input type="checkbox" />',
		'title' => __( 'Map Name' ),
		'shortcode' => __( 'Shortcode' ),
		'date' => __( 'Date' )
	);

	return $columns;
}



add_action( 'manage_smoothmaps_posts_custom_column', 'smoothMaps_shortcode_Columns', 10, 2 );

function smoothMaps_shortcode_Columns( $column, $post_id ) {


	if( $column== 'shortcode')	echo '<input type="text" value="[smoothmaps id='.$post_id.']" />';

	
}

/********************************************************************Register Settings Page********************************************************************************************************/
function smoothMaps_register_settings_cb(){
    
    register_setting('smoothMaps-settings-group', 'smoothMaps_options', 'smoothMaps_options_sanitize');
} 
add_action( 'admin_init', 'smoothMaps_register_settings_cb' );

/******************************************************************Settings page content**********************************************************************************************************/

function smoothMaps_options_sanitize($input){
    $input['smoothMapsAPI'] = sanitize_text_field($input['smoothMapsAPI']);

    return $input;
} 

function smoothMaps_Callback() { 
	?>
    <div class="wrap">
        <h1><?php _e( 'Smooth Maps Settings', 'textdomain' ); ?></h1>
		<h2>API Keys</h2>
		<form method="post" action="options.php">
	<?php 
		settings_fields('smoothMaps-settings-group'); 
        $smoothMaps_options = get_option('smoothMaps_options');
	?>
		<p>
	<?php 
		_e( '<b>Google Maps API Key:</b>', 'textdomain' ); 
	?>
		</p>
		<input type="text" name="smoothMaps_options[smoothMapsAPI]" value =" <?php  echo esc_attr($smoothMaps_options['smoothMapsAPI']) ?>  " style="max-width:100%; width:400px"/>
		<br/>
		<br/> 
		<a href="https://elfsight.com/blog/2018/06/how-to-get-google-maps-api-key-guide/" target="_blank">How to get API Key</a>
		
	<?php
            submit_button();
    ?>
		 </form>
    </div>
    <?php
}

/***********************************************************************Add assets to the admin page******************************************************************************************/



function smoothMaps_addAssets() {
    wp_register_style('smoothMaps_addAssets', plugins_url('/assets/css/smoothmaps.css',__FILE__ ));
    wp_enqueue_style('smoothMaps_addAssets');
    wp_register_script( 'smoothMaps_addAssets', plugins_url('/assets/js/smoothmaps.js',__FILE__ ));
    wp_enqueue_script('smoothMaps_addAssets');
}

add_action( 'admin_init','smoothMaps_addAssets');



/************************************************************** Register extra boxes to map edit page ***************************************************************************************************************/

function smoothMaps_register_meta_boxes() {
    add_meta_box( 'smoothMaps-meta-box-id', __( 'Map Settings', 'textdomain' ), 'smoothMaps_display_callback', 'smoothmaps' );
}
add_action( 'add_meta_boxes', 'smoothMaps_register_meta_boxes' );

/************************************************************** Map Edit box ***************************************************************************************************************/
function smoothMaps_display_callback( $post ) {
$meta = get_post_meta($post->ID); 

?> 

<table>

    <tr>
        <td>
            <label>
				Map Type:
			</label>
		</td>
        <td>

            <select name="smoothMaps-type" id="smoothMaps-type"  />
            <option value="smoothMaps-full" <?php if($meta[ 'smoothMaps-type'][0]=="smoothMaps-full" ) echo "selected"; ?>>Full map	</option>
            <option value="smoothMaps-iframe" <?php if($meta[ 'smoothMaps-type'][0]=="smoothMaps-iframe" ) echo "selected"; ?>>Iframe	</option>
            </select>

        </td>
    </tr>
    <td>
        <tr id="smoothMaps-Iframe-address-row">
            <td>
                <label>
					<b>Address:</b>
                </label>   
            </td>
            <td>
                <input type="text" name="smoothMapsIframe-address" id="smoothMapsIframe-address" value="<?php echo $meta['smoothMapsIframe-address'][0]; ?>" />
                
                <br/>
			</td>
        </tr>

        <tr>
            <td>
                <label>
					Zoom:
				</label>
			</td>
            <td>
                <input type="number" min="0" max="22" placeholder="Zoom Level (0-22 / Default 16)" name="smoothMaps-zoom" id="smoothMaps-zoom" value="<?php echo $meta['smoothMaps-zoom'][0]; ?>"  />
                
                <br/>
            </td>
        </tr>
        <td>
            <label>
				Height:
			</label>
		</td>
        <td>
            <input type="number" name="smoothMaps-height" placeholder="Height of a map ( px )" id="smoothMaps-height" value="<?php echo $meta['smoothMaps-height'][0]; ?>" />
           
            <br/>
        </td>
        <tr>
            <td>
                <label>
					Width:
				</label>
			</td>
            <td>
                <input type="text" name="smoothMaps-width" id="smoothMaps-width" placeholder="Width of a map ( % / px ) " value="<?php echo $meta['smoothMaps-width'][0]; ?>"  />
                
                <br/>
            </td>
        </tr>
        <tr id="smoothMaps-style-row">
            <td>
                <label>
					Style:
				</label>
			</td>
            <td>
                <select name="smoothMaps-style" id="smoothMaps-style" />
                <option value="standard" <?php if($meta[ 'smoothMaps-style'][0]=="standard" ) echo "selected"; ?>>Standard Theme</option>
                <option value="night" <?php if($meta[ 'smoothMaps-style'][0]=="night" ) echo "selected"; ?>>Night Theme</option>
                <option value="gray" <?php if($meta[ 'smoothMaps-style'][0]=="gray" ) echo "selected"; ?>>Black&White Theme</option>
                </select>
                <br/>
            </td>
        </tr>
        <tr id="smoothMaps-hidebusinesses-row">
            <td>
                <label>
					Hide Other Businesses:
				</label>
			</td>
            <td>
                <select name="smoothMaps-hide" id="smoothMaps-hide" />
                <option value="show" <?php if($meta[ 'smoothMaps-hide'][0]=="show" ) echo "selected"; ?>>No, show all businesses</option>
                <option value="hide" <?php if($meta[ 'smoothMaps-hide'][0]=="hide" ) echo "selected"; ?>>Yes, hide all businesses around me</option>
                </select>
                <br/>
            </td>
        </tr>

</table>
	<div class="smoothMapstab">
	
		<!-- Markers Navigation (Marker 1, Marker 2 (tab names).... ) -->
		<div  id="markerNav">
		
			<a class="smoothMapstablinks active" onclick="smoothMaps_openMarker(event, 'marker-1')" id="smoothMapstabMarker-1">Marker 1</a>
			  <?php
				
				$i = 1;
				foreach(maybe_unserialize($meta['smoothMaps-address'][0]) as $key=>$value){
				if($key!=0){
					$i++;
					echo ' <a class="smoothMapstablinks" onclick="smoothMaps_openMarker(event, \'marker-'.$i.'\')" id="smoothMapstabMarker-'.$i.'">Marker '.$i.'</a>';
				}
			}
			?>
		</div>
			<!-- END Markers Navigation -->
	<a class="smoothMapstablinks" onclick="smoothMaps_createMarker()">+</a> 
</div>

	<!-- First address tab content -->
	<div id="markersmoothMapstabsContent">
		<div id="marker-1" class="smoothMapstabcontent markersmoothMapstab" style="display:block">
		  <label><b>Address:</b><br/>
			<input type="text" name="smoothMaps-address[0]" id="smoothMaps-address[0]" value="<?php echo maybe_unserialize($meta['smoothMaps-address'][0])[0]; ?>"  />
		 </label><br/>

	</div>
	<!-- End First address tab content -->
	
	<!-- Generate content tabs for all addresses -->
		<?php
			$i = 0;
			foreach(maybe_unserialize($meta['smoothMaps-address'][0]) as $key=>$value){

				if($key!=0){
					$i++;
						echo '
							<div id="marker-'.($i+1).'" class="smoothMapstabcontent markersmoothMapstab" >
							  <label><b>Address:</b><br/>
									<input type="text" name="smoothMaps-address['.$i.']" id="smoothMaps-address['.$i.']" value="'.$value.'" style="width:100%" />
							  </label>
							  <span style="color:red;font-weight:600;text-decoration:underline;cursor:pointer;" onclick="smoothMaps_removeElement('.($i+1).')">Remove</span><br/>
							</div>
						';
						}
			}
		?>
	<!--End Generate content tabs for all addresses -->
 
	</div>
	
	<h3>Preview map:</h3>
		<?php 
		// Draw the map for admin Page
		
		echo smoothMaps_draw($meta);
		
		?>
		

   <?php
}
 


function smoothMaps_save_meta_box( $post_id ) {
	
	 
	if(get_post_type($post_id)=='smoothmaps'){
update_post_meta($post_id, 'smoothMaps-type', sanitize_text_field($_POST['smoothMaps-type']));						// Select Value
update_post_meta($post_id, 'smoothMapsIframe-address', sanitize_text_field($_POST['smoothMapsIframe-address'])); 	

$addresses = $_POST['smoothMaps-address'];
// Sanitize addresses inputs for full google map.
if(is_array($addresses)){
	foreach($addresses as &$singleAddress){
		$singleAddress = sanitize_text_field($singleAddress);
	}
} else{
	$addresses = sanitize_text_field($addresses);
}
update_post_meta($post_id, 'smoothMaps-address', $addresses);

if(is_numeric($_POST['smoothMaps-zoom'])){
	// Check if zoom input is a number
	update_post_meta($post_id, 'smoothMaps-zoom', sanitize_text_field( $_POST['smoothMaps-zoom']));
}
if(smoothMapsWidthValidation($_POST['smoothMaps-width'])){
	// Check if width input is in % or px.
	update_post_meta($post_id, 'smoothMaps-width', sanitize_text_field( $_POST['smoothMaps-width'] ));
} 
else{
	// if width input is invalid - set it to 100%.
	update_post_meta($post_id, 'smoothMaps-width', '100%' );
}
if(is_numeric($_POST['smoothMaps-height'])){
	// Check if height input is a number
	update_post_meta($post_id, 'smoothMaps-height', sanitize_text_field($_POST['smoothMaps-height'] ));
}
update_post_meta($post_id, 'smoothMaps-style', sanitize_text_field( $_POST['smoothMaps-style'] )); 					// Select Value
update_post_meta($post_id, 'smoothMaps-hide', sanitize_text_field( $_POST['smoothMaps-hide'] ));					// Select Value

}
}
add_action( 'save_post', 'smoothMaps_save_meta_box' );


/************************************************************** Display shortcode in a sidebar ***************************************************************************************************************/

function smoothMaps_register_shortcode_box() {
    add_meta_box( 'smoothMaps-shortcode-box-id', __( 'Generated Shortcode', 'textdomain' ), 'smoothMaps_shortcode_sidebar', 'smoothmaps', 'side' );
}
add_action( 'add_meta_boxes', 'smoothMaps_register_shortcode_box' );

function smoothMaps_shortcode_sidebar( $post ) {

		if($post->post_status=="publish"){
			?>
				<input type="text" value="[smoothmaps id='<?php echo $post->ID; ?>']" />
				
			<?php
			esc_html_e( "Copy and paste this shortcode anywhere on your website to display the map.", 'text-domain');
		}
		else{
			esc_html_e( "Publish map to generate the shortcode.", 'text-domain');
		}
}

/*************************************************************************** Draw the map function *************************************************************************************************/


function smoothMaps_draw($meta){
	smoothMaps_loadGooglemapsScript();
	
			$zoom =  $meta['smoothMaps-zoom'][0]; 
			$style =  $meta['smoothMaps-style'][0]; 
			$hide =  $meta['smoothMaps-hide'][0]; 
			
			// Add default values in case of empty inputs
				if(empty($zoom))	$zoom = 16;
			$height = $meta['smoothMaps-height'][0];
				if(empty($height))	$height = "500";
			$width = $meta['smoothMaps-width'][0];
				if(empty($width))	$width = "100%";
	
	
		if($meta['smoothMaps-type'][0] == 'smoothMaps-iframe'){ 

		// If user choose iframe as a map type - generate a google maps iframe. No need for API Key
			$address = $meta['smoothMapsIframe-address'][0];
			return 
			'<script>
			
				  function smoothMaps_initMap() {
				  }</script>
				  
				  <div style="width:'.esc_attr($width).'">
			<iframe width="'.esc_attr($width).'" height="'.esc_attr($height).'" src="https://maps.google.com/maps?width='.esc_attr($width).'&amp;height='.esc_attr($height).'&amp;hl=en&amp;q='.urlencode(esc_attr($address)).'&amp;ie=UTF8&amp;t=&amp;z='.esc_attr($zoom).'&amp;iwloc=A&amp;output=embed" 
			frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
			</iframe></div>';
		}
			
		else{	
		// Draw the map using google maps API
			ob_start();
			$smoothMaps_options = get_option('smoothMaps_options'); // Get API key

			$addresses = maybe_unserialize($meta['smoothMaps-address'][0]);
			$i=0;

			foreach($addresses as &$address){
				
			if($address!=""){
				
			$coordinates = wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode(esc_attr($address)). '&key='.esc_attr($smoothMaps_options['smoothMapsAPI']))['body'];		
			
				
					$coordinates =  json_decode($coordinates, JSON_PRETTY_PRINT); // Convert JSON response to array

									$resultsLat[$i] = $coordinates["results"][0]["geometry"]["location"]["lat"];
									$resultsLong[$i] = $coordinates["results"][0]["geometry"]["location"]["lng"];
									
									// Fill results with zeros in case of error to avoid JS error with callback function.
									if(!is_float($resultsLat[$i]) || empty($resultsLat[$i])) $resultsLat[$i] = 0;
									if(!is_float($resultsLong[$i]) || empty($resultsLong[$i])) $resultsLong[$i] = 0;
							$i++;	
			}
			}

				


		/******************** Generate output for map drawing *************/
		$output = '<div id="map" style="height:'. esc_attr($height) .'px;width:'. esc_attr($width) .';"></div>';

				/******************** Load map themes from /templates/ folder *************/
				if($style=="night") {		
					$styles = "
					styles: [
            {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
            {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
            {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
            {
              featureType: 'administrative.locality',
              elementType: 'labels.text.fill',
              stylers: [{color: '#d59563'}]
            },
            {
              featureType: 'poi',
              elementType: 'labels.text.fill',
              stylers: [{color: '#d59563'}]
            },
            {
              featureType: 'poi.park',
              elementType: 'geometry',
              stylers: [{color: '#263c3f'}]
            },
            {
              featureType: 'poi.park',
              elementType: 'labels.text.fill',
              stylers: [{color: '#6b9a76'}]
            },
            {
              featureType: 'road',
              elementType: 'geometry',
              stylers: [{color: '#38414e'}]
            },
            {
              featureType: 'road',
              elementType: 'geometry.stroke',
              stylers: [{color: '#212a37'}]
            },
            {
              featureType: 'road',
              elementType: 'labels.text.fill',
              stylers: [{color: '#9ca5b3'}]
            },
            {
              featureType: 'road.highway',
              elementType: 'geometry',
              stylers: [{color: '#746855'}]
            },
            {
              featureType: 'road.highway',
              elementType: 'geometry.stroke',
              stylers: [{color: '#1f2835'}]
            },
            {
              featureType: 'road.highway',
              elementType: 'labels.text.fill',
              stylers: [{color: '#f3d19c'}]
            },
            {
              featureType: 'transit',
              elementType: 'geometry',
              stylers: [{color: '#2f3948'}]
            },
            {
              featureType: 'transit.station',
              elementType: 'labels.text.fill',
              stylers: [{color: '#d59563'}]
            },
            {
              featureType: 'water',
              elementType: 'geometry',
              stylers: [{color: '#17263c'}]
            },
            {
              featureType: 'water',
              elementType: 'labels.text.fill',
              stylers: [{color: '#515c6d'}]
            },
            {
              featureType: 'water',
              elementType: 'labels.text.stroke',
              stylers: [{color: '#17263c'}]
            },
			
					";
				}
				if($style=="gray"){
					$styles = '
 styles : [{ "elementType": "geometry", 
						"stylers": [{ "saturation": -100 }]
},
					';
				}
				/******************** Load map themes from /templates/ folder *************/
				if($hide=="hide"){
				  $hide_busineeses = "{
					  featureType: 'poi',  			
					  elementType: 'labels',
					  stylers: [{visibility: 'off'}]
					}";
					}
				 if($style == "standard"){
					 $styles="styles : [";
				 }

		$output = $output . '<script>
			
				  function smoothMaps_initMap() {
				var myLatLng = {lat: '. $resultsLat[0] .', lng: '. $resultsLong[0].' };
		 
				var fenway = {lat:' .  $resultsLat[0] .', lng: '.  $resultsLong[0].'};
				var map = new google.maps.Map(document.getElementById("map"), {
				  center: fenway,
				  zoom: '. esc_attr($zoom).',
				  '. $styles.$hide_busineeses.']
				  
				});';
				foreach($resultsLat as $key => $lat){
			$markers = $markers . '	var marker'.$key.' = new google.maps.Marker({
				  position:  {lat: '. $resultsLat[$key] .', lng: '. $resultsLong[$key].' },
				  map: map,
				});';
			}
				$output = $output .$markers. '	
			  }

		   </script> ';    
		   return $output;
		}
	}

/********************************************************************Register Shortcode*********************************************************************************************/

function smoothMaps_shortcode($id) {
	
	if(!wp_style_is('smoothMaps_addAssets') && !wp_script_is('smoothMaps_addAssets')){
		// Check if assets have been already added on a page. Add them for a front-end if they're not on there.
		wp_register_style('smoothMaps_addAssets', plugins_url('/assets/css/smoothmaps.css',__FILE__ ));
		wp_enqueue_style('smoothMaps_addAssets');
		wp_register_script( 'smoothMaps_addAssets', plugins_url('/assets/js/smoothmaps.js',__FILE__ ));
		wp_enqueue_script('smoothMaps_addAssets');
	}
	
	$meta = get_post_meta($id["id"]); // Get all meta data for relevant map id
	
	return smoothMaps_draw($meta); // Draw the map 
}

add_shortcode( 'smoothmaps', 'smoothMaps_shortcode' );



/***************************************************************Validation functions**************************************************************************************************************/
function smoothMapsWidthValidation($width){
	if(preg_match('/[0-9].*px/', $width)){
		return true;
	}
	else if(preg_match('/[0-9].*%/', $width)){
		return true;
	}
	else return false;	
}




 ?>