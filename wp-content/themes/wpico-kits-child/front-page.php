<?php
/**
 * Template Name: Home Page
 * Homepage template — displays the Kit Finder custom element and featured kit previews
 */
get_header();
?>

<main class="container">

  <!-- ===== HERO ===== -->
  <section class="kits-hero">
    <h1>Family Kits for Meaningful Time Together</h1>
    <p>
      Discover hands-on activity kits that bring your family closer,
      quiet the noise of the day, and turn ordinary evenings into lasting memories.
    </p>
  </section>

  <!-- ===== KIT FINDER CUSTOM ELEMENT ===== -->
  <?php
  $shop_page = get_page_by_path( 'kit-shop' );
  $reg_page  = get_page_by_path( 'kit-developer-registration' );
  $shop_url  = $shop_page ? esc_url( get_permalink( $shop_page ) ) : esc_url( home_url( '/kit-shop/' ) );
  $reg_url   = $reg_page  ? esc_url( get_permalink( $reg_page ) )  : esc_url( home_url( '/kit-developer-registration/' ) );
  ?>
  <kit-finder
    shop-url="<?php echo $shop_url; ?>"
    register-url="<?php echo $reg_url; ?>">
    <noscript>
      <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin:2rem 0;">
        <a href="<?php echo $shop_url; ?>" role="button">Browse Kits to Make</a>
        <a href="<?php echo $reg_url; ?>" role="button" class="outline">Become a Kit Developer</a>
      </div>
    </noscript>
  </kit-finder>

  <!-- ===== FEATURED EDITORIAL POSTS ===== -->
  <section class="kits-posts-preview">
    <h2>Why Kit Nights Work</h2>
    <p class="subtitle">Explore the science and stories behind why families thrive with kits.</p>

    <div class="kits-grid">
      <?php
      $editorial_posts = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'tax_query'      => array(
          array(
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'editorial',
          ),
        ),
        'orderby'        => 'date',
        'order'          => 'ASC',
      ) );

      // Fallback: get any posts tagged editorial or with 'stress' in title
      if ( ! $editorial_posts->have_posts() ) {
        $editorial_posts = new WP_Query( array(
          'post_type'      => 'post',
          'posts_per_page' => 5,
          'meta_key'       => '_kits_post_type',
          'meta_value'     => 'editorial',
          'orderby'        => 'date',
          'order'          => 'ASC',
        ) );
      }

      if ( $editorial_posts->have_posts() ) :
        while ( $editorial_posts->have_posts() ) :
          $editorial_posts->the_post();
          $thumb = get_the_post_thumbnail_url( null, 'medium' );
          $img_src = $thumb ? esc_url( $thumb ) : 'https://picsum.photos/seed/' . get_the_ID() . '/400/220';
          ?>
          <div class="kit-card">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo $img_src; ?>" alt="<?php the_title_attribute(); ?>" width="400" height="220" loading="lazy">
              <div class="kit-card-body">
                <h3><?php the_title(); ?></h3>
                <p><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
              </div>
            </a>
          </div>
          <?php
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p style="text-align:center;color:var(--kits-muted);">Editorial posts coming soon. Check back shortly!</p>';
      endif;
      ?>
    </div>
  </section>

  <!-- ===== FEATURED PRODUCT KITS ===== -->
  <section class="kits-posts-preview" style="margin-top:3rem;">
    <h2>Featured Kits</h2>
    <p class="subtitle">Ready-to-order kits curated for all ages and interests.</p>

    <div class="kits-grid">
      <?php
      $product_posts = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'meta_key'       => '_kits_post_type',
        'meta_value'     => 'product',
        'orderby'        => 'date',
        'order'          => 'ASC',
      ) );

      if ( $product_posts->have_posts() ) :
        while ( $product_posts->have_posts() ) :
          $product_posts->the_post();
          $thumb = get_the_post_thumbnail_url( null, 'medium' );
          $img_src = $thumb ? esc_url( $thumb ) : 'https://picsum.photos/seed/kit' . get_the_ID() . '/400/220';
          ?>
          <div class="kit-card">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo $img_src; ?>" alt="<?php the_title_attribute(); ?>" width="400" height="220" loading="lazy">
              <div class="kit-card-body">
                <h3><?php the_title(); ?></h3>
                <p><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
              </div>
            </a>
          </div>
          <?php
        endwhile;
        wp_reset_postdata();
      else :
        echo '<p style="text-align:center;color:var(--kits-muted);">Product kits coming soon!</p>';
      endif;
      ?>
    </div>

    <?php if ( $shop_page ) : ?>
    <p style="text-align:center;margin-top:2rem;">
      <a href="<?php echo $shop_url; ?>" role="button">View All Kits in the Shop →</a>
    </p>
    <?php endif; ?>
  </section>

  <!-- ===== WHY KITS STRIP ===== -->
  <section style="background:var(--kits-green-pale);border-radius:16px;padding:2.5rem 2rem;margin:3rem 0;text-align:center;">
    <h2 style="color:var(--kits-dark);margin-bottom:0.5rem;">Why Families Love Kits</h2>
    <p style="color:var(--kits-muted);max-width:560px;margin:0 auto 2rem;">
      Research shows that shared creative activities reduce cortisol levels,
      strengthen family bonds, and build confidence in children.
    </p>
    <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;max-width:700px;margin:0 auto;">
      <?php
      $benefits = array(
        array( '🧘', 'Reduces Stress', 'Making things together calms the nervous system' ),
        array( '❤️', 'Builds Bonds', 'Shared focus creates shared memories' ),
        array( '🎨', 'Sparks Creativity', 'Every kit opens a door to new skills' ),
        array( '📵', 'Screen-Free Fun', 'Hands-on joy that outshines any screen' ),
      );
      foreach ( $benefits as $b ) : ?>
      <div style="background:white;border-radius:12px;padding:1.25rem 1.5rem;width:160px;box-shadow:0 2px 8px rgba(45,106,79,0.08);">
        <div style="font-size:2rem;margin-bottom:0.5rem;"><?php echo $b[0]; ?></div>
        <strong style="font-family:Nunito,sans-serif;color:var(--kits-dark);display:block;margin-bottom:0.25rem;"><?php echo esc_html( $b[1] ); ?></strong>
        <small style="color:var(--kits-muted);font-size:0.8rem;"><?php echo esc_html( $b[2] ); ?></small>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
