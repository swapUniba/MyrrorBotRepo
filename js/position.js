      var marker;
      var geocoder;
      var watch = null;
      var options = {enableHighAccuracy:true, timeout: 35000}; 
      var comune = "Bari";

     function getCity(){
      return comune;
    }

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
  
            //divido l'indirizzo 
            var add = results[0].formatted_address.split(",");
            /*
            se l'indirizzo contiene anche un numero civico l'array 
            restituito da google sarà  di 4 elementi quindi i primi
            2 conterranno indirizzo e numero civico e il terzo il comune
            */

            if (add.length == 4){

                indirizzo = add[0] + add[1];
                //prendo il CAP del comune
                com = add[2].split(" ");
                cap = com[1];
             } else {
                /*
                in caso di numero civico mancante l'array che ottengo 
                contiene solo 3 campi
                */
                indirizzo = add[0];
                com = add[1].split(" ");
                
                cap = com[1];
                
              }

               comune = "";
                for (var i = 2; i < com.length -1; i++) {
                  comune += com[i]+" ";
                }
                
                if(comune == "")
                     comune = "Bari"
                             
              console.log(comune);
          }

    }); 
    
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