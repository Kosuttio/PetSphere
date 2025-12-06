<?php get_header(); ?>

<main>
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
    endif;
    ?>

    <!-- Tech & AI Fluff Section -->
    <section class="tech-specs">
        <div class="container">
            <h2>Technologické zázemí PetSphere</h2>
            <p>Nejsme jen obyčejný e-shop. Jsme technologická platforma.</p>
            
            <div class="tech-grid">
                <div class="tech-item">
                    <h3>Cloud & Infrastruktura</h3>
                    <p><?php echo get_theme_mod( 'petsphere_infra_text', 'Platforma PetSphere je provozována v cloudové infrastruktuře Microsoft Azure...' ); ?></p>
                </div>
                
                <div class="tech-item">
                    <h3>AI & Data</h3>
                    <p><?php echo get_theme_mod( 'petsphere_ai_text', 'Využíváme AI moduly pro personalizovaná doporučení...' ); ?></p>
                </div>

                <div class="tech-item">
                    <h3>Náš Tým</h3>
                    <p><?php echo get_theme_mod( 'petsphere_team_text', 'Společnost má více než třicet zaměstnanců...' ); ?></p>
                </div>

                <div class="tech-item">
                    <h3>Interní Systémy</h3>
                    <p>Pro řízení využíváme NetSuite, Microsoft 365, Jira a Notion pro maximální efektivitu.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
