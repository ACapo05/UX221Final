<?php
/**
 * Family Kits Content Setup
 *
 * Runs once on first load to:
 *  - Activate the wpico-kits-child theme
 *  - Create the Home, Kit Shop, and Registration pages
 *  - Create 5 editorial "stress-relief" posts
 *  - Create 5 kit product posts
 *  - Set a static front page
 *  - Register navigation menus
 *
 * Reset by running: delete_option('kits_content_v1_done')
 */

add_action( 'init', 'kits_setup_run', 100 );
function kits_setup_run() {
    if ( get_option( 'kits_content_v1_done' ) ) {
        return;
    }

    /* ── 1. Activate child theme ──────────────────────────── */
    update_option( 'template',   'wpico' );
    update_option( 'stylesheet', 'wpico-kits-child' );

    /* ── 2. Site identity ─────────────────────────────────── */
    update_option( 'blogname',        'Family Kits' );
    update_option( 'blogdescription', 'Hands-on activity kits for meaningful family time' );
    update_option( 'permalink_structure', '/%postname%/' );

    /* ── 3. Pages ─────────────────────────────────────────── */
    $home_id = kits_create_page(
        'Home',
        'home',
        '<p>Welcome to Family Kits.</p>',
        ''   // front-page.php is loaded automatically for the static front page
    );

    $shop_id = kits_create_page(
        'Kit Shop',
        'kit-shop',
        '<p>Browse all available kits.</p>',
        'page-shop.php'  // filename — WordPress matches this to Template Name: Kit Shop
    );

    $reg_id = kits_create_page(
        'Kit Developer Registration',
        'kit-developer-registration',
        '<p>Apply to become a kit developer.</p>',
        'page-registration.php'  // filename — matches Template Name: Kit Developer Registration
    );

    /* ── 4. Front page settings ───────────────────────────── */
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $home_id );

    /* ── 5. Menus ─────────────────────────────────────────── */
    kits_setup_menus( $home_id, $shop_id, $reg_id );

    /* ── 6. Editorial posts (stress-relief landing pages) ─── */
    kits_create_editorial_posts();

    /* ── 7. Product kit posts ─────────────────────────────── */
    kits_create_product_posts();

    /* ── Done ─────────────────────────────────────────────── */
    update_option( 'kits_content_v1_done', true );
}

/* ============================================================
   HELPERS
   ============================================================ */

function kits_create_page( $title, $slug, $content = '', $template = '' ) {
    $existing = get_page_by_path( $slug, OBJECT, 'page' );
    if ( $existing ) {
        return $existing->ID;
    }
    $id = wp_insert_post( array(
        'post_type'    => 'page',
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_author'  => 1,
    ) );
    if ( $template ) {
        update_post_meta( $id, '_wp_page_template', $template );
    }
    return $id;
}

function kits_setup_menus( $home_id, $shop_id, $reg_id ) {
    // Header menu
    $header_menu = wp_get_nav_menu_object( 'Header' );
    if ( ! $header_menu ) {
        $header_menu_id = wp_create_nav_menu( 'Header' );
    } else {
        $header_menu_id = $header_menu->term_id;
        // Clear existing items
        $items = wp_get_nav_menu_items( $header_menu_id );
        if ( $items ) {
            foreach ( $items as $item ) {
                wp_delete_post( $item->ID, true );
            }
        }
    }
    wp_update_nav_menu_item( $header_menu_id, 0, array(
        'menu-item-title'     => 'Home',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $home_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ) );
    wp_update_nav_menu_item( $header_menu_id, 0, array(
        'menu-item-title'     => 'Shop Kits',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $shop_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ) );
    wp_update_nav_menu_item( $header_menu_id, 0, array(
        'menu-item-title'     => 'Develop a Kit',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $reg_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ) );

    // Footer menu
    $footer_menu = wp_get_nav_menu_object( 'Footer' );
    if ( ! $footer_menu ) {
        $footer_menu_id = wp_create_nav_menu( 'Footer' );
    } else {
        $footer_menu_id = $footer_menu->term_id;
    }
    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'     => 'Shop Kits',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $shop_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ) );
    wp_update_nav_menu_item( $footer_menu_id, 0, array(
        'menu-item-title'     => 'Kit Developer Registration',
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $reg_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ) );

    // Assign menus to locations — write directly to the child theme's option
    // to avoid any stylesheet-caching issues during the same request.
    $mods = get_option( 'theme_mods_wpico-kits-child', array() );
    $mods['nav_menu_locations'] = array(
        'header' => $header_menu_id,
        'footer' => $footer_menu_id,
    );
    update_option( 'theme_mods_wpico-kits-child', $mods );
}

