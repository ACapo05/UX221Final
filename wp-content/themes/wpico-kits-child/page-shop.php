<?php
/**
 * Template Name: Kit Shop
 * Lists all kit product posts for kit makers to browse.
 */
get_header();
?>

<main class="container">

  <article style="background:transparent;box-shadow:none;border:none;padding:0;">
    <header style="text-align:center;padding:2rem 0 1rem;">
      <h1 style="color:var(--kits-dark);">Browse Our Kits</h1>
      <p style="color:var(--kits-muted);font-size:1.1rem;max-width:600px;margin:0 auto;">
        All the ingredients for a great family evening, packaged and ready to go.
        Each kit is designed to help you slow down, connect, and create together.
      </p>
    </header>

    <!-- Kits grid -->
    <div class="kits-grid" style="margin-top:2.5rem;">
      <?php
      $product_posts = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_key'       => '_kits_post_type',
        'meta_value'     => 'product',
        'orderby'        => 'title',
        'order'          => 'ASC',
      ) );

      if ( $product_posts->have_posts() ) :
        while ( $product_posts->have_posts() ) :
          $product_posts->the_post();
          $price  = get_post_meta( get_the_ID(), '_kits_price', true );
          $age    = get_post_meta( get_the_ID(), '_kits_age', true );
          $thumb  = get_the_post_thumbnail_url( null, 'medium' );
          $img    = $thumb ? esc_url( $thumb ) : 'https://picsum.photos/seed/kit' . get_the_ID() . '/400/280';
          ?>
          <div class="kit-card">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo $img; ?>" alt="<?php the_title_attribute(); ?>" width="400" height="280" loading="lazy">
              <div class="kit-card-body">
                <h3><?php the_title(); ?></h3>
                <p><?php echo wp_trim_words( get_the_excerpt(), 18 ); ?></p>
                <?php if ( $age || $price ) : ?>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--kits-border);">
                  <?php if ( $age ) : ?>
                    <small style="color:var(--kits-green);font-weight:600;">Ages <?php echo esc_html( $age ); ?>+</small>
                  <?php endif; ?>
                  <?php if ( $price ) : ?>
                    <strong style="color:var(--kits-orange-dark);font-family:Nunito,sans-serif;"><?php echo esc_html( $price ); ?></strong>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </div>
            </a>
          </div>
          <?php
        endwhile;
        wp_reset_postdata();
      else :
        ?>
        <div style="text-align:center;grid-column:1/-1;padding:3rem;color:var(--kits-muted);">
          <div style="font-size:3rem;margin-bottom:1rem;">🧰</div>
          <h3 style="color:var(--kits-green);">Kits Coming Soon!</h3>
          <p>Our first batch of kits is being prepared. Check back soon.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Call to action for kit developers -->
    <div style="background:linear-gradient(135deg,var(--kits-dark) 0%,var(--kits-green) 100%);border-radius:16px;padding:2.5rem;text-align:center;margin:4rem 0 2rem;">
      <h2 style="color:#fff;margin-bottom:0.5rem;">Got a Kit Idea?</h2>
      <p style="color:rgba(255,255,255,0.85);max-width:500px;margin:0 auto 1.5rem;">
        We're always looking for talented developers to bring new kits to our community.
        Share your idea in 100 words and we'll be in touch.
      </p>
      <?php
      $reg_page = get_page_by_path( 'kit-developer-registration' );
      $reg_url  = $reg_page ? esc_url( get_permalink( $reg_page ) ) : esc_url( home_url( '/kit-developer-registration/' ) );
      ?>
      <a href="<?php echo $reg_url; ?>" role="button"
         style="background:var(--kits-orange);border-color:var(--kits-orange);color:#fff;font-family:Nunito,sans-serif;font-weight:800;font-size:1.05rem;padding:0.85rem 2.5rem;border-radius:50px;display:inline-block;text-decoration:none;">
        Apply to Be a Developer →
      </a>
    </div>

  </article>
</main>

<?php get_footer(); ?>
