//field_5db0a3dd625c7

jQuery(document).ready( function($){
	
	$(".acf-field-5db0a3dd625c7").on( "change", 'select', function(e){		
		var _row = $(this).parent().parent().parent();
		var _cal = $(this).val();
		var _input = _row.children('.acf-field-5db0a5d5201ad').find('input');
			
			console.log( _input );
			
			$.ajax({
				type : "post",
				dataType : "json",
				url : Documentation.ajaxurl,
				data : {
					action: "document_categorie_name",
					document : _cal,
					mynonce : Documentation.docuNonce
				},
				beforeSend: function(){
				
				},
				}).done( function(response) {
					console.log(response.nom);
					_input.val( response.nom );
					
					//var $nombre_posts = response.post_count;
					//$("#documentation-search-results").html(response.les_posts);
					
				
				}).fail(function(jqXHR, textStatus, errorThrown){
				 
				}).always(function(response) {
				});
	});
	
});
 