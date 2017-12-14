<?php
/*
YARPP Template: Goutu
Description: Customizable column count
Author: Pascal O.
*/ ?>

<?php
define ( 'COLUMNS', '5');

$grid = 'grid-' . COLUMNS . 'col';?>


<?php if (have_posts()):?>

<h2>
<?php
	if ( is_singular( 'recipe' ) ) 
		echo __('Related Recipes','foodiepro');
	else
		echo __('Related Posts','foodiepro');
?>
</h2>

<div class="rpwe-block <?php echo $grid; ?>">
	<ul class="rpwe-ul">
	<?php while (have_posts()) : the_post(); ?>
		<?php if (has_post_thumbnail()):?>
				<li class="rpwe-li rpwe-clearfix">
					<a class="rpwe-img" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'square-thumbnail', array( 'class' => 'rpwe-aligncenter rpwe-thumb' ) ); ?>
					</a>
					<h3 class="rpwe-title">
						<a href="<?php the_permalink() ?>" rel="bookmark">
							<?php the_title_attribute(); ?>
						</a>	
					</h3>
				</li>
		<?php endif; ?>
	<?php endwhile; ?>
	</ul>
</div>

<?php else: 
//echo '<p>' . __('No related posts.', 'foodiepro') . '</p>';
endif; ?>
