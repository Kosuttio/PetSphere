<?php
/**
 * The main template file
 */
get_header();
?>

<main class="container" style="padding: 40px 20px;">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <hr>
            <?php
        endwhile;
    else :
        echo '<p>Žádný obsah nenalezen.</p>';
    endif;
    ?>
</main>

<?php
get_footer();
