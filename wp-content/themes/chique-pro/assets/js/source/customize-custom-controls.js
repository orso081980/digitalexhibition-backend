jQuery( function($) {
	 $( ".custom-sortable" ).sortable({
	 	cursor: "move",
		stop : function(event, ui){
			$(this).next().val( $(this).sortable( "toArray" ) ).trigger( 'change' );
		}
	}).disableSelection();
});
