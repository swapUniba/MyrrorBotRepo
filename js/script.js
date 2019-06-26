$(".messages").animate({ scrollTop: $(document).height() }, "fast");

  $("#profile-img").click(function() {
  	$("#status-options").toggleClass("active");
  });

  $(".expand-button").click(function() {
    $("#profile").toggleClass("expanded");
  	$("#contacts").toggleClass("expanded");
  });

  $("#status-options ul li").click(function() {
  	$("#profile-img").removeClass();
  	$("#status-online").removeClass("active");
  	$("#status-away").removeClass("active");
  	$("#status-busy").removeClass("active");
  	$("#status-offline").removeClass("active");
  	$(this).addClass("active");
  	
  	if($("#status-online").hasClass("active")) {
  		$("#profile-img").addClass("online");
  	} else if ($("#status-away").hasClass("active")) {
  		$("#profile-img").addClass("away");
  	} else if ($("#status-busy").hasClass("active")) {
  		$("#profile-img").addClass("busy");
  	} else if ($("#status-offline").hasClass("active")) {
  		$("#profile-img").addClass("offline");
  	} else {
  		$("#profile-img").removeClass();
  	};
  	
  	$("#status-options").removeClass("active");
  });

  function newMessage() {
  	message = $(".message-input input").val();
  	if($.trim(message) == '') {
  		return false;
  	}

  	$('<li class="sent"><img src="immagini/user.png" alt="" /><p>' + message + '</p></li>').appendTo($('.messages ul'));
  	$('.message-input input').val(null);
  	$('.contact.active .preview').html('<span>Tu: </span>' + message);

    //Scroll verso il basso quando viene inviata una domanda
  	$(".messages").animate({ scrollTop: $(document).height() }, "fast");

    return message;
  };

  //Quando viene cliccato il tasto sullo schermo per inviare
  $('.submit').click(function() {
    let query = newMessage();
    send(query);
  });

  //Quando viene premuto 'invio' sulla tastiera
  $(window).on('keydown', function(e) {
    if (e.which == 13) {
      let query = newMessage();
      send(query);

      return false;
    }
  });
 
 function send(query) {
      var text = query;
      $.ajax({
        type: "POST",
        url: "php/intentDetection.php",
        data: {testo:text},
        success: function(data) {
          setResponse(data);
        }
      });
  }

function setResponse(val) {
      console.log(val);
      val = JSON.parse(val);

      var canzoneNomeSpotify = "Ecco qui la canzone richiesta!";
      var canzoneArtistaSpotify = "Ecco qui la canzone dell'artista richiesto!";
      var canzoneGenereSpotify = "Ecco una playlist di canzoni del genere richiesto!";
      var playlistEmozioniSpotify = "Ecco qui una playlist di canzoni raccomandata in base al tuo umore";
      var canzoneEmozioniSpotify = "Ecco qui un brano consigliato in base al tuo umore";
      var canzoniPersonalizzateSpotify = "Ecco qui un brano consigliato che potrebbe piacerti";
      var video = "Ecco qui il video richiesto";

      if (val["intentName"] == "Interessi" || val["intentName"] == "Contatti" || val["intentName"] == "Esercizio fisico" || val["intentName"] == "Personalita") {
       
         $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + val["answer"] + '</p></li>');

      }else if(val['intentName'] == "Canzone per nome"){
        
        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + canzoneNomeSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');
      
      }else if(val['intentName'] == "Canzone per artista"){

        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + canzoneArtistaSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');

      }else if(val['intentName'] == "Canzoni in base al genere"){

        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + canzoneGenereSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');

      }else if(val['intentName'] == "Playlist di canzoni in base alle emozioni"){
      
        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + playlistEmozioniSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');

      }else if(val['intentName'] == "Canzoni in base alle emozioni"){

        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + canzoneEmozioniSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');

      }else if(val['intentName'] == "Canzoni personalizzate"){
        
        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + canzoniPersonalizzateSpotify + '&#x1F603;' +'<br>'+ '<iframe src="' + val['answer'] + '" width="250" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></p></li>');

      }else if(val["intentName"] == "Notizie in base ad un argomento" || val["intentName"] == "Notizie in base agli interessi" || val["intentName"] == "Notizie odierne"){

        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p><a href="'+val["answer"]+'">'+val["answer"]+'</a></p></li>');

      
      }else if(val["intentName"] == "Video in base alle emozioni" || val["intentName"] == "Ricerca Video"){

          $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + video + ' &#x1F603; <br>' +'<iframe id="ytplayer" type="text/html" width="260" height="260" src="' + val['answer'] + '" frameborder="0" allowfullscreen/></iframe></p></li>');

      }else {

        $(".chat").append('<li class="replies"><img src="immagini/chatbot.png" alt="" /><p>' + val["answer"] + '</p></li>');
      
      }

      //Scroll verso il basso quando viene ricevuta una risposta
      $(".messages").animate({ scrollTop: $(document).height() }, "fast");

    }

    //Intent avviato all'inizio del dialogo per mostrare la frase di benvenuto e per impostare il nome dell'utente nella schermata
    function welcomeIntent(){
      send("aiuto");
      setNominativo(); //Nome per la grafica del sito
      
    }

    //Funzione usata per impostare il nome dell'utente nella schermata
    function setNominativo() {
      $.ajax({
        type: "POST",
        url: "php/setNominativo.php",
        success: function(data) {
          console.log(data);
          $(".nomeUtente").append(data);
        }
      });
    }



