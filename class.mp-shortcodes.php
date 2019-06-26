<?php

add_shortcode( 'memberpress_frontend_data', array( 'MP_Shortcodes', 'memberpress_frontend_data' ) );

class MP_Shortcodes {

	/*
	Frontend shortcode function for displaying the api data in a table.
	*/
	public static function memberpress_frontend_data() {
	ob_start();
	?>
	<h1 class="table-title"></h1>
	<table class="api-table">
		<thead class="api-table-header">
		</thead>
		<tbody class="api-table-body">
		</tbody>
	</table>
	<script type="text/javascript">
	var xhr = new XMLHttpRequest();
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		xhr.open('POST', ajaxurl, true );
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
		xhr.onload = function() {
			if ( xhr.status >= 200 && xhr.status < 300 ) {
				var responseJSON = JSON.parse( xhr.responseText );
				var responseTitle = ( responseJSON.hasOwnProperty('title') ) ? responseJSON.title : '';
				var responseHeaders = ( responseJSON.hasOwnProperty('data') && responseJSON.data.hasOwnProperty('headers') ) ? responseJSON.data.headers : [];
				var responseRows = ( responseJSON.hasOwnProperty('data') && responseJSON.data.hasOwnProperty('rows') ) ? responseJSON.data.rows : [];

				document.querySelector('.table-title').innerHTML = responseTitle;

				for ( var i = 0; i < responseHeaders.length; i++ ) {
					var thHeader = document.createElement('th');
					thHeader.innerHTML = responseHeaders[i];
					document.querySelector('.api-table-header').append(
					thHeader);
				}
				for ( const row in responseRows ) {
					var fname = responseRows[row].fname;
					var lname = responseRows[row].lname;
					var id = responseRows[row].id;
					var email = responseRows[row].email;
					var date = responseRows[row].date;
					var dateObj = new Date(date*1000);
					var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
					var month = months[dateObj.getMonth() ];
					var formattedDate = month + " " + dateObj.getDate() + ", " + dateObj.getFullYear();
					var newTR = document.createElement('tr');
					var idTD = document.createElement('td');
					var fnameTD = document.createElement('td');
					var lnameTD = document.createElement('td');
					var emailTD = document.createElement('td');
					var dateTD = document.createElement('td');
					idTD.innerHTML = id;
					fnameTD.innerHTML = fname;
					lnameTD.innerHTML = lname;
					emailTD.innerHTML = email;
					dateTD.innerHTML = formattedDate;
					newTR.append(idTD);
					newTR.append(fnameTD);
					newTR.append(lnameTD);
					newTR.append(emailTD);
					newTR.append(dateTD);
					document.querySelector('.api-table-body').append(newTR);

				}
			}
		}

		xhr.send(
			"action=memberpress_endpoint" 
		);
	</script>
	<?php
	return ob_get_clean();
	}
}