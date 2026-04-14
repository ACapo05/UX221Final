/**
 * <kit-finder> Custom Web Component
 *
 * Displays two clear paths on the homepage:
 *   1. Kit Makers → shop page (browse kits to build at home)
 *   2. Kit Developers → registration page (apply to create kits)
 *
 * Attributes:
 *   shop-url      – URL for the kits shop page
 *   register-url  – URL for the developer registration page
 *
 * Usage:
 *   <kit-finder shop-url="/kit-shop/" register-url="/kit-developer-registration/"></kit-finder>
 */
class KitFinder extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
  }

  static get observedAttributes() {
    return ['shop-url', 'register-url'];
  }

  connectedCallback() {
    this.render();
  }

  attributeChangedCallback() {
    this.render();
  }

  render() {
    const shopUrl     = this.getAttribute('shop-url')     || '/kit-shop/';
    const registerUrl = this.getAttribute('register-url') || '/kit-developer-registration/';

    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          width: 100%;
          margin: 2.5rem 0;
          font-family: 'Nunito', 'Open Sans', -apple-system, sans-serif;
        }

        .kf-wrapper {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 1.5rem;
          max-width: 860px;
          margin: 0 auto;
        }

        @media (max-width: 600px) {
          .kf-wrapper { grid-template-columns: 1fr; }
        }

        .kf-card {
          border-radius: 16px;
          padding: 2.5rem 2rem;
          display: flex;
          flex-direction: column;
          align-items: center;
          text-align: center;
          text-decoration: none;
          color: inherit;
          transition: transform 0.25s ease, box-shadow 0.25s ease;
          cursor: pointer;
          border: 2px solid transparent;
          position: relative;
          overflow: hidden;
        }

        .kf-card:hover,
        .kf-card:focus-visible {
          transform: translateY(-6px);
          outline: none;
        }

        .kf-card:focus-visible {
          border-color: #2D6A4F;
        }

        /* MAKER card - warm green */
        .kf-card--maker {
          background: linear-gradient(145deg, #2D6A4F 0%, #40916C 100%);
          box-shadow: 0 4px 24px rgba(45, 106, 79, 0.3);
        }

        .kf-card--maker:hover {
          box-shadow: 0 12px 40px rgba(45, 106, 79, 0.45);
        }

        /* DEVELOPER card - warm orange */
        .kf-card--developer {
          background: linear-gradient(145deg, #E76F51 0%, #F4A261 100%);
          box-shadow: 0 4px 24px rgba(231, 111, 81, 0.3);
        }

        .kf-card--developer:hover {
          box-shadow: 0 12px 40px rgba(231, 111, 81, 0.45);
        }

        .kf-icon {
          font-size: 3.5rem;
          margin-bottom: 1rem;
          line-height: 1;
          filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }

        .kf-eyebrow {
          font-size: 0.75rem;
          font-weight: 700;
          letter-spacing: 0.12em;
          text-transform: uppercase;
          color: rgba(255,255,255,0.75);
          margin-bottom: 0.35rem;
        }

        .kf-title {
          font-size: 1.6rem;
          font-weight: 900;
          color: #ffffff;
          margin: 0 0 0.75rem;
          line-height: 1.2;
        }

        .kf-desc {
          font-size: 0.95rem;
          color: rgba(255,255,255,0.88);
          margin: 0 0 1.75rem;
          line-height: 1.6;
          flex-grow: 1;
        }

        .kf-btn {
          display: inline-block;
          padding: 0.75rem 2rem;
          border-radius: 50px;
          font-family: 'Nunito', sans-serif;
          font-weight: 800;
          font-size: 1rem;
          text-decoration: none;
          transition: all 0.2s ease;
          letter-spacing: 0.03em;
        }

        .kf-card--maker .kf-btn {
          background: #ffffff;
          color: #2D6A4F;
        }

        .kf-card--maker .kf-btn:hover {
          background: #D8F3DC;
          color: #1B4332;
        }

        .kf-card--developer .kf-btn {
          background: #ffffff;
          color: #C1440E;
        }

        .kf-card--developer .kf-btn:hover {
          background: #FFF3EE;
          color: #9B2D0A;
        }

        /* Subtle decorative circle */
        .kf-card::before {
          content: '';
          position: absolute;
          top: -30px;
          right: -30px;
          width: 120px;
          height: 120px;
          border-radius: 50%;
          background: rgba(255,255,255,0.08);
          pointer-events: none;
        }

        .kf-heading {
          text-align: center;
          margin-bottom: 1.5rem;
          color: #1B4332;
          font-size: 1.4rem;
          font-weight: 800;
        }

        .kf-or {
          display: flex;
          align-items: center;
          justify-content: center;
          color: #5A6472;
          font-weight: 700;
          font-size: 0.9rem;
          gap: 0.75rem;
          margin: 0;
          writing-mode: vertical-rl;
          letter-spacing: 0.1em;
        }

        @media (max-width: 600px) {
          .kf-or { writing-mode: horizontal-tb; margin: 0.5rem 0; }
        }
      </style>

      <p class="kf-heading">What brings you here today?</p>

      <div class="kf-wrapper">

        <!-- PATH 1: Kit Maker (buyer / builder) -->
        <a class="kf-card kf-card--maker" href="${shopUrl}" aria-label="Browse kits to make at home">
          <div class="kf-icon">🧰</div>
          <p class="kf-eyebrow">I want to build</p>
          <h2 class="kf-title">Find a Kit to Make</h2>
          <p class="kf-desc">
            Browse our curated collection of hands-on family kits.
            Pick one up, gather the family, and unwind together.
          </p>
          <span class="kf-btn">Shop All Kits →</span>
        </a>

        <!-- PATH 2: Kit Developer (creator / supplier) -->
        <a class="kf-card kf-card--developer" href="${registerUrl}" aria-label="Apply to become a kit developer">
          <div class="kf-icon">💡</div>
          <p class="kf-eyebrow">I want to create</p>
          <h2 class="kf-title">Develop a Kit</h2>
          <p class="kf-desc">
            Got a brilliant kit idea? Apply to become a developer
            and share your creativity with families everywhere.
          </p>
          <span class="kf-btn">Apply to Develop →</span>
        </a>

      </div>
    `;
  }
}

// Register the custom element
if ( ! customElements.get('kit-finder') ) {
  customElements.define('kit-finder', KitFinder);
}
