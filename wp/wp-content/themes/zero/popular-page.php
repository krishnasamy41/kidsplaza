<?php /* Template Name: Top View */ get_header(); ?><?php if (have_posts()) : while (have_posts()) : the_post(); bbit_views(get_the_ID()); ?>    <?php bbit_breadcrumbs(); ?>    <?php    $popularpost = new WP_Query( array( 'posts_per_page' => 10, 'meta_key' => 'post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );    while ( $popularpost->have_posts() ) : $popularpost->the_post();    get_template_part( 'includes/templates/loop', 'index' );    endwhile;	?>    <?php bbit_pagination($pages = '', $range = 2); ?><?php endwhile; endif; ?><?php get_footer(); ?>