		<h3>Objects related to <b><?php echo $query; ?></b></h3>
		<div id="rows">
			<div class="row" style="width:100%;">
			<?php
				foreach($links as $link){
					echo "<a href=\"#\" onclick=\"appendRow('{$link}');\"><div class=\"nollejObject col-md-1\">".$link."</div></a>";
				}
				
			?>
			</div>
		</div>
		
