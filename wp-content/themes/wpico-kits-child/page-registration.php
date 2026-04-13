<?php
/**
 * Template Name: Kit Developer Registration
 * Registration page — for prospective kit developers to apply.
 */
get_header();

$success = isset( $_GET['kits_applied'] ) && '1' === $_GET['kits_applied'];
$error   = isset( $_GET['kits_error'] )   ? sanitize_text_field( wp_unslash( $_GET['kits_error'] ) ) : '';
?>

<main class="container">

  <!-- Page intro -->
  <article style="max-width:780px;margin:0 auto;">
    <header>
      <h1 style="color:var(--kits-dark);">Become a Kit Developer</h1>
      <p style="font-size:1.1rem;color:var(--kits-muted);margin-top:0.25rem;">
        Have a brilliant idea for a family activity kit? Tell us about it — we'd love to hear from you.
      </p>
    </header>

    <?php if ( $success ) : ?>
    <!-- SUCCESS STATE -->
    <div style="background:#D8F3DC;border:2px solid #40916C;border-radius:12px;padding:2rem;text-align:center;margin:2rem 0;">
      <div style="font-size:3rem;margin-bottom:0.75rem;">🎉</div>
      <h2 style="color:#1B4332;margin:0 0 0.5rem;">Application Received!</h2>
      <p style="color:#2D6A4F;margin:0;">
        Thank you for applying. Our team reviews all submissions weekly.
        We'll be in touch at the email address you provided.
      </p>
    </div>

    <?php else : ?>

    <?php if ( 'wordcount' === $error ) : ?>
    <div style="background:#FFE8E8;border:2px solid #E76F51;border-radius:8px;padding:1rem 1.5rem;margin-bottom:1.5rem;">
      <strong style="color:#C1440E;">Please write between 80 and 120 words</strong> in your kit summary.
      Use the word counter below to check before submitting.
    </div>
    <?php elseif ( 'missing' === $error ) : ?>
    <div style="background:#FFE8E8;border:2px solid #E76F51;border-radius:8px;padding:1rem 1.5rem;margin-bottom:1.5rem;">
      <strong style="color:#C1440E;">Please fill in all required fields</strong> before submitting.
    </div>
    <?php endif; ?>

    <!-- ===== WHAT WE'RE LOOKING FOR ===== -->
    <div style="background:var(--kits-green-pale);border-radius:12px;padding:1.5rem;margin-bottom:2rem;">
      <h3 style="color:var(--kits-dark);margin-top:0;">What We're Looking For</h3>
      <ul style="margin:0;color:var(--kits-green);">
        <li>Original kit ideas that families can enjoy together at home</li>
        <li>Safe, age-appropriate materials and clear instructions</li>
        <li>A focus on creativity, calm, and connection — not screens</li>
        <li>Reliable supply chain capability (you can source your own materials)</li>
      </ul>
    </div>

    <!-- ===== REGISTRATION FORM ===== -->
    <form
      method="post"
      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
      id="kits-registration-form"
      novalidate>

      <?php wp_nonce_field( 'kits_registration', 'kits_nonce' ); ?>
      <input type="hidden" name="action" value="kits_register">

      <!-- Name -->
      <label for="kits_name">
        Your Full Name <span style="color:var(--kits-orange-dark);" aria-hidden="true">*</span>
      </label>
      <input
        type="text"
        id="kits_name"
        name="kits_name"
        placeholder="Jane Smith"
        required
        autocomplete="name"
        value="<?php echo esc_attr( wp_unslash( $_POST['kits_name'] ?? '' ) ); ?>">

      <!-- Email -->
      <label for="kits_email">
        Email Address <span style="color:var(--kits-orange-dark);" aria-hidden="true">*</span>
      </label>
      <input
        type="email"
        id="kits_email"
        name="kits_email"
        placeholder="jane@example.com"
        required
        autocomplete="email"
        value="<?php echo esc_attr( wp_unslash( $_POST['kits_email'] ?? '' ) ); ?>">

      <!-- Brand / Business Name -->
      <label for="kits_brand">Brand or Business Name <small>(optional)</small></label>
      <input
        type="text"
        id="kits_brand"
        name="kits_brand"
        placeholder="e.g. Sunshine Kits Co."
        autocomplete="organization"
        value="<?php echo esc_attr( wp_unslash( $_POST['kits_brand'] ?? '' ) ); ?>">

      <!-- Kit Types -->
      <fieldset style="border:1px solid var(--kits-border);border-radius:10px;padding:1rem 1.5rem;margin-bottom:1.5rem;">
        <legend style="font-family:Nunito,sans-serif;font-weight:700;color:var(--kits-green);padding:0 0.5rem;">
          What types of kits do you have in mind?
        </legend>
        <?php
        $kit_types = array(
          'craft'   => '🎨 Arts &amp; Crafts',
          'food'    => '🍞 Food &amp; Cooking',
          'nature'  => '🌿 Nature &amp; Garden',
          'science' => '🔬 Science &amp; Discovery',
          'play'    => '🎲 Games &amp; Play',
          'wellness'=> '🧘 Mindfulness &amp; Wellness',
        );
        foreach ( $kit_types as $val => $label ) :
          $checked = isset( $_POST['kits_types'] ) && in_array( $val, (array) wp_unslash( $_POST['kits_types'] ) ) ? 'checked' : '';
          ?>
          <label style="display:flex;align-items:center;gap:0.5rem;font-weight:400;cursor:pointer;margin-bottom:0.4rem;">
            <input type="checkbox" name="kits_types[]" value="<?php echo esc_attr( $val ); ?>" <?php echo $checked; ?>>
            <?php echo $label; ?>
          </label>
        <?php endforeach; ?>
      </fieldset>

      <!-- 100-Word Summary -->
      <label for="kits_summary">
        Why would you make an interesting kit developer?
        <span style="color:var(--kits-orange-dark);" aria-hidden="true">*</span>
        <small style="font-weight:400;color:var(--kits-muted);display:block;margin-top:0.2rem;">
          Write approximately 100 words about your background, kit idea, and what makes your kits special.
        </small>
      </label>

      <textarea
        id="kits_summary"
        name="kits_summary"
        rows="8"
        placeholder="Tell us about yourself and your kit idea. What inspired you? What experience do you bring? Why would families love your kits? Aim for approximately 100 words..."
        required
        aria-describedby="word-count-display"><?php echo esc_textarea( wp_unslash( $_POST['kits_summary'] ?? '' ) ); ?></textarea>

      <!-- Word Counter -->
      <div id="word-count-display"
           style="display:flex;align-items:center;gap:0.75rem;margin-top:0.5rem;margin-bottom:1.5rem;font-family:Nunito,sans-serif;font-size:0.9rem;">
        <span>Word count:</span>
        <strong id="kits-word-count"
                style="font-size:1.1rem;color:var(--kits-muted);min-width:2.5rem;text-align:center;">0</strong>
        <div id="kits-word-bar-bg"
             style="flex:1;height:8px;background:#E0EDE7;border-radius:99px;overflow:hidden;max-width:260px;">
          <div id="kits-word-bar"
               style="height:100%;width:0%;background:var(--kits-muted);border-radius:99px;transition:width 0.3s ease,background 0.3s ease;"></div>
        </div>
        <span id="kits-word-status" style="color:var(--kits-muted);font-size:0.8rem;">Target: ~100 words</span>
      </div>

      <!-- Consent -->
      <label style="display:flex;align-items:flex-start;gap:0.75rem;font-weight:400;cursor:pointer;margin-bottom:1.5rem;">
        <input type="checkbox" name="kits_consent" required style="margin-top:3px;flex-shrink:0;">
        <span style="font-size:0.9rem;color:var(--kits-muted);">
          I agree that Family Kits may contact me about my application at the email address provided.
          I understand my submission will be reviewed by the editorial team.
        </span>
      </label>

      <!-- Submit -->
      <button type="submit" id="kits-submit-btn" style="width:100%;font-size:1.1rem;padding:1rem;">
        Submit My Application
      </button>

      <p style="text-align:center;font-size:0.85rem;color:var(--kits-muted);margin-top:1rem;">
        Not ready to apply? <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Browse kits first</a>
        to get inspired by what we carry.
      </p>

    </form>

    <?php endif; // end !success ?>

  </article>