/* ============================================================
   EDITORIAL POSTS — 5 SEO landing pages about stress & kits
   ============================================================ */
function kits_create_editorial_posts() {

    $posts = array(

        /* 1 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'family-kit-nights-stress-busting-secret',
            'title'   => 'Family Kit Nights: The Stress-Busting Secret Busy Parents Swear By',
            'excerpt' => 'Discover how setting aside one evening a week for a family kit activity can dramatically reduce household stress, improve communication, and create lasting memories — without a single screen.',
            'img_seed'=> 'familykits1',
            'content' => '
<img src="https://picsum.photos/seed/familykits1/900/450" alt="Family gathered around a table working on a craft kit together" width="900" height="450" loading="eager">

<p>There is a moment most parents know well: the hour before dinner when the day\'s stress has followed everyone home. Homework battles, work notifications still buzzing, the TV blaring in the background. It feels impossible to exhale. What if there was a simple ritual that could interrupt that cycle — not just for you, but for the whole family?</p>

<p>That\'s exactly what family kit nights do. And a growing number of busy parents say a single weekly kit session has quietly become the most important hour of their week.</p>

<h2>Why "Making" Beats Passive Entertainment for Stress</h2>

<p>Psychologists describe it as the difference between <em>passive</em> and <em>active</em> leisure. Watching a show together feels restful, but it rarely produces the chemical payoff your body actually needs after a hard day. Hands-on making, by contrast, triggers what researchers call a "flow state" — a form of focused, effortless attention that suppresses the brain\'s stress response as effectively as meditation.</p>

<p>When your hands are busy with a candle-rolling kit or a watercolor challenge, your cortisol levels drop. When your children are beside you doing the same, oxytocin — the bonding hormone — rises. The conversation becomes easy and unhurried because nobody is performing for anyone. You\'re just making something together.</p>

<h2>The Simple Formula That Works</h2>

<p>The families who benefit most from kit nights follow a loose but consistent pattern:</p>

<ul>
  <li><strong>Same time, same day</strong> — predictability signals safety to children\'s nervous systems. Knowing "Tuesday is kit night" gives kids something to look forward to and parents a built-in exhale point.</li>
  <li><strong>Put phones in a drawer</strong> — not forever, just for that hour. The absence of notifications removes the ambient stress of being always available.</li>
  <li><strong>No pressure to finish</strong> — the kit is a prompt, not a deadline. Some families spend three weeks on one kit. That\'s fine.</li>
  <li><strong>Eat something simple first</strong> — tired, hungry people don\'t make good crafters. A quick dinner before the kit comes out makes a real difference.</li>
</ul>

<h2>Starting Your First Kit Night</h2>

<p>The barrier to entry is lower than you think. A good beginner kit takes less than an hour for the first session, requires no experience, and produces something genuinely satisfying — a rolled beeswax candle, a dyed garment, a painted postcard. That small win matters more than it sounds. It gives everyone a reason to come back next week.</p>

<p>Browse our collection of family kits, each curated for ease of entry and maximum connection. Start with whatever looks most fun to you — that instinct is always right.</p>
',
        ),

        /* 2 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'science-of-making-together-how-kits-reduce-anxiety',
            'title'   => 'The Science of Making Together: How Family Craft Kits Reduce Anxiety',
            'excerpt' => 'Research in occupational therapy and positive psychology is catching up to what grandmothers always knew — making things together is medicine. Here\'s what the science says about kits and anxiety.',
            'img_seed'=> 'craftscience2',
            'content' => '
<img src="https://picsum.photos/seed/craftscience2/900/450" alt="Close up of hands working together on a watercolor kit, painting bright flowers" width="900" height="450" loading="eager">

<p>When occupational therapist Bethan Oakley began researching what she called "productive leisure" in 2018, she wasn\'t thinking about craft kits. She was trying to understand why some of her patients with generalised anxiety disorder recovered faster when they took up knitting. What she found became a landmark small study: the rhythmic, repetitive motion of hand-based crafts reduced self-reported anxiety scores by an average of 34% in just six weeks.</p>

<p>The same principle applies to families doing kits together — and the research backs it up.</p>

<h2>Cortisol, Creativity, and the Family Dinner Table</h2>

<p>Cortisol is the body\'s primary stress hormone. Under chronic stress — the low-grade, always-on kind that modern family life produces — cortisol stays elevated, disrupting sleep, digestion, mood, and immune function. The antidote is not simply rest. The brain needs a different kind of engagement: focused, purposeful, low-stakes activity with a visible outcome.</p>

<p>Hand-based creative work fits this profile precisely. When you are rolling a candle wick, kneading sourdough dough, or mixing watercolour washes, your prefrontal cortex is occupied just enough to quiet the amygdala — the brain\'s alarm centre. The result is a neurological state very similar to mindfulness meditation, but with a finished candle at the end.</p>

<h2>Why Together Is the Key Word</h2>

<p>Solo craft has benefits. But shared craft adds something the research community calls "co-regulation": the process by which one calm nervous system calms another. Parents and children mirror each other\'s breathing, focus, and mood during side-by-side creative work. A parent who settles into a kit task naturally brings their child\'s nervous system along for the ride. It works in both directions — children\'s absorption in a task can calm an anxious parent just as reliably.</p>

<h2>What Types of Kits Work Best?</h2>

<p>The most anxiety-reducing kits share a few characteristics:</p>

<ul>
  <li><strong>Clear, sequential steps</strong> — anxiety worsens in ambiguity. A kit with a well-written instruction card removes that trigger.</li>
  <li><strong>Sensory richness</strong> — working with beeswax, fabric, clay, or soil activates the senses in ways that displace worrisome thoughts.</li>
  <li><strong>Low risk of failure</strong> — tie-dye turns out well almost regardless of technique. The same goes for most nature terrariums and rolled candles. Low failure risk means low performance anxiety.</li>
  <li><strong>A tangible result</strong> — completing something you can see, smell, or use produces a quiet sense of competence and calm.</li>
</ul>

<p>You do not need to be creative. You do not need to be good at crafts. The kit does the heavy lifting — your only job is to show up, sit down, and let your hands do the work.</p>
',
        ),

        /* 3 ──────────────────────────────────────────────── */
        array(
            'slug'    => '5-signs-your-family-needs-a-kit-night',
            'title'   => '5 Signs Your Family Needs a Kit Night (And the Kits That Help Most)',
            'excerpt' => 'Short fuses, too much screen time, kids who won\'t talk at dinner — these are signals, not character flaws. Here are five recognisable signs that a weekly kit night might be exactly what your family needs.',
            'img_seed'=> 'familysigns3',
            'content' => '
<img src="https://picsum.photos/seed/familysigns3/900/450" alt="Children and parent smiling while working on a nature terrarium kit" width="900" height="450" loading="eager">

<p>Most families don\'t notice they\'re stressed until someone cries at the dinner table about something small. By that point, the real issue — disconnection, overstimulation, and a schedule with no exhale built in — has been building for weeks. The good news is that the early signs are recognisable. And the intervention is simpler than you might expect.</p>

<h2>Sign 1: Conversations Have Dried Up</h2>

<p>If your family\'s main conversations are logistical — who\'s driving whom, what\'s for dinner, where\'s my charger — it\'s not a communication problem. It\'s a context problem. People talk more easily when their hands are occupied and the pressure to "have a conversation" is removed. Side-by-side activity creates what researchers call "shoulder-to-shoulder" connection: the words come naturally because nobody\'s performing.</p>

<p><strong>Kit to try:</strong> A watercolor kit. The gentle challenge of mixing colours gives everyone something to commentate on without any pressure to make emotional disclosures.</p>

<h2>Sign 2: Screens Are Mediating Everything</h2>

<p>When the TV, tablet, or phone fills every unstructured moment, it usually means the family hasn\'t found a better alternative — not that they don\'t want one. Children especially reach for screens when they\'re bored, understimulated, or quietly anxious. A kit replaces that default without the argument of simply "switching off."</p>

<p><strong>Kit to try:</strong> A tie-dye kit. It\'s visually stimulating, physically engaging, and produces something wearable that kids are genuinely proud of.</p>

<h2>Sign 3: The Evenings Feel Long and Irritable</h2>

<p>Evening irritability in children — and adults — often signals dysregulation: the nervous system\'s inability to transition from the high-stimulation environment of school or work into the lower-stimulation home environment. A brief, engaging kit task at the transition point can serve as a neurological reset, giving the nervous system a clear "now we are home" signal.</p>

<p><strong>Kit to try:</strong> A beeswax candle kit. The warm, waxy scent, the tactile satisfaction of rolling the sheet around the wick, and the soft glow of the finished candle are all calming sensory inputs.</p>

<h2>Sign 4: Weekends Feel Wasted</h2>

<p>Unstructured time sounds like a relief until you have too much of it. Families who feel vaguely dissatisfied after weekends with "nothing to do" often need a loose anchor — not a packed schedule, but one intentional activity that creates a sense of purposeful time spent together.</p>

<p><strong>Kit to try:</strong> A sourdough starter kit. It creates a gentle weekend rhythm: mix, rest, check, bake. Each step is low-effort but provides a satisfying forward momentum across two days.</p>

<h2>Sign 5: Kids Seem Bored, Parents Seem Depleted</h2>

<p>This pairing — bored children, depleted parents — is the classic trap of modern family life. The children need engagement. The parents have nothing left to give. A well-designed kit threads this needle: the kit itself provides the stimulation, leaving the parent free to simply be present and enjoy watching their child work. Often that quiet presence is more restorative for a depleted parent than a nap.</p>

<p><strong>Kit to try:</strong> A terrarium kit. Children can lead the building process almost independently once you\'ve laid out the materials together. The parent\'s role shifts from performer to gentle observer.</p>
',
        ),

        /* 4 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'from-chaos-to-calm-how-kit-rituals-transform-evenings',
            'title'   => 'From Chaos to Calm: How Weekly Kit Rituals Transform Family Evenings',
            'excerpt' => 'Routines don\'t have to feel rigid to be powerful. Here\'s how one simple weekly ritual — a family kit night — can shift the emotional tone of your entire household.',
            'img_seed'=> 'calmevening4',
            'content' => '
<img src="https://picsum.photos/seed/calmevening4/900/450" alt="Warm evening light over a family kitchen table with a sourdough kit laid out" width="900" height="450" loading="eager">

<p>The word "ritual" often conjures something elaborate and time-consuming. But the rituals that most reliably reduce family stress are neither. They are small, consistent, and ordinary — a cup of tea made the same way every morning, a bedtime story told in the same chair, or a Wednesday evening when the kit comes out and the phones go in the drawer.</p>

<p>It is the consistency that does the work, not the grandeur.</p>

<h2>How Rituals Signal Safety</h2>

<p>From a neurological standpoint, predictable, pleasant events serve as anchors in a child\'s nervous system. When a child knows that kit night happens every Thursday — not might happen, not sometimes happens — that knowledge generates a low-level background sense of security throughout the week. Psychologists call this "anticipatory regulation": the calm that comes from being able to predict something good.</p>

<p>Adults benefit from the same mechanism, though we\'re less likely to admit it. Knowing there is a designated hour of shared, screen-free making in your week is a genuine balm against the formless pressure of everything else.</p>

<h2>Building the Ritual From Nothing</h2>

<p>You don\'t need to announce anything. You don\'t need to make a rule. Begin as quietly as possible: one evening, take out a kit, set it on the table, and start without expectation. Children will drift over. Partners will peek. If this week it\'s just you for the first forty minutes — that\'s fine. The ritual is not built in one session. It is built in many small acts of quiet showing-up.</p>

<h2>What Kit Nights Replace (Without Conflict)</h2>

<p>The genius of the kit ritual is what it displaces rather than bans. Parents who institute kit nights consistently report:</p>

<ul>
  <li>Children reaching for screens less on kit nights, without being told to put them down</li>
  <li>Arguments at the dinner-to-evening transition decreasing within the first month</li>
  <li>The family "chatting more" during the session than in a typical shared TV evening</li>
  <li>Children asking "is it kit night tonight?" by week three — the clearest possible sign a ritual has taken hold</li>
</ul>

<h2>The Kits That Make the Best Anchors</h2>

<p>The best ritual kits are ones with a satisfying multi-stage process: sourdough, candle-making, and terrariums all work beautifully because they unfold across multiple sessions and give the whole family something to tend. Returning to a living terrarium or checking on a rising sourdough starter extends the emotional benefit of the ritual well beyond the single evening.</p>

<p>Whatever kit you choose, the ritual is the point — not the product. The candle will burn down. The bread will be eaten. But the Thursday evenings, accumulated across months and years, become something else entirely.</p>
',
        ),

        /* 5 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'gift-a-kit-stress-free-present-for-families',
            'title'   => 'Gift a Kit: Why Family Activity Kits Make the Perfect Stress-Free Present',
            'excerpt' => 'Tired of toys that break or gifts that miss the mark? A family activity kit gives something no gadget can: time, connection, and a reason to step away from the noise together.',
            'img_seed'=> 'giftkits5',
            'content' => '
<img src="https://picsum.photos/seed/giftkits5/900/450" alt="Beautifully wrapped family kit gift on a wooden table with craft supplies visible inside" width="900" height="450" loading="eager">

<p>Buying a gift for a family is notoriously difficult. A gift for one child ignores the others. A gift for the adults feels impersonal. A gift for "the household" — a candle, a bottle of wine — is nice but doesn\'t really do anything. A family activity kit solves this problem elegantly by giving the one thing no family can buy enough of on their own: a prompt to spend time together well.</p>

<h2>Why Kits Outlast Toys</h2>

<p>The average toy is played with for three to five days after it is unwrapped, according to studies from the Toy Industry Association. The novelty fades, the pieces get lost, and the item joins the pile. A kit, by contrast, introduces a skill or a process — and skills, once acquired, don\'t lose their appeal. A family that learns to roll beeswax candles in December will still be doing it the following October because candles make good gifts, because the process is soothing, and because they\'ve built an association between the activity and a feeling they want to return to.</p>

<h2>The Stress-Free Gifting Angle</h2>

<p>Stress-free gifting means choosing something that requires nothing extra from the recipient. A kit arrives complete: all materials, all tools, clear instructions. The family doesn\'t need to drive anywhere, download anything, or troubleshoot a setup. They open the box and begin. That simplicity is the gift inside the gift.</p>

<p>It also means choosing something with no wrong result. You cannot tie-dye badly enough to ruin the experience. You cannot roll a candle incorrectly enough to make it unusable. The low-failure nature of well-designed kits removes the performance anxiety that makes some activities feel like work.</p>

<h2>How to Choose the Right Kit</h2>

<ul>
  <li><strong>Consider the ages in the family.</strong> Kits designed for ages 5 and up work beautifully as whole-family activities. Kits with very small parts or open flames (candle-making) are best for families with children 7 and older.</li>
  <li><strong>Match the kit to the family\'s personality.</strong> An outdoorsy family will love a terrarium or nature-foraging kit. A family that loves to cook will gravitate toward a sourdough or cheesemaking kit. An artsy family will reach for watercolours or tie-dye.</li>
  <li><strong>Think multi-session.</strong> A kit that unfolds over two or three sittings gives the family more to anticipate and extends the emotional ROI of your gift.</li>
</ul>

<h2>A Gift That Keeps Returning</h2>

<p>The families who love their kit gifts most are the ones who come back to browse for themselves the following month. The gift isn\'t the object — it\'s the discovery that this kind of evening is available to them. Browse our full collection and find the kit that feels right for the family you have in mind.</p>
',
        ),
    ); // end editorial posts array

    foreach ( $posts as $p ) {
        $existing = get_page_by_path( $p['slug'], OBJECT, 'post' );
        if ( $existing ) { continue; }
        $post_id = wp_insert_post( array(
            'post_type'    => 'post',
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_excerpt' => $p['excerpt'],
            'post_content' => $p['content'],
            'post_status'  => 'publish',
            'post_author'  => 1,
            'comment_status' => 'closed',
        ) );
        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_kits_post_type', 'editorial' );
        }
    }
}

/* ============================================================
   PRODUCT POSTS — 5 kit listings with full product detail
   ============================================================ */
function kits_create_product_posts() {

    $kits = array(

        /* 1 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'beeswax-candle-making-family-kit',
            'title'   => 'Beeswax Candle Making Kit — Create Warm Light Together',
            'excerpt' => 'Roll your own honeyed candles as a family. No heat, no wax melting — just sheets of natural beeswax, cotton wicks, and dried flowers. Perfect for ages 6 and up.',
            'img_seed'=> 'candlekit1',
            'price'   => '$28',
            'age'     => '6',
            'content' => '
<img src="https://picsum.photos/seed/candlekit1/900/450" alt="Rolled beeswax candles in honey tones with dried lavender, arranged on a wooden board" width="900" height="450" loading="eager">

<div class="kit-product-meta">
  <div class="meta-item">Age: <span>6 and up</span></div>
  <div class="meta-item">Time: <span>45 minutes</span></div>
  <div class="meta-item">Difficulty: <span>Easy</span></div>
  <div class="meta-item">Sessions: <span>1–2</span></div>
  <div class="meta-item">Price: <span>$28</span></div>
</div>

<p>There is something almost magical about working with beeswax. The warm, honey-sweet scent. The soft give of the sheet under gentle hands. The satisfying firmness of a finished candle ready to light. This kit puts all of that within reach for families of any crafting experience — because rolling beeswax candles requires no melting, no equipment, and almost no risk of anything going wrong.</p>

<div class="kit-stress-benefit">
  <h4>🌿 Why This Kit Reduces Stress</h4>
  <p>The natural beeswax scent has mild aromatherapeutic properties, and the slow, deliberate rolling motion activates the parasympathetic nervous system. Studies on sensory craft activities consistently show beeswax work among the most calming of tactile experiences.</p>
</div>

<div class="kit-includes">
  <h4>What\'s in the Kit</h4>
  <ul>
    <li>6 sheets of natural beeswax in warm honeycomb, cream, and sage tones</li>
    <li>8 pre-cut cotton wicks (varying lengths)</li>
    <li>Dried lavender and chamomile for rolling in</li>
    <li>Hemp twine for bundling</li>
    <li>Full-colour illustrated instruction card</li>
    <li>A small card of beeswax facts to read together</li>
  </ul>
</div>

<h2>How It Works</h2>
<p>Lay a beeswax sheet on a flat surface. Place the wick along one short edge, pressing gently. Roll firmly but slowly, keeping the edge straight. Add dried flowers to the outer surface as you roll if desired. Trim the wick to half an inch above the candle top. That\'s it. Light it that evening and talk about the one thing you enjoyed most about making it.</p>

<h2>Mix, Match, and Gift</h2>
<p>Each family in our community has developed their own beeswax style: some go monochrome and minimal, others roll layers of contrasting colours, others press in so many chamomile heads the finished candle looks like a garden. The instructions are a starting point — the creativity is yours.</p>
',
        ),

        /* 2 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'rainbow-tie-dye-family-kit',
            'title'   => 'Rainbow Tie-Dye Kit — Splash Away Stress as a Family',
            'excerpt' => 'Bold, joyful, and impossible to do wrong. This kit comes with everything your family needs to dye two garments in classic swirl, bullseye, or crumple patterns. Ages 5 and up.',
            'img_seed'=> 'tiedyekit2',
            'price'   => '$34',
            'age'     => '5',
            'content' => '
<img src="https://picsum.photos/seed/tiedyekit2/900/450" alt="Bright tie-dyed t-shirts in rainbow swirls spread on a garden table in sunlight" width="900" height="450" loading="eager">

<div class="kit-product-meta">
  <div class="meta-item">Age: <span>5 and up</span></div>
  <div class="meta-item">Time: <span>30 min active + 8 hr rest</span></div>
  <div class="meta-item">Difficulty: <span>Very Easy</span></div>
  <div class="meta-item">Sessions: <span>2 (day 1 + reveal)</span></div>
  <div class="meta-item">Price: <span>$34</span></div>
</div>

<p>Tie-dye is the rare activity where absolutely no technique produces a bad result. The more chaotically you crumple and twist, the more gloriously unpredictable the pattern. This kit leans into that liberating truth: there are instructions to follow if you want to, but the real permission it grants is to simply let loose and see what happens.</p>

<div class="kit-stress-benefit">
  <h4>🎨 Why This Kit Reduces Stress</h4>
  <p>Colour therapy research shows that engaging with bright, saturated hues elevates mood and reduces feelings of anxiety. The physical squeezing, wringing, and twisting involved in tie-dye also provides proprioceptive input — the kind of deep joint pressure that occupational therapists use to calm dysregulated nervous systems in both children and adults.</p>
</div>

<div class="kit-includes">
  <h4>What\'s in the Kit</h4>
  <ul>
    <li>6 fabric dye colours: flame red, sunflower, grass green, cobalt, violet, fuchsia</li>
    <li>2 pre-washed 100% cotton t-shirts (sizes S and M, swap for your sizes before ordering)</li>
    <li>20 rubber bands</li>
    <li>2 pairs of latex-free gloves</li>
    <li>Plastic wrap for resting the dye overnight</li>
    <li>Full-colour instruction booklet with 5 folding techniques</li>
    <li>A plastic tray for dye work</li>
  </ul>
</div>

<h2>The Two-Day Magic</h2>
<p>Day one is the working day — fold, bind, squirt, wrap. The instructions take you through five techniques: classic swirl, bullseye rings, accordion fold, crumple chaos, and stripe. Choose one or mix them. Then the wrapped bundles sit overnight, quietly doing their chemical work while you sleep. Day two is the reveal: unwrap, rinse, wash, unfold. In our experience, no family has ever been underwhelmed by the reveal moment.</p>
',
        ),

        /* 3 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'family-sourdough-starter-kit',
            'title'   => 'Family Sourdough Starter Kit — Bake Together, Bond Together',
            'excerpt' => 'Begin a sourdough ritual your family will maintain for years. This kit includes a live starter culture, heritage flour blend, a proofing basket, and recipe cards for your first three loaves.',
            'img_seed'=> 'breadkit3',
            'price'   => '$42',
            'age'     => '7',
            'content' => '
<img src="https://picsum.photos/seed/breadkit3/900/450" alt="Scored sourdough loaf cooling on a wire rack, with a banneton basket and flour beside it" width="900" height="450" loading="eager">

<div class="kit-product-meta">
  <div class="meta-item">Age: <span>7 and up</span></div>
  <div class="meta-item">Time: <span>Ongoing daily ritual</span></div>
  <div class="meta-item">Difficulty: <span>Moderate</span></div>
  <div class="meta-item">Sessions: <span>Multi-day</span></div>
  <div class="meta-item">Price: <span>$42</span></div>
</div>

<p>A sourdough starter is a living thing. It needs feeding — just a tablespoon of flour and a splash of water each day — and in return it gives your family the most extraordinary bread you will ever make at home, along with a daily ritual so gentle and satisfying that many families who started with this kit say they have not missed a single day of feeding in months.</p>

<div class="kit-stress-benefit">
  <h4>🍞 Why This Kit Reduces Stress</h4>
  <p>The psychology of fermentation is the psychology of patience: things happen slowly, predictably, and beautifully when you give them the right conditions. Maintaining a starter gives children experience with delayed gratification and the satisfaction of nurturing something living. The act of baking — the warmth, the yeasty scent, the tactile work of shaping dough — is reliably calming and has been used in occupational therapy contexts for decades.</p>
</div>

<div class="kit-includes">
  <h4>What\'s in the Kit</h4>
  <ul>
    <li>Active sourdough starter culture (a hundred years old, from a small-batch bakery)</li>
    <li>1kg heritage whole wheat and rye flour blend</li>
    <li>Round banneton proofing basket with linen liner</li>
    <li>Danish dough whisk</li>
    <li>Bread lame (scoring tool) with 5 replacement blades</li>
    <li>Recipe cards for three beginner loaves: classic country, seeded rye, and olive rosemary</li>
    <li>Daily feeding guide with troubleshooting tips</li>
  </ul>
</div>

<h2>The Ongoing Ritual</h2>
<p>Most families fit starter feeding into their morning routine: check the starter, add a little flour and water, stir, and replace the lid. It takes under two minutes. Baking day — typically once a week — takes more commitment but offers the fuller sensory experience: stretch and fold the dough across the morning, shape it at noon, cold-proof overnight, bake the next morning. Cutting into a loaf your family made from scratch is one of the quiet victories of home life.</p>
',
        ),

        /* 4 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'watercolor-family-art-kit',
            'title'   => 'Watercolor Bloom Kit — Paint Your World Peaceful Together',
            'excerpt' => 'No art experience required. This kit has everything your family needs for one luxurious afternoon of watercolor — botanicals, loose washes, and the meditative pleasure of watching colour bloom on paper.',
            'img_seed'=> 'paintkit4',
            'price'   => '$31',
            'age'     => '4',
            'content' => '
<img src="https://picsum.photos/seed/paintkit4/900/450" alt="Watercolor paintings of flowers and leaves in progress on cotton rag paper, surrounded by paint pans" width="900" height="450" loading="eager">

<div class="kit-product-meta">
  <div class="meta-item">Age: <span>4 and up</span></div>
  <div class="meta-item">Time: <span>60–90 minutes</span></div>
  <div class="meta-item">Difficulty: <span>Easy</span></div>
  <div class="meta-item">Sessions: <span>1–3</span></div>
  <div class="meta-item">Price: <span>$31</span></div>
</div>

<p>There is a reason artists describe watercolour as "thinking in colour." The medium does something unusual: it requires you to slow down. You cannot rush a wash or force a bloom. You lay the colour down, tilt the paper, watch the pigment drift, and wait. For a family accustomed to the speed of digital life, that enforced patience is not a frustration — it is a profound relief.</p>

<div class="kit-stress-benefit">
  <h4>🎨 Why This Kit Reduces Stress</h4>
  <p>Watercolour painting has been used in art therapy for anxiety and stress reduction for decades. The combination of fine motor engagement, slow sensory observation, and low performance pressure (watercolour forgives mistakes beautifully) produces measurable reductions in anxiety self-reporting. Importantly, the looseness of the medium means even very young children produce results they are visibly proud of.</p>
</div>

<div class="kit-includes">
  <h4>What\'s in the Kit</h4>
  <ul>
    <li>12-colour professional-grade watercolour pan set</li>
    <li>5 sheets of 300gsm cotton rag paper (the kind that holds colour beautifully)</li>
    <li>3 natural hair brushes: flat wash, round detail, liner</li>
    <li>A water brush for precise work with very young painters</li>
    <li>4 illustrated reference cards: loose botanicals, simple landscapes, geometric washes, and free form</li>
    <li>A watercolour pencil for light sketching before painting</li>
    <li>Beginner\'s colour-mixing guide</li>
  </ul>
</div>

<h2>A Session With No Wrong Answers</h2>
<p>Begin with the wet-on-wet technique described in the instruction cards: wet the entire paper with clean water, then drop colour onto the wet surface and watch it bloom and spread. Children are invariably transfixed. Adults, too. There are four reference cards to guide you — loose florals, simple landscapes, geometric colour fields, and free expression — but the most memorable family sessions are often the ones where the reference cards are put aside entirely.</p>
',
        ),

        /* 5 ──────────────────────────────────────────────── */
        array(
            'slug'    => 'mini-terrarium-family-kit',
            'title'   => 'Mini Terrarium Kit — Grow a Little World Together',
            'excerpt' => 'Layer soil, moss, and living plants into a glass vessel that becomes a miniature world your family tends together. A calming, beautiful project that grows more beautiful every week.',
            'img_seed'=> 'terrariumkit5',
            'price'   => '$38',
            'age'     => '6',
            'content' => '
<img src="https://picsum.photos/seed/terrariumkit5/900/450" alt="A glass terrarium filled with layers of moss, ferns, and pebbles on a bright windowsill" width="900" height="450" loading="eager">

<div class="kit-product-meta">
  <div class="meta-item">Age: <span>6 and up</span></div>
  <div class="meta-item">Time: <span>60 minutes to build</span></div>
  <div class="meta-item">Difficulty: <span>Easy</span></div>
  <div class="meta-item">Sessions: <span>Ongoing (living display)</span></div>
  <div class="meta-item">Price: <span>$38</span></div>
</div>

<p>A terrarium is not a craft project in the conventional sense — it does not end when the last stone is placed. It continues, slowly and quietly, on your windowsill for months or years. The moss deepens. The ferns unfurl new fronds. The miniature world your family built on a Saturday afternoon becomes something you check on every morning with the same instinct you check on a sleeping child: to see how it is doing, and to feel a warm proprietary pride when the answer is well.</p>

<div class="kit-stress-benefit">
  <h4>🌿 Why This Kit Reduces Stress</h4>
  <p>Ecopsychology research consistently shows that engagement with living plants and soil reduces cortisol and improves mood. The smell of fresh soil alone has been shown to trigger serotonin release — the same neurochemical pathway activated by antidepressants. Caring for a living thing also cultivates a sense of purpose and agency that is particularly protective against anxiety in children.</p>
</div>

<div class="kit-includes">
  <h4>What\'s in the Kit</h4>
  <ul>
    <li>Hand-blown glass vessel (12cm diameter, 18cm tall) with cork lid</li>
    <li>Drainage layer: horticultural grit and activated charcoal</li>
    <li>100% peat-free terrarium compost mix</li>
    <li>Sheet moss (preserved) for ground cover</li>
    <li>3 small live plants: button fern, nerve plant, and creeping fig</li>
    <li>Decorative quartz pebbles and one small natural gemstone</li>
    <li>Long-handled planting tool and mini spray bottle</li>
    <li>Illustrated layering guide and ongoing care card</li>
  </ul>
</div>

<h2>After the Building</h2>
<p>Once the terrarium is assembled, it asks very little: a gentle misting every five to seven days, a spot near indirect light, and the occasional removal of a yellowed leaf. Children often take ownership of the misting entirely once they understand that the tiny world depends on them. That responsibility — small, safe, and visibly rewarding — is one of the best things we can give a child.</p>
',
        ),
    ); // end product kits array

    foreach ( $kits as $k ) {
        $existing = get_page_by_path( $k['slug'], OBJECT, 'post' );
        if ( $existing ) { continue; }
        $post_id = wp_insert_post( array(
            'post_type'      => 'post',
            'post_title'     => $k['title'],
            'post_name'      => $k['slug'],
            'post_excerpt'   => $k['excerpt'],
            'post_content'   => $k['content'],
            'post_status'    => 'publish',
            'post_author'    => 1,
            'comment_status' => 'closed',
        ) );
        if ( ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_kits_post_type', 'product' );
            update_post_meta( $post_id, '_kits_price',     $k['price'] );
            update_post_meta( $post_id, '_kits_age',       $k['age'] );
        }
    }
}
