<h2>Bookwize Integrated Cinnamon Settings</h2>

<form method="POST" action="options.php">
	<?php
	settings_fields( 'bookwize-integrated-cinnamon' );
	do_settings_sections( 'bookwize-integrated-cinnamon' );
	submit_button();
	?>
</form>
