/* Ce fichier comporte les script js utilisés sur la page de gestion des droits */ 
$(function(){

	$("#profileListe").change(function(){
		loadListeDroits( $("#profileListe").val() );
	});
});


function loadListeDroits(id){
	var html = "";
	html += "<div style='text-align:center;margin-top:50px;'><img src='../../img/wait-big.gif'  /></div>";
	$("#listeDroits").html(html);
    $.ajax({
        type: "POST",
        url: "droit/liste",
        data: {idProfil:id},
        cache: false,
        success: function(data){
           $('#listeDroits').html(data);
        }
    });	
}

function doDroitsProcess( data ){
    $.ajax({
        type: "POST",
        url: "droit/updateDroits",
        data: {formdata:data},
        cache: false,
        success: function(data){
			var resJson = jQuery.parseJSON(data);
			if( resJson.result == "success")
				//loadListeDroits(resJson.idprofil);
                                    window.location = "/droit/liste";
			else
				alert("Erreur durant le traitement. Veuillez réessayer svp.");
        }
    });	
}	
	
	