<?php
	$db = new PDO('mysql:host=localhost;dbname='.$_REQUEST["system"].';charset=utf8', $_REQUEST["dbuser"], $_REQUEST["dbpassword"]);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$type = $_REQUEST["type"];
	
	if($type == "companies") {
	
		$getCompanies = $db->query("SELECT * FROM company ORDER BY name ASC");
		$companies = $getCompanies->fetchAll(PDO::FETCH_ASSOC);
		
		$companyArray = array();
		
		foreach($companies as $company) {
		
			$companyEntry = array("company_name" => $company["name"], "country" => $company["country"], "region" => $company["region"], "product" => $company["product"], "rus_agree" => $company["russia_agreement"], "license" => $company["license"]);
			array_push($companyArray,$companyEntry);
		}
		
		$data["data"] = $companyArray;
		echo json_encode($data);
		
	} else if ($type == "products") {
	
		$getProducts = $db->query("SELECT * FROM product ORDER BY name ASC");
		$products = $getProducts->fetchAll(PDO::FETCH_ASSOC);
		
		$productArray = array();
		
		foreach($products as $product) {
		
			$productEntry = array("id" => $product["id"], "name" => ucfirst($product["name"]), "category" => ucfirst($product["category"]), "subcategory" => ucfirst($product["subcategory"]), "variant" => ucfirst($product["variant"]), "grade" => strtoupper($product["specification_grade"]),"product_weight" => $product["weight_min"]." - ".$product["weight_max"].$product["weight_unit"], "packaging" => ucfirst($product["packaging"]), "code" => strtoupper($product["prefix"].$product["code"]));
			array_push($productArray,$productEntry);
		}
	
		$data["data"] = $productArray;
		echo json_encode($data);
	
	
	} else if ($type == "product") {
		
		$productId = $_GET["id"];
		
		$getProduct = $db->prepare("SELECT * FROM product WHERE id = ? LIMIT 1");
		$getProduct->execute(array($productId));
		$product = $getProduct->fetchAll(PDO::FETCH_ASSOC);
		$product = $product[0];
		
		$getDescription = $db->prepare("SELECT * FROM product_descriptions WHERE product_id = ? ORDER BY id ASC");
		$getDescription->execute(array($productId));
		$descriptions = $getDescription->fetchAll(PDO::FETCH_ASSOC);
		
		$getImages = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY id ASC");
		$getImages->execute(array($productId));
		$images = $getImages->fetchAll(PDO::FETCH_ASSOC);
		
		
		//get images from folder for now
		if(is_dir('resources/products/images/'.strtolower($product["prefix"]).'/'.$product["code"])) {
			$images = glob('resources/products/images/'.strtolower($product["prefix"]).'/'.$product["code"].'/*.{jpeg,gif,png,jpg}', GLOB_BRACE);
		} else {
			$images = glob('resources/products/images/'.strtolower($product["prefix"]).'/*.{jpeg,gif,png,jpg,JPG,PNG,GIF,JPEG}', GLOB_BRACE);
		}
		
		/*print "<ul>
				<li><b>Category</b> : ".ucfirst($product["category"])."</li>
			   	<li><b>Subcategory</b> : ".ucfirst($product["subcategory"])."</li>
			   	<li><b>Variant</b> : ".ucfirst($product["variant"])."</li>
			   	<li><b>Product Code</b> : ".strtoupper($product["prefix"].$product["code"])."</li>
			   	<li><b>Specification Grade</b> : ".ucfirst($product["specification_grade"])."</li>
			   	<li><b>Product Weight</b> : ".$product["weight_min"]." - ".$product["weight_max"]." ".ucfirst($product["weight_unit"])."
			   	 	<br>".ucfirst($product["weight_text"])."</li>
			   	<li><b>Container Weight</b> : ".$product["container_weight_min"]." - ".$product["container_weight_max"]." ".ucfirst($product["container_weight_unit"])."
			   		<br>".ucfirst($product["container_weight_text"])."</li>
			   	<li><b>Packaging</b> : ".ucfirst($product["packaging"])."</li>
			   	<li><b>Label</b> : ".ucfirst($product["label"])."</li>
			   	<li><b>Requirements</b> : ".ucfirst($product["requirements"])."</li>
			   </ul>";*/
			
		//fix spec grade if multiples   
		$specificationGrade = implode('/',str_split($product["specification_grade"])); 
			   
		print '<table class = "table">
					<caption><h4>ISF Global Product Specification - '.ucwords($product["name"]).'</h4></caption>
					<tr>
					  <th>Product Code</th>
						<td class = "pdfTitle">'.strtoupper($product["prefix"].$product["code"]).'</td>
					</tr>
					<tr>
					  <th>Description</th>
						<td>
							<ul style = "padding-left:1em;">';
							foreach($descriptions as $description) {
								print '<li>'.ucfirst($description["description"]).'</li>';
							}
					print '</ul>						
						</td>
					</tr>
					<tr>
					  <th>Category</th>
						<td>'.ucfirst($product["category"]).'</td>
					</tr>
					<tr>
					  <th>Subcategory</th>
						<td>'.ucfirst($product["subcategory"]).'</td>
					</tr>
					<tr>
					  <th>Variant</th>
						<td>'.ucfirst($product["variant"]).'</td>
					</tr>
					<tr>
					  <th>Specification Grade</th>
						<td>'.strtoupper($specificationGrade).'</td>
					</tr>
					<tr>
					  <th>Product Weight</th>
						<td>'.$product["weight_min"]." - ".$product["weight_max"]." ".$product["weight_unit"]."
			   	 			<br>".ucfirst($product["weight_text"]).'</td>
					</tr>
					<tr>
					  <th>Container Weight</th>
						<td>'.$product["container_weight_min"]." - ".$product["container_weight_max"]." ".$product["container_weight_unit"]."
			   				<br>".ucfirst($product["container_weight_text"]).'</td>
					</tr>
					<tr>
					  <th>Packaging</th>
						<td>'.ucfirst($product["packaging"]).'</td>
					</tr>
					<tr>
					  <th>Label</th>
						<td>'.ucfirst($product["label"]).'</td>
					</tr>
					<tr>
					  <th>Requirements</th>
						<td>'.ucfirst($product["requirements"]).'</td>
					</tr>
				</table>';
				
				
				if(!empty($images)) {
						   
					print '<table class = "table">';
				
					$imgCount = 0;
				   foreach($images as $image) {

						if($imgCount == 0) {
							print '<tr>';
						}
						print	'<td style = "padding:10px;">
									<img src = "'.$image.'" style = "max-width:95%;min-width:170px!important;border-radius:0px;" />
								</td>';
						if($imgCount == 1) {
							print '</tr>';
							$imgCount = 0;
						}
						$imgCount++;
					}
				
				   print '</table>';
				}
	} else if($type == "process_order") {
	
		$selected_products = $_GET["selected_products"];
		$selected_products = explode(",",$selected_products);
		
		$getTerms = $db->query("SELECT * FROM terms_conditions ORDER BY type,version ASC");
		$terms = $getTerms->fetchAll(PDO::FETCH_ASSOC);
		
		print '<form id = "poForm">
					<input type = "hidden" id = "selected_products" name = "selected_products" value = "'.$_GET["selected_products"].'" />
					<input type = "hidden" id = "function" name = "function" value = "createPO" />
					<table class = "table">
						<thead>
							<th>Product Name</th>
							<th>Code</th>
							<th>Quantity</th>
							<th>Quantity Type</th>
							<th>Unit Price ($)</th>
							<th>Total Price ($)</th>
						</thead>
						
						<tbody>';
						
					
		foreach($selected_products as $product) {
		
			$getProduct = $db->prepare("SELECT * FROM product WHERE id = ? LIMIT 1");
			$getProduct->execute(array($product));
			$product = $getProduct->fetchAll(PDO::FETCH_ASSOC);
			$product = $product[0];
			
			print '<tr>
						<td>'.ucwords($product["name"]).'</td>
						<td>'.strtoupper($product["prefix"].$product["code"]).'</td>
						<td><input id = "'.$product["id"].'_quantity" name = "'.$product["id"].'_quantity" data-product-id = "'.$product["id"].'" class = "productInfo calcListen" type = "number" value = "0" /></td>
						<td><select id = "'.$product["id"].'_quantity_type" name = "'.$product["id"].'_quantity_type">
								<option value = "T">Tons</option>
								<option value = "Q">Quantity</option>
								<option value = "C">Containers</option>
							</select>
						</td>
						<td><input id = "'.$product["id"].'_unit_price" name = "'.$product["id"].'_unit_price" data-product-id = "'.$product["id"].'" class = "productInfo calcListen" type = "text" value = "0" /></td>
						<td><input id = "'.$product["id"].'_total_price" name = "'.$product["id"].'_total_price" data-product-id = "'.$product["id"].'" class = "productInfo calcListen subTotal" type = "text" readonly = "true" value = "0"/></td>
					</tr>';
			
		}
		
		//total row
		print '<tr><td></td><td></td><td></td><td></td><td><b>Total : </td><td><input type = "text" id = "orderTotal" name = "orderTotal" value = "0" readonly="true" /></b></td></tr>';
		
		print '</tbody>
			</table>';
			
		print '<div class="form-group row" style = "width:47%;padding:2%;vertical-align:top;display:inline-block;">
				  
				  <label for="order_date" class="col-2 col-form-label">Order Date</label>
				  <div class="col-10">
					<select id = "document_type" name = "document_type" class = "form-control">
						<option value = "purchaseorder">Purchase Order</option>
						<option value = "proformainvoice">Proforma Invoice</option>
					</select>
				  </div>
				  
				  <label for="order_date" class="col-2 col-form-label">Order Date</label>
				  <div class="col-10">
					<input class="form-control" type="date" value = "'.date('Y-m-d').'" id="order_date" name = "order_date">
				  </div>
				  
				  <label for="payment_date" class="col-2 col-form-label">Payment Date</label>
				  <div class="col-10">
					<input class="form-control" type="date" value = "'.date('Y-m-d').'" id="payment_date" name = "payment_date">
				  </div>
				  
				  <label for="delivery_date" class="col-2 col-form-label">Delivery Date (est)</label>
				  <div class="col-10">
					<input class="form-control" type="date" value = "'.date('Y-m-d').'" id="delivery_date" name = "delivery_date">
				  </div>
				  
				  <label for="shipping_date" class="col-2 col-form-label">Shipping Date (est)</label>
				  <div class="col-10">
					<input class="form-control" type="date" value = "'.date('Y-m-d').'" id="shipping_date" name = "shipping_date">
				  </div>
				  
				  <label for="loading_date" class="col-2 col-form-label">Loading Date (est)</label>
				  <div class="col-10">
					<input class="form-control" type="date" value = "'.date('Y-m-d').'" id="loading_date" name = "loading_date">
				  </div>
				  
				  <label for="destination_country" class="col-2 col-form-label">Destination Country</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="destination_country" name = "destination_country" placeholder = "e.g Vietnam">
				  </div>
				  
				  <!--<label for="delivery_basis" class="col-2 col-form-label">Delivery Basis (Incoterms)</label>
				  <div class="col-10">
					<input class="form-control" type="text" id="delivery_basis" name = "delivery_basis" placeholder = "e.g FCA">
				  </div>-->
				  
				  <label for="shipping_from" class="col-2 col-form-label">Shipping From</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_from" name = "shipping_from" placeholder = "">
				  </div>
				  
				  <label for="terms" class="col-2 col-form-label">Terms & Conditions</label>
				  <div class="col-10">
					<select id = "terms" name = "terms" class = "form-control">
						<option value = "">Choose</option>';
						foreach($terms as $term) {
							print '<option value = "'.$term["id"].'" data-toggle="tooltip" data-placement="left" title = "'.$term["content"].'">'.$term["type"].' version '.$term["version"].'</option>';
						}
			print '</select>			
				  </div>
				</div>
				<div class="form-group row" style = "width:45%;padding:2%;margin-left:5%;vertical-align:top;display:inline-block;">
				 
				  <label for="shipping_address_1" class="col-2 col-form-label">Shipping Address</label>
				  <div class="col-10">
					<input class="form-control" type="text" id="shipping_address_1" name = "shipping_address_1">
					<input class="form-control" type="text" id="shipping_address_2" name = "shipping_address_2">
					<input class="form-control" type="text" id="shipping_address_3" name = "shipping_address_3">
					<input class="form-control" type="text" id="shipping_address_4" name = "shipping_address_4">
				  </div>
				  
				  <label for="shipping_company" class="col-2 col-form-label">Shipping Company</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_company" name = "shipping_company" placeholder = "">
				  </div>
				  
				  <label for="shipping_method" class="col-2 col-form-label">Shipping Method</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_method" name = "shipping_method" placeholder = "">
				  </div>
				  
				  <label for="shipping_reference" class="col-2 col-form-label">Shipping Reference</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_reference" name = "shipping_reference" placeholder = "">
				  </div>
				  
				  <label for="shipping_contact_name" class="col-2 col-form-label">Shipping Contact Name</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_contact_name" name = "shipping_contact_name" placeholder = "">
				  </div>
				  
				  <label for="shipping_contact_number" class="col-2 col-form-label">Shipping Contact Number</label>
				  <div class="col-10">
					<input class="form-control" type="text" value="" id="shipping_contact_number" name = "shipping_contact_number" placeholder = "">
				  </div>
				  
				  <label for="invoiced_to_1" class="col-2 col-form-label">Invoiced To</label>
				  <div class="col-10">
					<input class="form-control" type="text" id="invoiced_to_1" name = "invoiced_to_1" value = "ISF Global Limited">
					<input class="form-control" type="text" id="invoiced_to_2" name = "invoiced_to_2" value = "Unit 2, Longs Business Centre">
					<input class="form-control" type="text" id="invoiced_to_3" name = "invoiced_to_3" value = "232 Fakenham Road">
					<input class="form-control" type="text" id="invoiced_to_4" name = "invoiced_to_4" value = "NR8 6QW">
				  </div>
				</div>
				
				<!--<label for = "terms" class = "col-2 col-form-label">Terms & Conditions</label>
				<div class="col-10">
  					<textarea class="form-control" rows="5" id="terms" name = "terms"></textarea>
  				</div>-->';
			
			
			
			
		print '</form>';
	
	
	}
		
?>