<?php
//get the CRM user from host (id = 999999) contacts to form signature
$host = DB::table('client')->where('id','999999')->first();
$username = strtolower(Session::get('user'));
$user = DB::table('contact')->where('contact_type','client')->where('type_id','999999')->whereRaw('LOWER(firstname) = ?', array($username))->first();
?>


@yield('content')
<br></br>

Kind Regards <br></br><br>

@if(is_object($user))
	{{ucwords($user->firstname." ".$user->lastname)}} <br></br>
	{{ucwords($user->position)}} <br></br><br>
	
	@if(substr($user->phone1,0,3) == "+44")
		EN: <a href = "tel:{{$user->phone1}}">{{$user->phone1}}</a> </br><br>
	@elseif(substr($user->phone1,0,3) == "+7 ")
		RU: <a href = "tel:{{$user->phone1}}">{{$user->phone1}}</a> </br><br>
	@elseif(substr($user->phone1,0,3) == "+38")
		UKR: <a href = "tel:{{$user->phone1}}">{{$user->phone1}}</a> </br><br>
	@endif
	
	@if(substr($user->phone2,0,3) == "+44")
		EN: <a href = "tel:{{$user->phone2}}">{{$user->phone2}}</a></br><br>
	@elseif(substr($user->phone2,0,3) == "+7 ")
		RU: <a href = "tel:{{$user->phone2}}">{{$user->phone2}}</a> </br><br>
	@elseif(substr($user->phone2,0,3) == "+38")
		UKR: <a href = "tel:{{$user->phone2}}">{{$user->phone2}}</a> </br><br>
	@endif
	
	@if($user->email1 != "")
		E: <a href = "mailto:{{$user->email1}}">{{$user->email1}}</a> </br><br>
	@endif
	
	@if($user->email2 != "")
		E: <a href = "mailto:{{$user->email2}}">{{$user->email2}}</a> </br><br>
	@endif
	
	@if($user->skype != "")
		SKYPE: <a href = "skype:{{$user->skype}}?call">{{$user->skype}}</a></br><br>
	@endif
@endif

@if(is_object($host))
	@if($host->website != "")
		W: <a href = "{{$host->website}}">{{$host->website}}</a></br><br>
	@endif
@endif
	
	<br></br>
	<img src = "{{$message->embed(public_path().'/images/esig.png')}}" />
	<br></br><br>
	
	{{$host->client_name}}</br><br>
	{{$host->address1}}</br><br>
	{{$host->address2}}</br><br>
	{{$host->address3}}</br><br>
	{{$host->address4}}</br><br>
	{{$host->postcode}}</br><br></br><br>
	
	This mail (electronic mail and its attachments) is private and confidential to the sender and the authorised individual or entity recipient. 
	If you are not the intended recipient of this mail or the person who is responsible for the transmission of it to the intended recipient, you 
	are hereby notified that storing, copying, using or forwarding of any part of the contents is strictly prohibited, please completely delete it 
	from your system and notify the sender. Any disclosure or sharing of this information may lead to legal responsibility. The “Resource” Group of 
	Companies shall have no liability with regard to the accuracy and integrity of this mail and its transmission.
	
	

