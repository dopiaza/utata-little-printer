<!DOCTYPE html>
<html lang="en">
<head>
	<title>Utata</title>
	<meta charset="utf-8">
</head>
<body>
<style type="text/css">
	body
	{
		margin: 0px;
		padding: 10px 0;
		font-family: 'Cabin';
		font-size:20px;
		width: 384px;
	}

	#content
	{
		width: 384px;
	}

	header
	{
		position: relative;	
		text-align: center;
	}

	#logo
	{
		margin: 5px auto 0;	
		padding: 0;
		width: 150px;	
		height: auto;
	}

	#article
	{
		position: relative;
	}

	h1
	{
		font-size: 26px;
		font-weight: 800;	
		margin: 0;	
		padding: 0;
	}

	h2
	{
		font-size: 22px;
		font-weight: 800;		
		margin: 0;	
		padding: 0;
	}

	#text
	{
		margin: 0 4px;
	}

	footer
	{
		font-size: 14px;
		text-align: center;
		width: 384px;
		padding: 0.5em 0;
		border-top: 1px dotted black;
	}

	div#photo
	{
		position: relative;
	}

	div#photo img
	{
		width: 376px;
		height: auto;
		margin: 10px 4px;		
	}

	.separator
	{
		position: relative;
		margin-left: 4px;
		width: 376px;
		height: 5px;
		border-bottom: 3px black solid;
	}

	.final
	{
		margin-bottom: 10px;
	}

</style>
</body>
</html>
<article>
	<div class="separator"></div>
	<header>
		<!-- Alway use absolute URLs for images wherever possible. BERG will love you. -->
		<img id="logo" src="http://www.utata.org/berg/utata_logo.png">
	</header>
	<div id="photo">
		<img class="dither" src="<?php echo getLocalPhoto($publication['photoUrl']) ?>">
	</div>
	<div id="text">
		<h1><?php echo $publication['title'] ?></h1>
		<h2><?php echo $publication['photographer'] ?></h2>
		<?php echo $publication['content'] ?>
		<p class="byline">-- <?php echo $publication['author'] ?></p>
	</div>
	<footer>
		http://utata.org/
	</footer>
	<div class="separator final"></div>
</article>
</body>
</html>