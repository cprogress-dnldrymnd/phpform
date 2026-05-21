<?php
/**
 * Securely extracts the public reCAPTCHA Site Key from the system configuration.
 */
$config_file = __DIR__ . '/config.json';
$recaptcha_site_key = '';

if (file_exists($config_file)) {
  $config = json_decode(file_get_contents($config_file), true);
  if (isset($config['recaptcha_enabled']) && $config['recaptcha_enabled'] && !empty($config['recaptcha_site_key'])) {
    $recaptcha_site_key = $config['recaptcha_site_key'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tundra Drone | Modular Payload Platform | Coptrz</title>
  <meta name="description" content="Tundra Drone's modular payload system for the Parrot ANAFI UKR. Swap missions in 5 seconds. Spotlight, IR, Laser, Dropper, Speaker and more. Available exclusively through Coptrz.">

  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <!-- NAV -->
  <nav>
    <div class="nav-brand">
      <a href="#" class="nav-logo" aria-label="Tundra Drone home">
        <img src="assets/images/tundra-logo-white.png" alt="Tundra Drone" width="120" height="48" decoding="async">
      </a>
      <a href="https://www.coptrz.com" class="nav-coptrz" target="_blank" rel="noopener noreferrer">
        <img src="assets/images/coptrz-logo-white.png" alt="Coptrz — The Drone Experts" width="140" height="32" decoding="async">
      </a>
    </div>
    <ul class="nav-links">
      <li><a href="#payloads">Payloads</a></li>
      <li><a href="#how">How It Works</a></li>
      <li><a href="#market">Why Now</a></li>
      <li><a href="#coptrz">Coptrz</a></li>
      <li><a href="#download" class="nav-cta">Download Specs</a></li>
    </ul>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-eyebrow">Exclusively Available at Coptrz</div>
      <h1>
        Drones last.
        <span class="accent-word">Missions change.</span>
      </h1>
      <p class="hero-sub">
        Tundra's modular payload platform for the <strong>Parrot ANAFI UKR</strong> — swap
        spotlight, IR, laser, dropper and more in under <strong>5 seconds. No tools.</strong>
      </p>
      <div class="hero-actions">
        <a href="#download" class="btn-primary">Download Full Payload Specs</a>
        <a href="#payloads" class="btn-secondary">Explore Modules</a>
      </div>
      <div class="hero-stats">
        <div>
          <div class="hero-stat-num">5s</div>
          <div class="hero-stat-label">Payload Swap</div>
        </div>
        <div>
          <div class="hero-stat-num">8+</div>
          <div class="hero-stat-label">Mission Modules</div>
        </div>
        <div>
          <div class="hero-stat-num">IP54</div>
          <div class="hero-stat-label">Protection</div>
        </div>
        <div>
          <div class="hero-stat-num">±85°C</div>
          <div class="hero-stat-label">Temp Range</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-drone-frame">
        <img src="assets/images/drone1.png">
      </div>
    </div>
  </section>

  <div class="section-rule"></div>

  <!-- TRUST STRIP -->
  <div class="trust-strip">
    <div class="trust-strip-label">Trusted for</div>
    <div class="trust-items">
      <div class="trust-item"><span class="trust-dot"></span>Police & Blue Light</div>
      <div class="trust-item"><span class="trust-dot"></span>Search & Rescue</div>
      <div class="trust-item"><span class="trust-dot"></span>Defence & Security</div>
      <div class="trust-item"><span class="trust-dot"></span>Critical Infrastructure</div>
    </div>
  </div>

  <!-- MISSION BRIEF -->
  <section class="mission-brief">
    <div class="mission-brief-left">
      <div class="section-label">The Problem</div>
      <h2>Your drone is locked to one mission.</h2>
    </div>
    <div class="mission-brief-right">
      <p>Professional drones are becoming critical infrastructure — used across defence, police, SAR, and inspection. But most are still <strong>built around fixed payloads</strong>. Operators improvise with zip ties and tape. It doesn't scale, and it conflicts with sensors and antennas.</p>
      <p>Tundra solves this by standardising the mission layer. One drone. One base. <strong>Unlimited missions.</strong> Deploy the right tool for the right scenario — without buying a new aircraft.</p>
      <div class="mission-tagline">Drones last. Missions change. Payloads standardise.</div>
    </div>
  </section>

  <div class="section-rule"></div>

  <!-- PAYLOAD GRID -->
  <section class="payloads-section" id="payloads">
    <div class="payloads-header">
      <div>
        <div class="section-label">Modular Payload System</div>
        <h2>Build your mission kit</h2>
      </div>
      <div class="payloads-note">All modules attach in 5 seconds. No tools required. IP54 rated. −40 to +85°C.</div>
    </div>

    <div class="payload-grid">

      <!-- BASE -->
      <div class="payload-card">
        <div class="payload-card__media">
          <img src="assets/images/payloads/base.jpg" alt="Tundra BASE" width="800" height="500" loading="lazy" decoding="async">
          <span class="payload-card__media-label">assets/images/payloads/base.jpg</span>
        </div>
        <div class="payload-card__body">
          <div class="payload-icon"><span class="payload-icon-dot"></span>System Core</div>
          <h3>Tundra BASE</h3>
          <p>The heart of the system. Attaches to the drone in seconds, then accepts any combination of mission modules. Drone auto-detects attached module and updates the remote controller UI instantly.</p>
          <div class="payload-specs">
            <div class="spec-item">
              <div class="spec-label">Weight</div>
              <div class="spec-val">70g / 2.5oz</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Connections</div>
              <div class="spec-val">USB-C + 2 modules</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Mount time</div>
              <div class="spec-val">5 seconds</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">IP Rating</div>
              <div class="spec-val">IP54 equiv.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- SPOTLIGHT -->
      <div class="payload-card">
        <div class="payload-card__media">
          <img src="assets/images/payloads/spotlight.jpg" alt="Tundra Spotlight" width="800" height="500" loading="lazy" decoding="async">
          <span class="payload-card__media-label">assets/images/payloads/spotlight.jpg</span>
        </div>
        <div class="payload-card__body">
          <div class="payload-icon"><span class="payload-icon-dot"></span>Illumination</div>
          <h3>Spotlight</h3>
          <p>High-intensity white spotlight for pilot visibility and on-ground signalling. Brightness adjustable from the remote controller. Available in left/right versions. Optional motorised zoom lens.</p>
          <div class="payload-specs">
            <div class="spec-item">
              <div class="spec-label">Lumens</div>
              <div class="spec-val">20,000 (boost)</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Colour</div>
              <div class="spec-val">Cold white</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Weight</div>
              <div class="spec-val">150g / 5.3oz</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Control</div>
              <div class="spec-val">Remote adj.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- IR LIGHT -->
      <div class="payload-card">
        <div class="payload-card__media">
          <img src="assets/images/payloads/ir-light.jpg" alt="Tundra IR Light" width="800" height="500" loading="lazy" decoding="async">
          <span class="payload-card__media-label">assets/images/payloads/ir-light.jpg</span>
        </div>
        <div class="payload-card__body">
          <div class="payload-icon"><span class="payload-icon-dot"></span>Night Vision</div>
          <h3>IR Light</h3>
          <p>Covert IR illuminator for night-vision devices (NVDs). Intensity adjustable from remote. Available with motorised zoom lens. Left/right versions available. Invisible to the naked eye.</p>
          <div class="payload-specs">
            <div class="spec-item">
              <div class="spec-label">Wavelength</div>
              <div class="spec-val">750–940nm</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Power</div>
              <div class="spec-val">70W</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Weight</div>
              <div class="spec-val">150g / 5.3oz</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Control</div>
              <div class="spec-val">Remote adj.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- DROP -->
      <div class="payload-card">
        <div class="payload-card__media">
          <img src="assets/images/payloads/drop.jpg" alt="Tundra Drop System" width="800" height="500" loading="lazy" decoding="async">
          <span class="payload-card__media-label">assets/images/payloads/drop.jpg</span>
        </div>
        <div class="payload-card__body">
          <div class="payload-icon"><span class="payload-icon-dot"></span>Delivery</div>
          <h3>Drop System</h3>
          <p>Precision payload release controlled via remote controller. Release pin operates without power — load can be attached pre-flight. Multiple security locks prevent accidental release. Rope or 3D-printed adapter compatible.</p>
          <div class="payload-specs">
            <div class="spec-item">
              <div class="spec-label">Weight</div>
              <div class="spec-val">50g / 1.7oz</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Interface</div>
              <div class="spec-val">RC controlled</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Safety</div>
              <div class="spec-val">Multi-lock</div>
            </div>
            <div class="spec-item">
              <div class="spec-label">Config</div>
              <div class="spec-val">L+R pair</div>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA CARD -->
      <div class="payload-card--cta">
        <div>
          <h3>Speaker, Strobe, Battery — and more</h3>
          <p>3 further modules in the full lineup. Download the complete spec sheet for detailed specifications, configurations, and lead times across all 8 modules.</p>
          <div class="payload-cta-modules">
            <span class="payload-cta-pill">Speaker</span>
            <span class="payload-cta-pill">Strobe</span>
            <span class="payload-cta-pill">Battery</span>
          </div>
        </div>
        <a href="#download" class="btn-cta-card">Get the Full Lineup</a>
      </div>

    </div>
  </section>

  <div class="section-rule"></div>

  <!-- HOW IT WORKS -->
  <section class="how-section" id="how">
    <div class="how-inner">
      <div class="section-label">System Architecture</div>
      <h2>One drone. Any mission.</h2>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-num">01</div>
          <h3>Attach the BASE</h3>
          <p>Clip the Tundra BASE to your Parrot ANAFI UKR in 5 seconds. No tools, no screws. The drone detects the connection via USB-C.</p>
        </div>
        <div class="step-card">
          <div class="step-num">02</div>
          <h3>Select your module</h3>
          <p>Click your mission module — spotlight, IR, laser, dropper — into the BASE. Left and right slots accept two modules simultaneously.</p>
        </div>
        <div class="step-card">
          <div class="step-num">03</div>
          <h3>Drone auto-detects</h3>
          <p>The ANAFI UKR automatically recognises the attached payload and surfaces its controls on the remote controller display. Zero configuration.</p>
        </div>
        <div class="step-card">
          <div class="step-num">04</div>
          <h3>Execute the mission</h3>
          <p>Fly and operate payloads from the standard RC. Swap modules in under 5 seconds between sorties. One aircraft — infinite mission sets.</p>
        </div>
      </div>
      <div style="text-align: center; margin-top: 2rem;">
        <a href="#download" class="btn-primary">Download Full Payload Specs</a>
      </div>
    </div>
  </section>



  <div class="section-rule"></div>

  <!-- MARKET CONTEXT -->
  <section class="market-section" id="market">
    <div class="market-inner">
      <div class="market-left">
        <div class="section-label">Why Now</div>
        <h2>The field is already moving. The kit hasn't kept up.</h2>
        <p>Micro UAV fleets are expanding rapidly across defence, police, SAR, and infrastructure. Drones are no longer tools — they're becoming <strong>critical operational infrastructure</strong>. But the hardware layer hasn't caught up with how operators actually work.</p>
        <p>Today, operators in the field improvise. Zip ties. Tape. Velcro. Payloads that interfere with sensors. Systems that take minutes to reconfigure. <strong>There is no standardised mission layer.</strong> Tundra is building it.</p>
        <div class="market-stats">
          <div class="market-stat">
            <div class="market-stat-num">$2T+</div>
            <div class="market-stat-label">Global annual defence spending</div>
          </div>
          <div class="market-stat">
            <div class="market-stat-num">#1</div>
            <div class="market-stat-label">Fastest growing segment: unmanned systems</div>
          </div>
          <div class="market-stat">
            <div class="market-stat-num">Grp 1</div>
            <div class="market-stat-label">Micro UAVs — Tundra's focus: most widely deployed</div>
          </div>
          <div class="market-stat">
            <div class="market-stat-num">4+</div>
            <div class="market-stat-label">Sectors now operating micro UAV fleets at scale</div>
          </div>
        </div>
      </div>
      <div class="market-right">
        <div class="section-label">The Drivers</div>
        <ul class="market-drivers">
          <li>
            <span class="driver-num">01</span>
            <div class="driver-text">
              <h4>Geopolitical instability</h4>
              <p>Conflict zones are demonstrating at scale that small, modular drones outperform fixed-payload systems. The lessons from Ukraine are being standardised globally.</p>
            </div>
          </li>
          <li>
            <span class="driver-num">02</span>
            <div class="driver-text">
              <h4>Autonomous systems proliferation</h4>
              <p>Modern defence and emergency response increasingly relies on robotic and autonomous platforms. Micro UAVs are the most widely deployed category — low cost, rapidly replaceable, highly distributed.</p>
            </div>
          </li>
          <li>
            <span class="driver-num">03</span>
            <div class="driver-text">
              <h4>Made-in-China ban pressure</h4>
              <p>Western governments are actively restricting Chinese-manufactured drone hardware. Demand for trusted, Western-allied alternatives — like the Parrot ANAFI UKR platform — is growing fast.</p>
            </div>
          </li>
          <li>
            <span class="driver-num">04</span>
            <div class="driver-text">
              <h4>Mission diversity vs. airframe cost</h4>
              <p>Operators can't afford a different airframe for every mission. Modularity isn't a nice-to-have — it's a procurement necessity. One airframe, many missions, is the only sustainable model.</p>
            </div>
          </li>
        </ul>
        <div class="market-callout">
          "Operators are the MacGyers out there — paper clips, zip ties, tape. <strong>It doesn't scale.</strong> Also conflicts with the drone sensors and antennas."
          <div style="margin-top: 0.5rem; font-size: 0.78rem; opacity: 0.5; font-style: normal; letter-spacing: 0.06em; text-transform: uppercase;">— Tundra Drone Technologies</div>
        </div>
      </div>
    </div>
  </section>

  <div class="section-rule"></div>

  <!-- COPTRZ AUTHORITY SECTION -->
  <section class="coptrz-section" id="coptrz">
    <div class="coptrz-inner">
      <div class="coptrz-left">
        <div class="section-label">Sold &amp; Supported by Coptrz</div>
        <h2>The UK's professional drone authority</h2>
        <p>Tundra Drone is available exclusively through Coptrz — the UK's leading professional UAV solutions provider. When you buy through Coptrz, you get an expert team with deep operational and regulatory knowledge behind you.</p>
        <p>Procurement advice, CAA compliance, GVC training, ongoing technical support — Coptrz makes sure you fly mission-ready from day one.</p>
        <div class="coptrz-stats">
          <div class="coptrz-stat">
            <div class="coptrz-stat-val">10+</div>
            <div class="coptrz-stat-label">Years in professional drone solutions</div>
          </div>
          <div class="coptrz-stat">
            <div class="coptrz-stat-val">CAA</div>
            <div class="coptrz-stat-label">Authorised training &amp; compliance</div>
          </div>
          <div class="coptrz-stat">
            <div class="coptrz-stat-val">GVC</div>
            <div class="coptrz-stat-label">Drone licence training available</div>
          </div>
          <div class="coptrz-stat">
            <div class="coptrz-stat-val">UK</div>
            <div class="coptrz-stat-label">Based, stocked &amp; shipped nationwide</div>
          </div>
        </div>
      </div>
      <div class="coptrz-right">
        <div class="coptrz-uses-label">Who uses Tundra through Coptrz</div>
        <div class="use-cases-grid">

          <div class="use-case-card">
            <div class="use-case-icon-wrap" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
              </svg>
            </div>
            <h4>Police &amp; Emergency Services</h4>
            <p>Night surveillance, crowd management, search operations across multiple sorties.</p>
          </div>

          <div class="use-case-card">
            <div class="use-case-icon-wrap" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
              </svg>
            </div>
            <h4>Search &amp; Rescue</h4>
            <p>IR illumination for NVD teams, spotlight for casualty location, drop system for emergency supply delivery.</p>
          </div>

          <div class="use-case-card">
            <div class="use-case-icon-wrap" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
            </div>
            <h4>Defence &amp; Security</h4>
            <p>Laser designation synced to camera crosshair, covert IR support, modular ISR built around mission requirements.</p>
          </div>

          <div class="use-case-card">
            <div class="use-case-icon-wrap" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12.83 2.18 8.58 4.88a1 1 0 0 1 0 1.74l-8.58 4.88a2 2 0 0 1-1.66 0L2.6 8.8a1 1 0 0 1 0-1.74l8.57-4.88a2 2 0 0 1 1.66 0Z" />
                <path d="m22 12.65-8.97 5.1a2 2 0 0 1-1.66 0L2.6 12.65" />
                <path d="m22 17.65-8.97 5.1a2 2 0 0 1-1.66 0L2.6 17.65" />
              </svg>
            </div>
            <h4>Fleet Operators</h4>
            <p>One airframe, multiple mission profiles — procurement teams standardising across police, infrastructure and response.</p>
          </div>

        </div>
      </div>
    </div>
  </section>

  <div class="section-rule"></div>

  <!-- LEAD MAGNET -->
  <section class="lead-magnet" id="download">
    <div class="lead-magnet-content">
      <div class="section-label">Free Technical Download</div>
      <h2>Get the complete Tundra Payload Spec Sheet</h2>
      <p>Download the full modular payload system lineup — detailed specs, weights, operating parameters, and configuration options for all 8 modules. Used by procurement teams, fleet operators, and system integrators.</p>
      <ul class="lead-items">
        <li><span class="li-check">✓</span>Full technical specifications for all 8 payload modules</li>
        <li><span class="li-check">✓</span>Weight budgets, IP ratings, and temperature ranges</li>
        <li><span class="li-check">✓</span>Module compatibility and stacking configurations</li>
        <li><span class="li-check">✓</span>Production status and lead times (current as of 2026)</li>
        <li><span class="li-check">✓</span>Parrot ANAFI UKR integration requirements</li>
      </ul>
    </div>
    <form id="LeadForm" class="lead-form-card">

      <h3>Download the full payload lineup</h3>
      <p class="form-sub">Complete specs for all 8 modules — sent directly to your inbox.</p>
      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="first-name" placeholder="e.g. James" required>
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="last-name" placeholder="e.g. Morton" required>
      </div>
      <div class="form-group">
        <label>Work Email</label>
        <input type="email" name="email" placeholder="you@organisation.com" required>
      </div>

      <div class="form-group">
        <label>Organisation Type</label>
        <select name="sector" required>
          <option value="">Select your sector...</option>
          <option value="Asset Integrity &amp; Inspection">Asset Integrity &amp; Inspection</option>
          <option value="Surveying &amp; Construction">Surveying &amp; Construction</option>
          <option value="Public Safety">Public Safety</option>
          <option value="Agriculture &amp; Forestry">Agriculture &amp; Forestry</option>
          <option value="Research &amp; Education">Research &amp; Education</option>
          <option value="Energy &amp; Renewables">Energy &amp; Renewables</option>
          <option value="Oil &amp; Gas">Oil &amp; Gas</option>
          <option value="Security">Security</option>
          <option value="Sport">Sport</option>
          <option value="Film &amp; Media">Film &amp; Media</option>
          <option value="Marine">Marine</option>
          <option value="Drone Service Provider">Drone Service Provider</option>
          <option value="Government Body">Government Body</option>
          <option value="Military &amp; Defence">Military &amp; Defence</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Phone (optional)</label>
        <input type="tel" name="phone" placeholder="+44 ...">
      </div>
      <button type="submit" class="form-submit" id="tundraSubmitBtn">
        <span class="btn-text">Download Payload Spec Sheet</span>
        <svg class="tundra-spinner" style="display: none;" viewBox="0 0 50 50">
          <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
        </svg>
      </button>

      <div id="tundraFormNotice" style="display:none; margin-top: 15px; font-size: 0.85rem; font-weight: 600;"></div>
      <p class="form-privacy">Your details are handled in accordance with Coptrz Privacy Policy. No spam, ever.</p>
      <input type="hidden" name="utm_source" value="">
      <input type="hidden" name="utm_medium" value="">
      <input type="hidden" name="utm_campaign" value="">
      <input type="hidden" name="utm_term" value="">
      <input type="hidden" name="utm_content" value="">
    </form>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-left">
      <div class="footer-logos">
        <img src="assets/images/tundra-logo-white.png" alt="Tundra Drone" width="100" height="40" loading="lazy" decoding="async">
        <div class="footer-divider"></div>
        <img src="assets/images/coptrz-logo-white.png" alt="Coptrz" width="110" height="28" loading="lazy" decoding="async">
      </div>
      <p class="footer-copy">© 2026 Tundra Drone Technologies AS. All rights reserved. Tundra® is a registered trademark.<br>Distributed in the UK exclusively by Coptrz Ltd. Specifications subject to change.</p>
    </div>
    <div class="footer-links">
      <a href="https://coptrz.com/privacy-policy" target="_blank">Privacy</a>
    </div>
  </footer>
  <script>
    window.TUNDRA_CONFIG = {
      recaptchaSiteKey: '<?php echo htmlspecialchars($recaptcha_site_key, ENT_QUOTES, 'UTF-8'); ?>'
    };
  </script>
  <script src="assets/js/main.js" defer></script>
</body>

</html>