</main>

<!-- ===== WORD COUNTER JAVASCRIPT ===== -->
<script>
(function() {
  'use strict';

  const textarea   = document.getElementById('kits_summary');
  const countEl    = document.getElementById('kits-word-count');
  const barEl      = document.getElementById('kits-word-bar');
  const statusEl   = document.getElementById('kits-word-status');
  const submitBtn  = document.getElementById('kits-submit-btn');

  if (!textarea) return;

  function countWords(text) {
    return text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
  }

  function updateCounter() {
    const words  = countWords(textarea.value);
    const target = 100;
    const min    = 80;
    const max    = 120;
    const pct    = Math.min((words / max) * 100, 100);

    countEl.textContent = words;
    barEl.style.width   = pct + '%';

    if (words < min) {
      barEl.style.background  = '#F4A261';   /* orange — not enough */
      countEl.style.color     = '#C1440E';
      statusEl.textContent    = words === 0 ? 'Target: ~100 words' : `Need ${min - words} more word${min - words === 1 ? '' : 's'}`;
      statusEl.style.color    = '#C1440E';
    } else if (words > max) {
      barEl.style.background  = '#E76F51';   /* red-orange — too many */
      countEl.style.color     = '#C1440E';
      statusEl.textContent    = `${words - max} word${words - max === 1 ? '' : 's'} over limit`;
      statusEl.style.color    = '#C1440E';
    } else {
      barEl.style.background  = '#2D6A4F';   /* green — good */
      countEl.style.color     = '#2D6A4F';
      statusEl.textContent    = '✓ Great length!';
      statusEl.style.color    = '#2D6A4F';
    }
  }

  textarea.addEventListener('input', updateCounter);
  updateCounter(); // Run on page load if re-populated

  // Form validation before submit
  const form = document.getElementById('kits-registration-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      const words = countWords(textarea.value);
      if (words < 80 || words > 120) {
        e.preventDefault();
        textarea.focus();
        statusEl.style.color = '#C1440E';
        const msg = words < 80
          ? `Your summary is ${words} words. Please write at least 80 words (target ~100).`
          : `Your summary is ${words} words. Please reduce it to 120 words or fewer.`;
        alert(msg);
      }
    });
  }
})();
</script>

<?php get_footer(); ?>
