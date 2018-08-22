<?php
	if($displayBoules == "checked")
		{require('views/aff-boules.phtml'); }
	else if($displayBoulesHome == "checked" and $page=="home")
		{require('views/aff-boules.phtml'); }
?>