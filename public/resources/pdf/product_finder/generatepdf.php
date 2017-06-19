<?php
//define db connection from post/get request
$db = new PDO('mysql:host=localhost;dbname='.$_REQUEST["system"].';charset=utf8', $_REQUEST["dbuser"], $_REQUEST["dbpassword"]);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// include autoloader
require_once '../../php/dompdf/autoload.inc.php';
//create dompdf namespace
use Dompdf\Dompdf;

//what pdf file are we generating
$document_type = $_REQUEST["type"];

//initialise new pdf object
$dompdf = new Dompdf();

if($document_type == "product_specification") {

	$productid = $_REQUEST["productid"];
	
	$getProduct = $db->prepare("SELECT * FROM product WHERE id = ? LIMIT 1");
	$getProduct->execute(array($productid));
	$product = $getProduct->fetchAll(PDO::FETCH_ASSOC);
	$product = $product[0];
	
	$getDescription = $db->prepare("SELECT * FROM product_descriptions WHERE product_id = ? ORDER BY id ASC");
	$getDescription->execute(array($productid));
	$descriptions = $getDescription->fetchAll(PDO::FETCH_ASSOC);
	
	//get images from folder for now
	if(is_dir('resources/products/images/'.strtolower($product["prefix"]).'/'.$product["code"])) {
		$images = glob('resources/products/images/'.strtolower($product["prefix"]).'/'.$product["code"].'/*.{jpeg,gif,png,jpg}', GLOB_BRACE);
	} else {
		$images = glob('resources/products/images/'.strtolower($product["prefix"]).'/*.{jpeg,gif,png,jpg,JPG,PNG,GIF,JPEG}', GLOB_BRACE);
	}
	
	//fix spec grade if multiples   
	$specificationGrade = implode('/',str_split($product["specification_grade"]));
	
	$pdfTitle = 'ISF Global Product Specification - '.ucwords($product["name"]);
	
	//include standard headers,footers and styles.
	include_once('pdf_styles.php');
	
	$pdfHTML = '<table class = "table table-striped" cellspacing="0" width="100%">
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
								$pdfHTML .= '<li>'.ucfirst($description["description"]).'</li>';
							}
					$pdfHTML .= '</ul>						
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
						   
					$pdfHTML .= '<table class = "table table-striped" cellspacing="0" width="100%">';
				
					$imgCount = 0;
				   foreach($images as $image) {

						if($imgCount == 0) {
							$pdfHTML .= '<tr>';
						}
						$pdfHTML .=	'<td style = "padding:10px;">
									<img src = "'.$image.'" style = "max-width:95%;min-width:170px!important;border-radius:0px;" />
								</td>';
						if($imgCount == 1) {
							$pdfHTML .= '</tr>';
							$imgCount = 0;
						}
						$imgCount++;
					}
				
				   $pdfHTML .= '</table>';
				}
				
	$dompdf->setPaper('A4', 'portrait');
	$pdfName = "ISF_Global_".strtoupper($product["prefix"].$product["code"]);
	



} else if($document_type == "purchaseorder") {

	$porderid = $_REQUEST["orderid"];
	
	$displayOrderId =  "ISFP".str_pad($porderid,6,"0",STR_PAD_LEFT);
	
	$pdfTitle = "Purchase Order :</span> ".$displayOrderId;
	
	//include standard headers,footers and styles.
	include_once('pdf_styles.php');
	
	//get the purchase order
	$getPO = $db->prepare("SELECT * FROM purchase_order WHERE id = ? LIMIT 1");
	$getPO->execute(array($porderid));
	$purchaseOrder = $getPO->fetchAll(PDO::FETCH_ASSOC);
	$purchaseOrder = $purchaseOrder[0];
	
	$getTerms = $db->prepare("SELECT * FROM terms_conditions WHERE id = ? LIMIT 1");
	$getTerms->execute(array($purchaseOrder["terms"]));
	$terms = $getTerms->fetchAll(PDO::FETCH_ASSOC);
	$termsconditions = $terms[0]["content"];
	
	//get the products for the purchase order
	$getProducts = $db->prepare("SELECT * FROM purchase_order_products WHERE purchase_order_id = ? ORDER BY id ASC");
	$getProducts->execute(array($porderid));
	$poproducts = $getProducts->fetchAll(PDO::FETCH_ASSOC);
	
	
	
	$pdfHTML = '<div class = "poInfo">
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">PO Number :</span> '.$displayOrderId.'</li>
								<li><span class = "poInfoLabel">Order Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["order_date"])).'</li>
								<li><span class = "poInfoLabel">Payment Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["payment_date"])).'</li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Invoiced To :</span> '.$purchaseOrder["invoiced_to"].'</li>
							</ul>
						</div>
						
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping Address :</span> '.$purchaseOrder["shipping_address"].'</li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Destination Country :</span> '.$purchaseOrder["destination_country"].'</li>
								<li><span class = "poInfoLabel">Delivery Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["delivery_date"])).'</li>
								<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> '.$purchaseOrder["delivery_basis"].'</li>
							</ul>						
						</div>
						
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping To :</span> '.$purchaseOrder["shipping_to"].'</li>
								<li><span class = "poInfoLabel">Shipping Company :</span> '.$purchaseOrder["shipping_company"].'</li>
								<li><span class = "poInfoLabel">Shipping Method :</span> '.$purchaseOrder["shipping_method"].'</li>
								<li><span class = "poInfoLabel">Shipping Reference :</span> '.$purchaseOrder["shipping_reference"].'</li>
								<li><span class = "poInfoLabel">Shipping Contact Name :</span> '.$purchaseOrder["shipping_contact_name"].'</li>
								<li><span class = "poInfoLabel">Shipping Contact Number :</span> '.$purchaseOrder["shipping_contact_number"].'</li>
								<li><span class = "poInfoLabel">Shipping Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["shipping_date"])).'</li>
								<li><span class = "poInfoLabel">Loading Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["loading_date"])).'</li>
							</ul>
						</div>
					</div>
					
					<table class = "table table-striped" style = "width:100%!important;font-size:60%!important;">
						<thead>
							<tr style = "white-space: nowrap!important;">
								<th>#</th>
								<th>Product Code</th>
								<th>Description</th>
								<th>Specification</th>
								<th>Quantity (T)</th>							
								<th>Unit Price</th>
								<th>Total Ex VAT</th>
								<th>Total VAT</th>
								<th>Total Inc VAT</th>
							</tr>
						</thead>';
						
						$itemCount = 1;
						
						foreach($poproducts as $poproduct) {
						
							
							$getProduct = $db->prepare("SELECT * FROM product WHERE id = ? LIMIT 1");
							$getProduct->execute(array($poproduct["product_id"]));
							$product = $getProduct->fetchAll(PDO::FETCH_ASSOC);
							$product = $product[0];
							
							$pdfHTML .= '<tr>
										<td>'.$itemCount.'</td>
										<td>'.strtoupper($product["prefix"].$product["code"]).'</td>
										<td>'.ucwords($product["name"]).'</td>
										<td>As per attached (ISF_Global_'.strtoupper($product["prefix"].$product["code"]).')</td>
										<td>'.$poproduct["quantity"].'</td>
										<td>$'.$poproduct["unit_price"].'</td>
										<td>$'.$poproduct["total_price_ex_vat"].'</td>
										<td>$'.$poproduct["total_price_vat"].'</td>
										<td>$'.$poproduct["total_price"].'</td>
									</tr>';
									
							$itemCount++;
						}
						
						$pdfHTML .= '<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td><b>Total : </b></td>
										<td><b>$'.$purchaseOrder["total_price"].'</b></td>
									</tr>';
						
						$pdfHTML .= '</tbody>
							</table>';
							
						$pdfHTML .= $poSignature;
						
						//second page
						
						$pdfHTML .= '<div style = "page-break-before:always;">
										<div class = "poInfo">
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">PO Number :</span> '.$displayOrderId.'</li>
													<li><span class = "poInfoLabel">Order Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["order_date"])).'</li>
													<li><span class = "poInfoLabel">Payment Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["payment_date"])).'</li>
													<li><span class = "poInfoLabel">&nbsp;</span></li>
													<li><span class = "poInfoLabel">Invoiced To :</span> '.$purchaseOrder["invoiced_to"].'</li>
												</ul>
											</div>
						
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">Shipping Address :</span> '.$purchaseOrder["shipping_address"].'</li>
													<li><span class = "poInfoLabel">&nbsp;</span> </li>
													<li><span class = "poInfoLabel">&nbsp;</span> </li>
													<li><span class = "poInfoLabel">&nbsp;</span></li>
													<li><span class = "poInfoLabel">Destination Country :</span> '.$purchaseOrder["destination_country"].'</li>
													<li><span class = "poInfoLabel">Delivery Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["delivery_date"])).'</li>
													<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> '.$purchaseOrder["delivery_basis"].'</li>
												</ul>						
											</div>
						
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">Shipping Company :</span> '.$purchaseOrder["shipping_company"].'</li>
													<li><span class = "poInfoLabel">Shipping Method :</span> '.$purchaseOrder["shipping_method"].'</li>
													<li><span class = "poInfoLabel">Shipping Reference :</span> '.$purchaseOrder["shipping_reference"].'</li>
													<li><span class = "poInfoLabel">Shipping Contact Name :</span> '.$purchaseOrder["shipping_contact_name"].'</li>
													<li><span class = "poInfoLabel">Shipping Contact Number :</span> '.$purchaseOrder["shipping_contact_number"].'</li>
													<li><span class = "poInfoLabel">Shipping Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["shipping_date"])).'</li>
													<li><span class = "poInfoLabel">Loading Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["loading_date"])).'</li>
												</ul>
											</div>
										</div>';
										
										$pdfHTML .= '<div class = "terms">
														<h3><b>Terms & Conditions</b></h3>
														<p>'.nl2br($termsconditions).'</p>
													</div><br></br>';
										
										
										$pdfHTML .= $poSignature;
									
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('A4', 'landscape');
	$pdfName = "ISF_Global_".$displayOrderId;


} else if($document_type == "proformainvoice") {

	$porderid = $_REQUEST["orderid"];
	
	$displayOrderId =  "ISFI".str_pad($porderid,6,"0",STR_PAD_LEFT)."P";
	
	$pdfTitle = "Proforma Invoice :</span> ".$displayOrderId;
	
	//include standard headers,footers and styles.
	include_once('pdf_styles.php');
	
	//get the purchase order
	$getPO = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
	$getPO->execute(array($porderid));
	$purchaseOrder = $getPO->fetchAll(PDO::FETCH_ASSOC);
	$purchaseOrder = $purchaseOrder[0];
	
	$getTerms = $db->prepare("SELECT * FROM terms_conditions WHERE id = ? LIMIT 1");
	$getTerms->execute(array($purchaseOrder["terms"]));
	$terms = $getTerms->fetchAll(PDO::FETCH_ASSOC);
	$termsconditions = $terms[0]["content"];
	
	//get the products for the purchase order
	$getProducts = $db->prepare("SELECT * FROM order_products WHERE purchase_order_id = ? ORDER BY id ASC");
	$getProducts->execute(array($porderid));
	$poproducts = $getProducts->fetchAll(PDO::FETCH_ASSOC);
	
	
	
	$pdfHTML = '<div class = "poInfo">
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Invoice Number :</span> '.$displayOrderId.'</li>
								<li><span class = "poInfoLabel">Invoice Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["order_date"])).'</li>
								<li><span class = "poInfoLabel">Payment Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["payment_date"])).'</li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Invoiced To :</span> '.$purchaseOrder["invoiced_to"].'</li>
							</ul>
						</div>
						
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping Address :</span> '.$purchaseOrder["shipping_address"].'</li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span> </li>
								<li><span class = "poInfoLabel">&nbsp;</span></li>
								<li><span class = "poInfoLabel">Destination Country :</span> '.$purchaseOrder["destination_country"].'</li>
								<li><span class = "poInfoLabel">Delivery Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["delivery_date"])).'</li>
								<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> '.$purchaseOrder["delivery_basis"].'</li>
							</ul>						
						</div>
						
						<div class = "poInfoSplit">
							<ul>
								<li><span class = "poInfoLabel">Shipping To :</span> '.$purchaseOrder["shipping_to"].'</li>
								<li><span class = "poInfoLabel">Shipping Company :</span> '.$purchaseOrder["shipping_company"].'</li>
								<li><span class = "poInfoLabel">Shipping Method :</span> '.$purchaseOrder["shipping_method"].'</li>
								<li><span class = "poInfoLabel">Shipping Reference :</span> '.$purchaseOrder["shipping_reference"].'</li>
								<li><span class = "poInfoLabel">Shipping Contact Name :</span> '.$purchaseOrder["shipping_contact_name"].'</li>
								<li><span class = "poInfoLabel">Shipping Contact Number :</span> '.$purchaseOrder["shipping_contact_number"].'</li>
								<li><span class = "poInfoLabel">Shipping Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["shipping_date"])).'</li>
								<li><span class = "poInfoLabel">Loading Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["loading_date"])).'</li>
							</ul>
						</div>
					</div>
					
					<table class = "table table-striped" style = "width:100%!important;font-size:60%!important;">
						<thead>
							<tr style = "white-space: nowrap!important;">
								<th>#</th>
								<th>Product Code</th>
								<th>Description</th>
								<th>Specification</th>
								<th>Quantity (T)</th>							
								<th>Unit Price</th>
								<th>Total Ex VAT</th>
								<th>Total VAT</th>
								<th>Total Inc VAT</th>
							</tr>
						</thead>';
						
						$itemCount = 1;
						
						foreach($poproducts as $poproduct) {
						
							
							$getProduct = $db->prepare("SELECT * FROM product WHERE id = ? LIMIT 1");
							$getProduct->execute(array($poproduct["product_id"]));
							$product = $getProduct->fetchAll(PDO::FETCH_ASSOC);
							$product = $product[0];
							
							$pdfHTML .= '<tr>
										<td>'.$itemCount.'</td>
										<td>'.strtoupper($product["prefix"].$product["code"]).'</td>
										<td>'.ucwords($product["name"]).'</td>
										<td>As per attached (ISF_Global_'.strtoupper($product["prefix"].$product["code"]).')</td>
										<td>'.$poproduct["quantity"].'</td>
										<td>$'.$poproduct["unit_price"].'</td>
										<td>$'.$poproduct["total_price_ex_vat"].'</td>
										<td>$'.$poproduct["total_price_vat"].'</td>
										<td>$'.$poproduct["total_price"].'</td>
									</tr>';
									
							$itemCount++;
						}
						
						$pdfHTML .= '<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td><b>Total : </b></td>
										<td><b>$'.$purchaseOrder["total_price"].'</b></td>
									</tr>';
						
						$pdfHTML .= '</tbody>
							</table>';
							
						$pdfHTML .= $poSignature;
						
						//second page
						
						$pdfHTML .= '<div style = "page-break-before:always;">
										<div class = "poInfo">
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">PO Number :</span> '.$displayOrderId.'</li>
													<li><span class = "poInfoLabel">Order Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["order_date"])).'</li>
													<li><span class = "poInfoLabel">Payment Date :</span> '.date("d/m/Y", strtotime($purchaseOrder["payment_date"])).'</li>
													<li><span class = "poInfoLabel">&nbsp;</span></li>
													<li><span class = "poInfoLabel">Invoiced To :</span> '.$purchaseOrder["invoiced_to"].'</li>
												</ul>
											</div>
						
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">Shipping Address :</span> '.$purchaseOrder["shipping_address"].'</li>
													<li><span class = "poInfoLabel">&nbsp;</span> </li>
													<li><span class = "poInfoLabel">&nbsp;</span> </li>
													<li><span class = "poInfoLabel">&nbsp;</span></li>
													<li><span class = "poInfoLabel">Destination Country :</span> '.$purchaseOrder["destination_country"].'</li>
													<li><span class = "poInfoLabel">Delivery Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["delivery_date"])).'</li>
													<li><span class = "poInfoLabel">Delivery Basis (Incoterms) :</span> '.$purchaseOrder["delivery_basis"].'</li>
												</ul>						
											</div>
						
											<div class = "poInfoSplit">
												<ul>
													<li><span class = "poInfoLabel">Shipping Company :</span> '.$purchaseOrder["shipping_company"].'</li>
													<li><span class = "poInfoLabel">Shipping Method :</span> '.$purchaseOrder["shipping_method"].'</li>
													<li><span class = "poInfoLabel">Shipping Reference :</span> '.$purchaseOrder["shipping_reference"].'</li>
													<li><span class = "poInfoLabel">Shipping Contact Name :</span> '.$purchaseOrder["shipping_contact_name"].'</li>
													<li><span class = "poInfoLabel">Shipping Contact Number :</span> '.$purchaseOrder["shipping_contact_number"].'</li>
													<li><span class = "poInfoLabel">Shipping Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["shipping_date"])).'</li>
													<li><span class = "poInfoLabel">Loading Date (Est) :</span> '.date("d/m/Y", strtotime($purchaseOrder["loading_date"])).'</li>
												</ul>
											</div>
										</div>';
										
										$pdfHTML .= '<div class = "terms">
														<h3><b>Terms & Conditions</b></h3>
														<p>'.nl2br($termsconditions).'</p>
													</div><br></br>';
										
										
										$pdfHTML .= $poSignature;
									
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('A4', 'landscape');
	$pdfName = "ISF_Global_".$displayOrderId;


}



//load all created html entities
$dompdf->loadHtml($pdfStyles.$header.$header2.$footer.$pdfHTML);

// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream($pdfName.".pdf");

//output not as download
//$dompdf->stream('specsheet.pdf',array('Attachment'=>0));

?>