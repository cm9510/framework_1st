<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Error.</title>
	<style type="text/css">
	*{margin: 0;padding: 0;}
	.err-head,.err-body{margin:10px auto;width:80%;padding:5px;}
	.err-head{border:2px pink solid;}
	.err-body{border:1px gray solid;}
	.title-overview{color: red;font-size:32px;}
	.content-desc{font-size:20px;}
	.content-desc span{color:red;}
	</style>
</head>
<body>
	<div class="err-head">
		<p class="title-overview">Error ：<?php echo e($title);?></p>
	</div>
	<div class="err-body">
		<p class="content-desc"><?php echo $content;?></p>
	</div>
</body>
</html>