<?php
	require('Headers.php');
	require('Classes.php');
?>

<html>
	<head>
		<title> Tabtabus </title>
		<link rel="stylesheet" type="text/css" href="mysite.css">
	</head>
	<body>
    <p>ololo</p>
		<?php
			$TaskManager = new TaskManager();

			if ($TaskManager->oCurrentTask)
				$TaskManager->buildResult();
			else
				$TaskManager->buildMenu();
		?>
	</body>
</html>