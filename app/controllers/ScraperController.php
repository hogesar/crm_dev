<?php

use Goutte\Client;

class ScraperController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$client = new Client();
		if (!$id) {
			$id = "51288142";
		}
		$url = 'http://www.rightmove.co.uk/property-for-sale/property-' . $id . '.html?showcase=false';
		$crawler = $client->request('GET', $url);

		$aPropData = array();

		$domCSSPath = 'div.property-header-bedroom-and-price > div > h1'; 
		$aPropData["title"] = $crawler->filter($domCSSPath)->each(function ($node) {
			return trim($node->text());
		});

		$domCSSPath = 'div.property-header-bedroom-and-price > div > address'; 
		$aPropData["address"] = $crawler->filter($domCSSPath)->each(function ($node) {
			return trim($node->text());
		});
		
		$domCSSPath = '#propertyHeaderPrice > small'; 
		$aPropData["status"] = $crawler->filter($domCSSPath)->each(function ($node) {
			return trim($node->text());
		});

		$domCSSPath = '#propertyHeaderPrice > strong'; 
		$aPropData["price"] = $crawler->filter($domCSSPath)->each(function ($node) {
			return trim($node->text());
		});
	
		$aDetails = array(); 
		$domCSSPath = 'div.sect.key-features > ul > li';
		$aPropData["details"] = $crawler->filter($domCSSPath)->each(function ($node) {
			return trim($node->text());	
		});

		$aPropData = $this->optimize($aPropData);
		
		//images;
		$domXPath = '//*[contains(@id,"thumbnail")]/img';
		$aPropData["thumbs"] = $crawler->filterXPath($domXPath)->each(function ($node) {
			return trim($node->attr("src"));
		});

		$domXPath = 'noscript > div > ul > li > a > img';
		$aPropData["images"] = $crawler->filter($domXPath)->each(function ($node) {
			return trim($node->attr("src"));
		});

		$domXPath = '#floorplanTabs > noscript > img';
		$aPropData["floorplans"] = $crawler->filter($domXPath)->each(function ($node) {
			return trim($node->attr("src"));
		});


		echo json_encode($aPropData,JSON_UNESCAPED_UNICODE);

	}

	private function optimize($config) {
	    foreach ($config as $key => $value) {
	        if  (is_array($value) && (count($value) == 1) && isset($value[0]) ) {
		    $config[$key] = $value[0];              
		}
	    }
	    return $config;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
