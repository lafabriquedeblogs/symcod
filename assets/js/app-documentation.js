( function( $ , d , w ) {
	
	'use-strict';
	
	function search_product(){
			
			var $term = $("#nom-produit").val();
			
			$("#version-produit").removeClass("actif");
			
			$.ajax({
				type : "post",
				dataType : "json",
				url : Documentation.ajaxurl,
				data : {
					action: "docu_search_nom_produits",
					term : $term,
					mynonce : Documentation.docuNonce
				},
				beforeSend: function(){
				
				},
				}).done( function(response) {
					if( response.no_post !== undefined ){
						$("#documentation-search-results").html("<p>" + response.no_post + "</p>");
					}
					
					var $nombre_posts = response.post_count;
					$("#documentation-search-results").html(response.les_posts);
					
				
				}).fail(function(jqXHR, textStatus, errorThrown){
				 
				}).always(function(response) {
				}); 		
		
	}
	
	function search_categorie_document(){
		
		var product_version = $("#version-produit").val();
		var input_product_id = $("#input_product_id").val();
		var input_cat = $("#form-categorie-document select option:selected").text();
		
		
		$("#selected-final-result").html('');
		
		$.ajax({
			type : "post",
			dataType : "json",
			url : Documentation.ajaxurl,
			data : {
				action: "docu_search_categorie_document",
				version : product_version,
				product_id : input_product_id,
				input_cat : input_cat,
				mynonce : Documentation.docuNonce
			},
			beforeSend: function(){
			
			},
			}).done( function(response) {
				$("#documentation-search-results").html( response.resultats);
	
			}).fail(function(jqXHR, textStatus, errorThrown){
			 	
			}).always(function(response) {
				console.log( response );
			});
			 		
	}
	
	$(d).ready( function(){
		
		$("#form-nom-produit").on("submit", function(e){
			//console.log(e.which);
			e.preventDefault();
			search_product();
		});
		
		
		/* Vérifie la valeur du champ produit afin d'afficher le label */
		$("#nom-produit").on("blur",function(e){
			//e.preventDefault();
			if( $(this).val() != ""){
				return false;
    		}
    		$(".input-label").removeClass("fill");
    		//$("#documentation-search-results").html("");	
		});
		
		/* Gestion de l'affichage du label du champ "Saisir le nom du produit"*/		
		$(".input-label").on("click",function(){
			var nomVal = $("#nom-produit").val();
			$(this).addClass("fill");

		});
		
		$("#form-categorie-document").on("change", "select",function(e){
			e.preventDefault();
			
			var page_id = $("#produit-title").html();
			var page_title = page_id; 
			
			var url = $(this).val(); // get selected value
			if (url) { // require a URL
			    //window.location = url; // redirect
			   var cat_title = $("option:selected", this).text();
			   $("#categorie-title").html( cat_title );
			   history.pushState({ id: page_id }, page_title, url );
			   
			   $(".categorie-document-list-result-item").each( function(){
				   //if( $(this).attr("data-categorie") != cat_title && cat_title != "Tous les documents" ){
				if( $(this).attr("data-categorie") != cat_title && cat_title != Documentation.tous_les_documents ){	
					   $(this).hide();
				   }else {
					   $(this).show();
				   }
			   });
			   
			   //search_categorie_document();
			   
			   
			}
			return false
		});
		
		$("#gform_4").on("change","select", function(e){
			e.preventDefault();
		});
/*
		$("#form-nom-produit").on("change","#select-product", function(e){
			e.preventDefault();
			var url = $(this).val(); // get selected value
			console.log(url);
			if (url) { // require a URL
			   window.location = url; // redirect
			}
		});
*/
		
		
	});
	
})( jQuery , document , window )