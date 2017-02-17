<?php
/*
Description: Batch Delete Comments
Author: Pascal Olive
Author URI: http://goutu.org
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =               BATCH DELETE COMMENTS
/* =================================================================*/
function ajax_batch_delete_comments() {
	
	echo '<p>In Batch Delete Comments function...</p>';
		
	if ( isset($_POST['args']['post-type']) ) {
		$post_type = $_POST['args']['post-type'];
		echo sprintf("<b>Post type</b> = %s",$post_type);
		echo "<br>";
	}
		
	if ( isset($_POST['args']['include']) ) {
		$include = $_POST['args']['include'];
		echo sprintf("<b>Limit to Posts</b> = %s",$include);
		echo "<br>";
	}
	

//	$response = array('msg'=>'Dans Batch Delete Comments script',
//										'post-type'=>$post_type,
//										'include'=>$include,
//										);
//	echo json_encode( $response );

	$deleted_count='0';

	if ( ! empty($include) ) {
		$include = ($include=='all')?'':$include;
	 	$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
	  foreach ($posts as $post) {
			$comments = get_comments( array('post_id'=>$post->ID ) );
			echo sprintf(__('Post %s contains %d comments'), $post->post_title, count($comments) );
			echo "<br>";
			if ( ! empty( $comments ) ) {
				foreach ($comments as $comment) {
					$deleted = wp_delete_comment( $comment->comment_ID );
					if ( $deleted ) {
						++$deleted_count;
					}
				}
			}

		}
		
	}
	
	else {
		echo "Please provide post IDs or 'all' for deletion to take place";
	} 
	
	echo sprintf('Delete comments operation completed, %s comments deleted',$deleted_count);
	echo "<br>";

}

?>


