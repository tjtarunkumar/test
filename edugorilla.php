<?php
/**
 * Plugin Name: EduGorilla
 * Description: Add Lead and search.
 * Version: 1.0.0
 * Author: Tarun Kumar
 * Author URI: http://www.facebook.com/tjtarunkumar
 * */
	function create_edugorilla_lead_table()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'edugorilla_lead'; //Defining a table name.
		$sql = "CREATE TABLE $table_name (
											id int(11) NOT NULL AUTO_INCREMENT,
											name varchar(200) NOT NULL,
                                            keyword varchar(200) NOT NULL,
											contact_no varchar(50) NOT NULL,
											email varchar(200) NOT NULL,
											query text(500) NOT NULL,
                                            location text(500) NOT NULL,
											longitude double NOT NULL,
											latitude double NOT NULL,
											category_id text(500) NOT NULL,
											distance int(50) NOT NULL,
											PRIMARY KEY id (id)
										) $charset_collate;"; //Defining query to create table.
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);//Creating a table name in cureent wordpress
	}
	register_activation_hook( __FILE__, 'create_edugorilla_lead_table' );
	
	add_action("admin_menu","create_menus");
	
	function create_menus() 
	{
			add_object_page(
							'EduGorilla',
							'EduGorilla',
							'read',
							'edugorilla',
							'edugorilla'
						);
						
			add_submenu_page(
                             'EduGorilla',
                             'EduGorilla | Listing',
                             'Listing',
                             'read',
                             'Listing',
                             'form_list'
                            );
	}

	function edugorilla()
	{
		$caller = $_POST['caller'];
		
		if($caller=="self")
		{
        	/** Get Data From Form **/
			$name = $_POST['name'];
			$contact_no = $_POST['contact_no'];
        	$keyword = $_POST['keyword'];
			$email = $_POST['email'];
			$query = $_POST['query'];
        	$location = $_POST['location'];
        	$long = $_POST['long'];
        	$lat = $_POST['lat'];
			$category_id = $_POST['category_id'];
			$distance = $_POST['distance'];
        
        	/** Error Checking **/
			$errors = array();
			if(empty($name))$errors['name']="Empty";
			elseif(!preg_match("/([A-Za-z]+)/", $name)) $errors['name']="Invalid";
        
        	if(empty($keyword)) $errors['keyword']="Empty";
			
			if(empty($contact_no))$errors['contact_no']="Empty";
			elseif(!preg_match("/([0-9]{10}+)/", $contact_no)) $errors['contact_no']="Invalid";
			
			if(empty($email))$errors['email']="Empty";
			elseif(filter_var($email, FILTER_VALIDATE_EMAIL) === false) $errors['email']="Invalid";
			
			if(empty($query))$errors['query']="Empty";
        
        	if(empty($long) or empty($lat) or empty($location))$errors['location']="Empty";
			
			if(empty($category_id))$errors['category_id']="Empty";
			
			if(empty($distance))$errors['distance']="Empty";
			elseif(!preg_match("/([0-9]+)/", $distance)) $errors['distance']="Invalid";
			
			
			if(empty($errors))
			{
            	$category = implode(",",$category_id);
				global $wpdb;
				$wpdb->insert( 
								$wpdb->prefix . 'edugorilla_lead', 
							array( 
								'name' => $name,
                            	'keyword' => $keyword,
								'contact_no' => $contact_no,
								'email' => $email,
								'query' => $query,
                            	'location' => $location,
                            	'longitude' => $long,
                            	'latitude' => $lat,
								'category_id' => $category,
								'distance' => $distance
							)
						);
				$success="Saved Successfully";
            	wp_mail( $email, "Hi", "Hi ".$name);
				foreach($_REQUEST as $var=>$val)$$var="";
			}
		}
?>
    <style>
  
     #map {
        width: 60%;		
        height: 500px;
        border:double;
       }
      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }
    </style>
	

		<div class="wrap">
				<h1>EduGorilla Leads</h1>
			<?php
			if($success)
			{
			?>
				<div class="updated notice">
					<p><?php echo $success;?></p>
				</div>
			<?php
			}
			?>
				<form  name=details method="post">
				 <table class="form-table">
					 <tr>
						 <th>Name<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="name" value="<?php echo $name;?>" placeholder="Type name here...">
							<font color="red"><?php echo $errors['name'];?></font>
						</td>
					</tr>
					 <tr>
						 <th>Contact No.<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="contact_no" value="<?php echo $contact_no;?>" placeholder="Type contact number here">
							<font color="red"><?php echo $errors['contact_no'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Email<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="email" value="<?php echo $email;?>" placeholder="Type email here">
							<font color="red"><?php echo $errors['email'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Query<sup><font color="red">*</font></sup></th>
						 <td>
							<textarea name="query" placeholder="Type your query here"><?php echo $query; ?></textarea>
							<font color="red"><?php echo $errors['query'];?></font>
						</td>
					</tr>
                 	 <tr>
						 <th>Keyword<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="keyword" value="<?php echo $keyword;?>" placeholder="Type keyword here">
							<font color="red"><?php echo $errors['keyword'];?></font>
						</td>
					</tr>
                 	<tr>
						 <th>Location<sup><font color="red">*</font></sup></th>
						 <td>
						     <input id="pac-input" name="location" class="controls" type="text" placeholder="Enter a location">
                             <div id="map"></div><br>
                             <input name="lat" id='latitude'  value="<?php echo $lat; ?>" placeholder="Latitude">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         	 <input name="long" id='longitude'  value="<?php echo $long; ?>" placeholder="Longitude">	
							 <font color="red"><?php echo $errors['location'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Category<sup><font color="red">*</font></sup></th>
						 <td>
							<select name="category_id[]" multiple id="edugorilla_category" class="js-example-basic-single">
								<?php 
    									$temparray = array();
    									$categories = get_terms('listing_categories', array('hide_empty' => false));
    									
    									foreach ($categories as $category) {
                                        	if((int)$category->parent != 0)
                                            {
                                            	$temparray[$category->parent][$category->term_id] = $category->name;
                                            }
                                        }
    					
										foreach ($temparray as $var=>$vals ) {
                                    ?>
                                 
                                        <option value="<?php echo $var; ?>">
                                   	<?php 
                                    	$d = get_term_by('id', $var, 'listing_categories');
                                        echo $d->name;
                                   ?>
                                   		</option>
 										
                            		<?php
											foreach($vals as $index=>$val)
                                            {
                                     ?>
                                        		
                                                <option value="<?php echo $index; ?>">
                                   					<?php echo $val; ?>
                                        		</option>
                                     <?php
                                            }
                                       ?>
                                   		
                                <?php
										}
								?>
							</select>
							<font color="red"><?php echo $errors['category_id'];?></font>
						</td>
					</tr>
					<tr>
						 <th>Distance (in kms)<sup><font color="red">*</font></sup></th>
						 <td>
							<input name="distance" value="<?php echo $distance;?>" placeholder="Type distance here">
							<font color="red"><?php echo $errors['distance'];?></font>
						</td>
					</tr>
					 <tr>
						<th>
							 <input type="hidden" name="caller" value="self">
						</th>
						<td>
                        	
                        	<a href="#confirmation" rel="modal:open" class="button button-primary" onclick="display();">Send Details</a>
						</td>
					 </tr>
				 </table>
			 </form>
		</div>

<!-------Modal------>
<div id="confirmation" style="display:none;">
  	<table>
    	<tr>
        	<th>Name</th>
        	<th>Contact No.</th>
        	<th>Email</th>
			<th>Query</th>
        	<th>Keyword</th>
        	<th>Location</th>
    	</tr>
    	<tr>
        	<th id="cnf_name"></th>
        	<th id="cnf_contact_no"></th>
        	<th id="cnf_email"></th>
			<th id="cnf_query"></th>
        	<th id="cnf_keyword"></th>
        	<th id="cnf_location"></th>    
    	</tr>
	</table>
	<center>
    	<button id="confirm" onclick="document.details.submit();">Confirm</button>
	</center>
</div>
<!---/Modal-------->
<script>
function display()
{
var x=document.details.name.value;
var y=document.details.contact_no.value;
var z=document.details.email.value;
var a=document.details.query.value;
var b=document.details.keyword.value;
var c=document.details.location.value;

document.getElementById("cnf_name").innerHTML=x;
document.getElementById("cnf_contact_no").innerHTML=y;
document.getElementById("cnf_email").innerHTML=z;
document.getElementById("cnf_query").innerHTML=a;
document.getElementById("cnf_keyword").innerHTML=b;
document.getElementById("cnf_location").innerHTML=c;

}

function initMap() 
{
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -33.8688, lng: 151.2195},
          zoom: 13
        });
		
        var input = (
            document.getElementById('pac-input'));

        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });
        google.maps.event.addListener(map, 'center_changed', function () {
            var location = map.getCenter();
            document.getElementById("latitude").value = location.lat();

            document.getElementById("longitude").value = location.lng();
            // call function to reposition marker location
            placeMarker(location);
        });
        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {

            window.alert("No details available for input: '" + place.name + "'");
            return;
          }
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17); 
          }
          marker.setIcon(({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
          }));
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
        });
      }       
      initMap();
    </script>
    
<?php
	}
	function script() 
	{
     
    wp_enqueue_style( 'select2-css', plugins_url( '/css/select2.css', __FILE__ ));
    wp_enqueue_style( 'modal-css', plugins_url( '/css/jquery.modal.css', __FILE__ ));

     wp_enqueue_script( 
        'select2-script',                         // Handle
        plugins_url( '/js/select2.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependancies
    );
    wp_enqueue_script( 
        'modal-script',                         // Handle
        plugins_url( '/js/jquery.modal.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependancies
    );
    wp_enqueue_script( 
        'script',                         // Handle
        plugins_url( '/js/script.js', __FILE__ ),  // Path to file
        array( 'jquery' )                             // Dependancies
    );
   }

add_action( 'admin_enqueue_scripts', 'script', 2000 );
add_action( 'wp_enqueue_scripts', 'script', 1000 );

include_once plugin_dir_path( __FILE__ )."list.php";
?>