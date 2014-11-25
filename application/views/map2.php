		<h3>Objects related to <b><?php echo $query; ?></b></h3>
		<div class="row">
		<?php
			$keys = array_keys($search['query']['pages']);
			for($i=0;$i<=12;$i++){
				echo "<div class=\"col-md-1\">".$search['query']['pages'][$keys[rand(0,count($search['query']['pages']))]]['title']."</div>";
			}
			
		?>
		</div>
