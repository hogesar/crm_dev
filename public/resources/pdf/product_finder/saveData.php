<?php
	$db = new PDO('mysql:host=localhost;dbname='.$_REQUEST["system"].';charset=utf8', $_REQUEST["dbuser"], $_REQUEST["dbpassword"]);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$function = $_REQUEST["function"];
	
	if($function == "createPO") {
		//creating an order, of any type
		if($_POST["document_type"] == "purchaseorder") {
			$maintable = "purchase_order";
			$prodtable = "purchase_order_products";
		} else if ($_POST["document_type"] == "proformainvoice") {
			$maintable = "orders";
			$prodtable = "order_products";
		}
		
	
		//define array thats going to hold ids for generating links
		$genIds = array();
		
		try {
		
			//get terms selected so we know what the incoterms are
			$getTerms = $db->prepare("SELECT * FROM terms_conditions WHERE id = ? LIMIT 1");
			$getTerms->execute(array($_POST["terms"]));
			$terms = $getTerms->fetchAll(PDO::FETCH_ASSOC);
			$terms = $terms[0];
			$delivery_basis = ucwords($terms["type"]);
			
		
			//creating a purchase order, need to insert the basic order.
			$insertPO = $db->prepare("INSERT INTO ".$maintable." (client_id,bank_id,order_date,payment_date,delivery_date,shipping_date,loading_date,
			destination_country,delivery_basis,shipping_from,shipping_address,shipping_company,shipping_method,shipping_reference,shipping_contact_name,
			shipping_contact_number,invoiced_to,total_price_ex_vat,total_price_vat,total_price,terms) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		
		
			//merge address posts to one var
			$shipping_address = $_POST["shipping_address_1"].", ".$_POST["shipping_address_2"].", ".$_POST["shipping_address_3"].", ".$_POST["shipping_address_4"];
			$shipping_address = rtrim($shipping_address,', ');
		
		
			$invoiced_to = $_POST["invoiced_to_1"].", ".$_POST["invoiced_to_2"].", ".$_POST["invoiced_to_3"].", ".$_POST["invoiced_to_4"];
			$invoiced_to = rtrim($invoiced_to,', ');
		
		
			$insertPO->execute(array("","",$_POST["order_date"],$_POST["payment_date"],$_POST["delivery_date"],$_POST["shipping_date"],
			$_POST["loading_date"],$_POST["destination_country"],$delivery_basis,$_POST["shipping_from"],$shipping_address,$_POST["shipping_company"],
			$_POST["shipping_method"],$_POST["shipping_reference"],$_POST["shipping_contact_name"],$_POST["shipping_contact_number"],$invoiced_to,"","",$_POST["orderTotal"],$_POST["terms"]));
		
		} catch(Exception $e) {
			
			echo 'Exception -> ';
			var_dump($e->getMessage());
		
		}
		
		$orderId = $db->lastInsertId();
		
		$genIds["order"][] = $orderId;
		
		
		//now we can insert products into the purchase_order_products_table
		$selected_products = explode(",",$_POST["selected_products"]);
		
		foreach($selected_products as $prodid) {
		
			try {
		
				$insertPOProd = $db->prepare("INSERT INTO ".$prodtable." (product_id,purchase_order_id,unit_price,quantity,quantity_type,total_price_ex_vat,
				total_price_vat,total_price) VALUES (?,?,?,?,?,?,?,?)");
			
				$insertPOProd->execute(array($prodid,$orderId,$_POST[$prodid."_unit_price"],$_POST[$prodid."_quantity"],$_POST[$prodid."_quantity_type"],"","",
				$_POST[$prodid."_total_price"]));
				
				$genIds["products"][] = $prodid;
			
			} catch(Exception $e) {
			
				echo 'Exception -> ';
				var_dump($e->getMessage());
		
			}
		
		}
		
		print json_encode($genIds);
		
	}

	
		
?>