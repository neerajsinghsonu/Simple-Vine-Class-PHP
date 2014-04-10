<?php
define('INSITE', true);
// attched vine class file
require_once 'Class.Vine.php';
// make an object
$vineObject = new Vine ();

// variable handling
if(!isset($_POST['search'])){
	$search = 'hollywood';
	$page = 1;
	$limit = 11;
}

// form handling code
if(isset($_POST['search'])){
	// get serach tag name
	$search = !empty($_POST['search-tag']) ? strip_tags(htmlentities($_POST['search-tag'])) : $search;
	// get page name
	$page = !empty($_POST['page']) ? strip_tags(htmlentities($_POST['page'])) : $page;
	// get limit
	$limit = !empty($_POST['limit']) ? strip_tags(htmlentities($_POST['limit'])) : $limit;
	// return requeted data
	$tagData = $vineObject->searchTag ( $search, $page, $limit );	
	// echo '<pre>',print_r($tagData, true),'</pre>'; die;
}else{
	$tagData = $vineObject->searchTag ( 'Hollywood' );
	// echo '<pre>',print_r($tagData, true),'</pre>'; die;
}

// next pagination link
$nextPageLink = $tagData['next'];
// previous pagination link
$previousPageLink = $tagData['prev'];
// record count limit
$recordLimit = $tagData['size'];

// unset the paging array
unset($tagData['next']);
unset($tagData['prev']);
unset($tagData['size']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Demo for VINE API | Get Vine Search Tag Data</title>
<style>
body{font-size: 12px;}
table tr th, table tr td{border:1px solid #D4D0C8;vertical-align: top; padding:3px; border-collapse: separate;}
table tr td table, table{width:100%;border-collapse: collapse;}
table tr td table tr th,table tr td table tr td{border:1px dotted #A9AEAD;} 
</style>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name='user'>
		<table>
			<tr>
				<th align="left">Search Tag</th>
				<th align="left">Page</th>
				<th align="left">Limit</th>
				<th rowspan="2" style='vertical-align: middle;'><input type="submit" value="Search.." id='search' name='search' /></th>	
			</tr>
			<tr>
				<td><input type="text" id='search-tag' name='search-tag' value='<?php echo $search; ?>' /></td>
				<td><input type="text" id='page' name='page' value='<?php echo $page; ?>' /></td>
				<td><input type="text" id='limit' name='limit' value='<?php echo !isset($_POST['search']) ? ($limit-1) : $limit; ?>' /></td>		
				<!--<td>&nbsp;</td> -->
			</tr>
		</table>
	</form>

	<p>&nbsp;</p>
	
	Go to <a class='paging' id="<?php echo $previousPageLink;?>" href='javascript:void(0);'>Prevoius</a> | <a class='paging' id="<?php echo $nextPageLink;?>" href='javascript:void(0);'>Next</a>
	
	<p>&nbsp;</p>
	
	<table>
	<tr>		
		<th colspan="8"><?php echo "Toal Fetch ". count($tagData). " records for ($search)";?></th>
	</tr>
		<tr>
			<th>SR. No.</th>
			<th>User Liked</th>
			<th>Who Likes</th>
			<th>Thumbnail</th>
			<th>Avatar</th>
			<th>Comments</th>
			<th>Entites</th>
			<th>Media</th>
		</tr>
		
		<?php // start php code ?>
		<?php foreach ($tagData as $j => $v) {
			echo '<tr>';
			
				echo '<td>'.($j+1).'</td>';
				
				echo '<td>';
				echo $tagData[$j]['likes']['count'];			
				echo '</td>';
				
				echo '<td>';
					echo '<table>';
						echo '<tr>';
							echo '<th>User Name</th>';
							echo '<th>User ID</th>';
						echo '</tr>';
						if($tagData[$j]['likes']['count'] != '0'){
							foreach ($tagData[$j]['likes']['data'] as $x) {
								echo '<tr>';
									echo '<td>'.$x['user_name'].'</td>';
									echo '<td>'.$x['user_id'].'</td>';
								echo '</tr>';
							}
						}else{
							echo '<tr><td colspan="2">&nbsp;</td></tr>';
						}
					echo '</table>';
				echo '</td>';
				
				echo '<td align="center"><img src="'.$tagData[$j]['thumbnail_url'].'" width="75"/></td>';
				echo '<td align="center"><img src="'.$tagData[$j]['avatar_url'].'" width="75"/></td>';
			
				echo '<td>';
					echo '<table>';
						echo '<tr>';
							echo '<th>Comment</th>';
							echo '<th>User Name</th>';
							echo '<th>User ID</th>';
							echo '<th>Description</th>';
							echo '<th>Photo</th>';
							echo '<th>Location</th>';
						echo '</tr>';
						if($tagData[$j]['comments']['count'] != '0'){
							foreach ($tagData[$j]['comments']['data'] as $y) {
								echo '<tr>';
									echo '<td>'.$y['comment'].'</td>';
									echo '<td>'.$y['user_name'].'</td>';
									echo '<td>'.$y['user_id'].'</td>';
									echo '<td>'.$y['description'].'</td>';
									echo '<td><img src="'.$y['avatar_url'].'" width="75"/></td>';
									echo '<td>'.$y['location'].'</td>';
								echo '</tr>';
							}
						}else{
							echo '<tr><td colspan="6">&nbsp;</td></tr>';
						}
					echo '</table>';
				echo '</td>';
				
				echo '<td>';
					echo '<table>';
						echo '<tr>';
							echo '<th>Link</th>';
							echo '<th>Type</th>';
							echo '<th>Title</th>';				
						echo '</tr>';
						if(isset($tagData[$j]['entities']) && count($tagData[$j]['entities']['data']) > 0){
							foreach ($tagData[$j]['entities']['data'] as $z) {
								echo '<tr>';
								echo '<td>'.$z['link'].'</td>';
								echo '<td>'.$z['type'].'</td>';
								echo '<td>'.$z['title'].'</td>';						
								echo '</tr>';
							}
						}else{
							echo '<tr><td colspan="3">&nbsp;</td></tr>';
						}
					echo '</table>';
				echo '</td>';
				
				echo '<td>';
					echo '<table>';
						echo '<tr>';
							echo '<th>Video</th>';
							echo '<th>User Name</th>';				
							echo '<th>Description</th>';
							echo '<th>Date</th>';				
						echo '</tr>';
						if(count($tagData[$j]['media']['data']) > 0){
							foreach ($tagData[$j]['media'] as $a) {
								echo '<tr>';
								echo '<td><video height="110" width="200" data-bindattr-2="2"  src="'.$a['high_video_url'].'" loop="" controls="true"></video></td>';
								echo '<td>'.$a['user_name'].'</td>';						
								echo '<td>'.$a['description'].'</td>';						
								echo '<td>'.$a['created_date'].'</td>';
								echo '</tr>';
							}
						}else{
							echo '<tr><td colspan="5">&nbsp;</td></tr>';
						}
					echo '</table>';
				echo '</td>';
				
			echo '</tr>';
		}?>		
		<?php // end php code ?>
	</table>
	<script type="text/javascript">
	<!--
	if (document.body.addEventListener) {
		document.body.addEventListener('click', pagination, false);
	} else {
		document.body.attachEvent('onclick', pagination);
	}
	function pagination(e) {
		e = e || window.event;
		var target = e.target || e.srcElement;
		if (target.className.match(/paging/)) {						
			document.getElementById("page").value=target.id;
			document.getElementById('search').click();					
		}
	}	
	</script>
</body>
</html>
