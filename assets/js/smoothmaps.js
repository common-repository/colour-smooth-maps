 function smoothMaps_openMarker(evt, markerNr) {
  // Declare all variables
 
  var i, smoothMapstabcontent, smoothMapstablinks;

  // Get all elements with class="smoothMapstabcontent" and hide them
  smoothMapstabcontent = document.getElementsByClassName("smoothMapstabcontent");
  for (i = 0; i < smoothMapstabcontent.length; i++) {
    smoothMapstabcontent[i].style.display = "none";
  }

  // Get all elements with class="smoothMapstablinks" and remove the class "active"
  smoothMapstablinks = document.getElementsByClassName("smoothMapstablinks");
  for (i = 0; i < smoothMapstablinks.length; i++) {
    smoothMapstablinks[i].className = smoothMapstablinks[i].className.replace(" active", "");
  }

  // Show the current smoothMapstab, and add an "active" class to the button that opened the smoothMapstab
  document.getElementById(markerNr).style.display = "block";
 
  evt.currentTarget.className += " active";
 // document.getElementById("smoothMapstabMarker-"+markerNr).className += " active";
}


 
 function smoothMaps_removeElement(elementId) {
    // Removes an element from the document
    var element = document.getElementById("marker-"+elementId);
    var element2 = document.getElementById("smoothMapstabMarker-"+elementId);
    element2.parentNode.removeChild(element2);
    element.parentNode.removeChild(element);
	
	smoothMaps_openMarker(event, 'marker-1');
	 document.getElementById("smoothMapstabMarker-1").className += " active"; 
	document.getElementById("post").submit();
	
}

function smoothMaps_createMarker(){
	var markersNr = document.querySelectorAll('.markersmoothMapstab').length;
	document.getElementById('markerNav').innerHTML= document.getElementById('markerNav').innerHTML+'<a class="smoothMapstablinks" onclick="smoothMaps_openMarker(event, \'marker-'+(markersNr+1)+'\')" id="smoothMapstabMarker-'+(markersNr+1)+'" >Marker '+(markersNr+1)+'</a>';
	document.getElementById('markersmoothMapstabsContent').innerHTML= document.getElementById('markersmoothMapstabsContent').innerHTML+'<div id="marker-'+(markersNr+1)+'" class="smoothMapstabcontent markersmoothMapstab"> <label><b>Address:</b><br/>	<input type="text" name="smoothMaps-address['+(markersNr)+']"  style="width:100%" /></label><br/></div>';
	smoothMaps_openMarker(event, 'marker-'+(markersNr+1));
	  smoothMapstablinks = document.getElementsByClassName("smoothMapstablinks");
	  for (i = 0; i < smoothMapstablinks.length; i++) {
		smoothMapstablinks[i].className = smoothMapstablinks[i].className.replace(" active", "");
	  }
	   document.getElementById("smoothMapstabMarker-"+(markersNr+1)).className += " active"; 
	
}


jQuery(document).ready(function( $ ) {
	
	if($('#smoothMaps-type').val() == 'smoothMaps-iframe' ){
		  $('#smoothMaps-Iframe-address-row').show();
		  $('#smoothMaps-style-row').hide();
		  $('#smoothMaps-hidebusinesses-row').hide();
		  $('.smoothMapstab').hide();
		  $('#markersmoothMapstabsContent').hide();
	  }
	  else {
		  $('#smoothMaps-Iframe-address-row').hide();
		  $('#smoothMaps-style-row').show();
		  $('#smoothMaps-hidebusinesses-row').show();
		  $('.smoothMapstab').show();
		  $('#markersmoothMapstabsContent').show();
	  }
	
	$('#smoothMaps-type').on('change', function() {
		
	  if( this.value == 'smoothMaps-iframe' ){
		  $('#smoothMaps-Iframe-address-row').show();
		  $('#smoothMaps-style-row').hide();
		  $('#smoothMaps-hidebusinesses-row').hide();
		  $('.smoothMapstab').hide();
		  $('#markersmoothMapstabsContent').hide();
	  }
	  else {
		  $('#smoothMaps-Iframe-address-row').hide();
		  $('#smoothMaps-style-row').show();
		  $('#smoothMaps-hidebusinesses-row').show();
		  $('.smoothMapstab').show();
		  $('#markersmoothMapstabsContent').show();
	  }
	});
});