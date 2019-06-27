      var marker;
      var geocoder;
      var watch = null;
      var options = {enableHighAccuracy:true, timeout: 35000}; 

      document.addEventListener("deviceready", onDeviceReady, false);

      $( document ).ready(function() {
          onDeviceReady();
      });

         function onDeviceReady() {    
          
        geocoder = new google.maps.Geocoder();
         //localizzazione GPS
         watch = navigator.geolocation.watchPosition(onSuccess,onError,options);
       
    }
function onSuccess(position){
      //geolocalizzazione avvenuta con successo        

      navigator.geolocation.clearWatch(watch);
      var lat = position.coords.latitude;
      var long = position.coords.longitude;
     
      //creo un oggetto posizione
      var latlng = new google.maps.LatLng(lat, long);
     

      //ricavo l'indirizzo a partire dalle coordinate
    geocoder.geocode({'location': latlng}, function(results, status) {

        if (status === 'OK') {        

           console.log(results);

          }

    });
     /*
       $.ajax({
        type: 'GET',
        dataType: "json",
        url: "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"&sensor=false&key=",
        data: {},
        success: function(data) {
            console.log(data);
            $.each( data['results'],function(i, val) {
                $.each( val['address_components'],function(i, val) {
                    if (val['types'] == "locality,political") {
                        if (val['long_name']!="") {
                            console.log(val['long_name']);
                        }
                        else {
                            console.log("unknown");
                        }
                        console.log(i+", " + val['long_name']);
                        console.log(i+", " + val['types']);
                    }
                });
            });
           
        },
        error: function () { console.log('error'); } 
    }); 
         */
    
    }      

        function onError(error){
  /*
   se si verifica un errore durante la geolocalizzazione lo mostro 
  */
  alert(error);


    }  


      function onrefresh(position){

      navigator.geolocation.clearWatch(watch);
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
    
      //creo un oggetto posizione
      var latlng = new google.maps.LatLng(latitude, longitude);

   
   
  }