<?php

//$linkPrefix = get_bloginfo('url');
$searchTab = '';
$featuredTab = '';
$popularTab = '';
$newestTab = '';
$updatedTab = '';
if(!empty($_GET['tab'])){
    $tab = $_GET['tab'].'Tab';
    $$tab = 'class="current"';
}else{
    $_GET['tab'] == 'search';
    $searchTab = 'class="current"';
}


?>
<div class="wrap">
	<div class="icon32" id="icon-themes"><br></div>
<h2>Application Marketplace</h2>

	<ul class="subsubsub">
		<li><a <?php echo $searchTab; ?> href="admin.php?page=appmarket&tab=search">Search</a> | </li>
		<li><a <?php echo $featuredTab; ?> href="admin.php?page=appmarket&tab=featured">Featured</a> | </li>
		<li><a <?php echo $popularTab; ?> href="admin.php?page=appmarket&tab=popular">Popular</a> | </li>
		<li><a <?php echo $newestTab; ?> href="admin.php?page=appmarket&tab=newest">Newest</a> | </li>
		<li><a <?php echo $updatedTab; ?> href="admin.php?page=appmarket&tab=updated">Recently Updated</a></li>
	</ul>
	<br class="clear">
		<p>Applications are predefined sets of interfaces created by other DB-Toolkit users and published on the DB-Toolkit Marketplace.</p>



        <?php
        if($_GET['tab'] == 'search'){
            
        //vardump($_POST);

        ?>
	<h4>Search</h4>
	<p class="install-help">Search for apps by keyword, author, or tag.</p>
	<form action="admin.php?page=appmarket&tab=search" method="post" id="search-plugins">
		<select id="typeselector" name="type">
			<option value="term">Term</option>
			<option value="author">Author</option>
			<option value="tag">Tag</option>
		</select>
		<input type="text" value="" name="s">
		<label for="plugin-search-input" class="screen-reader-text">Search Apps</label>
		<input type="submit" class="button" value="Search Apps" name="search" id="plugin-search-input">
	</form>
	<?php
        }
        ?>


        <br class="clear"></div>