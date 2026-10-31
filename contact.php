<?php
/**
 * CHARUSAT Student Portal - Official Contact Information
 * Sourced directly from official university records (charusat.ac.in).
 */

$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div style="margin-bottom: 2rem;">
    <span class="badge badge-primary">Get In Touch</span>
    <h1 style="margin-top: 0.5rem;">Contact CHARUSAT University</h1>
    <p style="color: var(--text-muted); font-size: 1.05rem;">
      Official administrative, academic, and admissions directory for Charotar University of Science and Technology.
    </p>
  </div>

  <!-- Primary Contact Directory Grid -->
  <div class="form-row-2" style="margin-bottom: 2rem;">
    
    <!-- Campus Address & Timing Card -->
    <article class="card">
      <div class="card-header">
        <h2>📍 Main Campus Address</h2>
      </div>
      <div class="card-body" style="line-height: 1.8;">
        <p style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">
          Charotar University of Science and Technology (CHARUSAT)
        </p>
        <p style="color: var(--text-muted); margin-bottom: 1.25rem;">
          CHARUSAT Campus, Off Nadiad-Petlad Highway,<br>
          Changa – 388 421, Taluka: Petlad, District: Anand,<br>
          Gujarat, India.
        </p>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; margin-top: 1rem;">
          <h3 style="font-size: 0.95rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
            🕒 University Office Hours
          </h3>
          <p style="margin-bottom: 0.25rem;"><strong>Monday – Saturday:</strong> 9:00 AM – 4:30 PM</p>
          <p style="font-size: 0.85rem; color: var(--text-muted);">
            <em>(Closed on 2nd &amp; 4th Saturdays and Public Holidays)</em>
          </p>
        </div>
      </div>
    </article>

    <!-- General Inquiries & Central Numbers -->
    <article class="card">
      <div class="card-header">
        <h2>📞 Central Telephones &amp; Emails</h2>
      </div>
      <div class="card-body" style="line-height: 1.8;">
        <div style="margin-bottom: 1.25rem;">
          <h3 style="font-size: 0.95rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
            Central Helpline / Reception
          </h3>
          <p style="font-size: 1.1rem; font-weight: 700; color: var(--primary); margin-bottom: 0.25rem;">
            +91-2697-265011 / +91-2697-265021
          </p>
          <p style="font-size: 0.88rem; color: var(--text-muted);">EPABX Campus Exchange</p>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <h3 style="font-size: 0.95rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
            Key Administrative Offices
          </h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.92rem;">
            <li>
              <strong>General Inquiries:</strong> 
              <a href="mailto:info@charusat.ac.in" style="color: var(--primary);">info@charusat.ac.in</a>
            </li>
            <li>
              <strong>Registrar Office:</strong> 
              <a href="mailto:registrar@charusat.ac.in" style="color: var(--primary);">registrar@charusat.ac.in</a>
            </li>
            <li>
              <strong>Provost Office:</strong> 
              <a href="mailto:provost@charusat.ac.in" style="color: var(--primary);">provost@charusat.ac.in</a>
            </li>
            <li>
              <strong>Admissions Desk:</strong> 
              <a href="mailto:admission@charusat.ac.in" style="color: var(--primary);">admission@charusat.ac.in</a>
            </li>
          </ul>
        </div>
      </div>
    </article>

  </div>

  <!-- Constituent Institutes Contacts -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>🏛️ Constituent Institutes Direct Desks</h2>
    </div>
    <div class="card-body">
      <div class="courses-grid">
        
        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">CSPIT (Engineering &amp; Technology)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">Chandubhai S. Patel Institute of Technology</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265112</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:info.cspit@charusat.ac.in">info.cspit@charusat.ac.in</a></div>
          </div>
        </div>

        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">DEPSTAR (Computer Science &amp; IT)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">Devang Patel Institute of Computer Technology</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265123</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:admission_depstar@charusat.ac.in">admission_depstar@charusat.ac.in</a></div>
          </div>
        </div>

        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">RPCP (Pharmacy)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">Ramanbhai Patel College of Pharmacy</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265144</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:admission_rpcp@charusat.ac.in">admission_rpcp@charusat.ac.in</a></div>
          </div>
        </div>

        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">I2IM (Management Studies)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">Indukaka Ipcowala Institute of Management</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265155</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:admission_i2im@charusat.ac.in">admission_i2im@charusat.ac.in</a></div>
          </div>
        </div>

        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">PDPIAS (Applied Sciences)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">P. D. Patel Institute of Applied Sciences</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265177</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:info.pdpias@charusat.ac.in">info.pdpias@charusat.ac.in</a></div>
          </div>
        </div>

        <div style="padding: 1rem; background: var(--bg-alt); border-radius: var(--radius-md);">
          <strong style="color: var(--text-main); display: block; font-size: 0.95rem;">ARIP / CIN (Medical &amp; Nursing)</strong>
          <small style="color: var(--text-muted); display: block; margin: 0.25rem 0 0.5rem;">Physiotherapy &amp; Nursing Institutes</small>
          <div style="font-size: 0.88rem;">
            <div>📞 <strong>Phone:</strong> +91-2697-265188</div>
            <div>✉️ <strong>Email:</strong> <a href="mailto:info.healthcare@charusat.ac.in">info.healthcare@charusat.ac.in</a></div>
          </div>
        </div>

      </div>
    </div>
  </article>

  <!-- Connect & Feedback Banner -->
  <section class="card" style="background: linear-gradient(135deg, var(--bg-surface), var(--bg-alt)); border-left: 6px solid var(--accent); padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
      <div style="max-width: 650px;">
        <h3 style="margin-bottom: 0.5rem;">Are you a current student with feedback or suggestions?</h3>
        <p style="color: var(--text-muted); margin-bottom: 0;">
          Submit your queries, course suggestions, or campus feedback directly through our dedicated student feedback portal.
        </p>
      </div>
      <div>
        <a href="<?= base_url('feedback.php') ?>" class="btn btn-primary">
          ✉️ Open Student Feedback Form
        </a>
      </div>
    </div>
  </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
