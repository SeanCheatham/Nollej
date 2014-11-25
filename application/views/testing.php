<?php $this->load->helper('url'); ?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		
		<link rel="<?php echo base_url(); ?>css/joint.min.css" />
		
	</head>
	<body>
		<div id="paper"></div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>js/joint.js"></script>
		<script src="<?php echo base_url(); ?>js/joint.shapes.fsa.js"></script>
		<script type="text/javascript">
var graph = new joint.dia.Graph;

var paper = new joint.dia.Paper({
    el: $('#paper'),
    width: '100%',
    height: '100%',
    model: graph,
    gridSize: 1
});


var r1 = joint.shapes.basic.Rect(100, 100, 100, 100);
var r2 = joint.shapes.basic.rect(100, 100, 100, 100);
r2.translate(100);

var link = new joint.dia.Link({
    source: { id: r1.id },
    target: { id: r2.id }
});

graph.addCells([rect, rect2, link]);
		</script>
	</body>
</html>
