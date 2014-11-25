					<div class="mastfoot">
						<div class="inner">
							<p style="float: left;">Copyright &copy; 2014.  Sean Cheatham.</p>
							
							<ul class="nav nav-pills" style="float:right;">
								<li><a href="<?php echo site_url(); ?>">Home</a></li>
								<li><a href="<?php echo site_url('pages/about'); ?>">About</a></li>
								<li><a href="http://seancheatham.com">Contact</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="<?php echo base_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/arbor.js'); ?>"></script>
        <script src="<?php echo base_url('js/nollej.js'); ?>"></script>
        <?php if(isset($query)) { ?>
              <script type="text/javascript">
                  getLinks("<?php echo $query; ?>",1);
              </script>
        <?php } ?>
	</body>
</html>