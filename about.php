<?php
/**
 * Campus Portal - About Page
 * CHARUSAT University - College Information
 */

$pageTitle = 'About CHARUSAT';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

  <!-- ───────────── Hero: Logo + University Name ───────────── -->
  <section style="
    display: flex;
    align-items: center;
    gap: 2rem;
    background: linear-gradient(135deg, #0f2247 0%, #1a3a6b 60%, #0f2247 100%);
    border-radius: var(--radius-lg, 12px);
    padding: 2.5rem 2.5rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
  " aria-label="CHARUSAT University Header">

    <!-- Logo -->
    <div style="flex-shrink: 0;">
      <img
        src="<?= base_url('assets/images/charusat_official_logo.png') ?>"
        alt="CHARUSAT University Logo"
        style="max-height: 90px; width: auto; object-fit: contain; max-width: 100%;"
      >
    </div>

    <!-- Text block -->
    <div style="flex: 1; min-width: 220px;">
      <span style="
        display: inline-block;
        background: #f5c518;
        color: #1a3a6b;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        border-radius: 4px;
        padding: 0.2rem 0.65rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
      ">Changa · Anand · Gujarat</span>

      <h1 style="color: #ffffff; font-size: 1.75rem; margin: 0 0 0.5rem; line-height: 1.2;">
        Charotar University of Science &amp; Technology
      </h1>
      <p style="color: #c8d8f0; font-size: 0.95rem; margin: 0 0 1rem; line-height: 1.5;">
        A NAAC A+ accredited private university in Gujarat, committed to excellence in
        engineering, technology, pharmacy, management, and applied sciences.
      </p>

      <!-- Quick stats -->
      <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        <div style="text-align: center;">
          <div style="color: #f5c518; font-size: 1.4rem; font-weight: 800;">2009</div>
          <div style="color: #c8d8f0; font-size: 0.75rem;">Est. (University)</div>
        </div>
        <div style="text-align: center;">
          <div style="color: #f5c518; font-size: 1.4rem; font-weight: 800;">70+</div>
          <div style="color: #c8d8f0; font-size: 0.75rem;">Programs</div>
        </div>
        <div style="text-align: center;">
          <div style="color: #f5c518; font-size: 1.4rem; font-weight: 800;">A+</div>
          <div style="color: #c8d8f0; font-size: 0.75rem;">NAAC Grade</div>
        </div>
        <div style="text-align: center;">
          <div style="color: #f5c518; font-size: 1.4rem; font-weight: 800;">6</div>
          <div style="color: #c8d8f0; font-size: 0.75rem;">Faculties</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ───────────── About / Overview ───────────── -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>🏛️ About CHARUSAT</h2>
    </div>
    <div class="card-body">
      <p style="line-height: 1.75; color: var(--text-muted);">
        <strong style="color: var(--text-main);">Charotar University of Science and Technology (CHARUSAT)</strong>
        is a premier private university situated in <strong>Changa, Anand District, Gujarat, India</strong>.
        The university traces its roots to the year <strong>2000</strong>, when the
        <em>Charotar Institute of Technology – Changa (CITC)</em> was established under the
        Education Campus Changa (ECC) banner. On <strong>July 7, 2009</strong>, it was officially
        granted full university status under the <em>Gujarat Private Universities Act</em>, marking
        its transformation into CHARUSAT.
      </p>
      <p style="line-height: 1.75; color: var(--text-muted); margin-top: 1rem;">
        The university is promoted and managed by <strong>Shri Charotar Moti Sattavis Patidar
        Kelavani Mandal</strong>, a non-profit charitable trust with a rich legacy of over a century
        in social service and educational philanthropy across the Charotar region of Gujarat.
      </p>
      <p style="line-height: 1.75; color: var(--text-muted); margin-top: 1rem;">
        Spread across a lush, eco-friendly green campus in Changa, CHARUSAT emphasises
        industry-academia collaboration, holistic student development, social outreach, and
        entrepreneurship. The university has consistently ranked among the leading private
        technical universities in India and is recognised for its vibrant research culture and
        state-of-the-art infrastructure.
      </p>
    </div>
  </article>

  <!-- ───────────── Accreditations ───────────── -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>🏅 Accreditations &amp; Approvals</h2>
    </div>
    <div class="card-body">
      <div class="form-row-2">
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem;">
          <li>&#x2B50; <strong>NAAC A+ Grade</strong> – National Assessment and Accreditation Council</li>
          <li>&#x2713; <strong>UGC Approved</strong> – University Grants Commission</li>
          <li>&#x2713; <strong>AICTE Approved</strong> – All India Council for Technical Education</li>
          <li>&#x2713; <strong>PCI Approved</strong> – Pharmacy Council of India</li>
        </ul>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem;">
          <li>&#x2713; <strong>INC Approved</strong> – Indian Nursing Council</li>
          <li>&#x2713; <strong>Recognised by Govt. of Gujarat</strong></li>
          <li>&#x2713; <strong>Member – Association of Indian Universities (AIU)</strong></li>
          <li>&#x2713; <strong>NBA Accredited Programs</strong> (select engineering programs)</li>
        </ul>
      </div>
    </div>
  </article>

  <!-- ───────────── Constituent Institutes & Faculties ───────────── -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>🎓 Constituent Institutes &amp; Faculties</h2>
    </div>
    <div class="card-body">
      <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        CHARUSAT operates through multiple constituent institutes grouped under distinct faculties,
        each offering a wide range of undergraduate, postgraduate, and doctoral programmes.
      </p>

      <div class="courses-grid">

        <!-- Faculty 1 -->
        <article class="card" style="border-left: 4px solid var(--primary);">
          <div class="card-body">
            <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Faculty of Technology &amp; Engineering</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">CSPIT &amp; DPICT</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>Chandubhai S. Patel Institute of Technology (CSPIT)</strong> — B.Tech / M.Tech in
              Computer Engineering, IT, Mechanical, Electrical, Civil, EC, AI &amp; ML.<br><br>
              <strong>Devang Patel Institute of Computer Technology (DPICT)</strong> — Specialized
              computing and software engineering disciplines.
            </p>
          </div>
        </article>

        <!-- Faculty 2 -->
        <article class="card" style="border-left: 4px solid var(--accent);">
          <div class="card-body">
            <span class="badge badge-info" style="margin-bottom: 0.5rem;">Faculty of Pharmacy</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">RPCP</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>Ramanbhai Patel College of Pharmacy (RPCP)</strong> — Offers B.Pharm, M.Pharm,
              and Doctoral studies in pharmaceutical sciences. One of the leading pharmacy institutes
              in Gujarat.
            </p>
          </div>
        </article>

        <!-- Faculty 3 -->
        <article class="card" style="border-left: 4px solid #16a34a;">
          <div class="card-body">
            <span class="badge badge-success" style="margin-bottom: 0.5rem;">Faculty of Computer Applications</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">PDPIAS – CS</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>P. D. Patel Institute of Applied Sciences – CS Dept.</strong> — BCA, MCA, and
              specialised IT courses preparing students for the rapidly evolving technology landscape.
            </p>
          </div>
        </article>

        <!-- Faculty 4 -->
        <article class="card" style="border-left: 4px solid #9333ea;">
          <div class="card-body">
            <span class="badge" style="background:#f3e8ff;color:#9333ea;margin-bottom:0.5rem;">Faculty of Management Studies</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">I3M</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>Indukaka Ipcowala Institute of Management (I3M)</strong> — BBA, MBA, and
              Management Development Programs with a strong emphasis on leadership, strategy, and
              entrepreneurship.
            </p>
          </div>
        </article>

        <!-- Faculty 5 -->
        <article class="card" style="border-left: 4px solid #ea580c;">
          <div class="card-body">
            <span class="badge" style="background:#fff7ed;color:#ea580c;margin-bottom:0.5rem;">Faculty of Applied Sciences</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">PDPIAS</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>P. D. Patel Institute of Applied Sciences (PDPIAS)</strong> — B.Sc. / M.Sc.
              in Physics, Chemistry, Mathematics, Microbiology, Biotechnology, and Biochemistry.
            </p>
          </div>
        </article>

        <!-- Faculty 6 -->
        <article class="card" style="border-left: 4px solid #0891b2;">
          <div class="card-body">
            <span class="badge" style="background:#ecfeff;color:#0891b2;margin-bottom:0.5rem;">Faculty of Medical Sciences</span>
            <h3 style="font-size: 1rem; margin-bottom: 0.4rem;">ARIP · CIN · CIPS</h3>
            <p style="font-size: 0.87rem; color: var(--text-muted); line-height: 1.5;">
              <strong>Ashok and Rita Patel Institute of Physiotherapy (ARIP)</strong> – BPT, MPT.<br>
              <strong>Charotar Institute of Nursing (CIN)</strong> – ANM, GNM, B.Sc./M.Sc. Nursing.<br>
              <strong>Charotar Institute of Paramedical Sciences (CIPS)</strong> – Allied health programmes.
            </p>
          </div>
        </article>

      </div>
    </div>
  </article>

  <!-- ───────────── Research & Key Highlights ───────────── -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>🔬 Research Centres &amp; Key Highlights</h2>
    </div>
    <div class="card-body">
      <div class="form-row-2">
        <div>
          <h3 style="font-size: 1.05rem; margin-bottom: 0.75rem;">Research &amp; Innovation</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.55rem; font-size: 0.92rem; color: var(--text-muted);">
            <li>&#x1F4CD; <strong>Research &amp; Development Centre</strong> — Promotes interdisciplinary research, patents, and consultancy projects across faculties.</li>
            <li>&#x1F680; <strong>Space Research and Technology Centre (SRTC)</strong> — Fosters high-end education and research in space technology in collaboration with national bodies.</li>
            <li>&#x1F4A1; <strong>Centre for Innovation &amp; Entrepreneurship</strong> — Supports student startups and industry-sponsored projects.</li>
          </ul>
        </div>
        <div>
          <h3 style="font-size: 1.05rem; margin-bottom: 0.75rem;">Campus &amp; Student Life</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.55rem; font-size: 0.92rem; color: var(--text-muted);">
            <li>&#x1F3EB; Eco-friendly green campus in Changa, Anand District, Gujarat</li>
            <li>&#x1F4BC; <strong>Placement Cell</strong> — Top recruiters include TCS, Infosys, Amazon, Adani Group, IBM, Reliance, and more</li>
            <li>&#x1F3C6; Active cultural, sports, and technical fest culture</li>
            <li>&#x1F4DA; Digital library, modern labs, and smart classrooms</li>
            <li>&#x1F3E0; On-campus hostels for boys and girls with full amenities</li>
          </ul>
        </div>
      </div>
    </div>
  </article>

  <!-- ───────────── Location & Contact ───────────── -->
  <article class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
      <h2>📍 Location &amp; Contact</h2>
    </div>
    <div class="card-body">
      <div class="form-row-2">
        <div style="font-size: 0.93rem; line-height: 1.8; color: var(--text-muted);">
          <strong style="color: var(--text-main); display: block; margin-bottom: 0.4rem;">CHARUSAT Campus Address</strong>
          CHARUSAT Campus, Changa – 388 421<br>
          Taluka: Petlad, District: Anand<br>
          Gujarat, India<br><br>
          <strong>Website:</strong>
          <a href="https://www.charusat.ac.in" target="_blank" rel="noopener noreferrer" style="color: var(--primary);">
            www.charusat.ac.in
          </a><br>
          <strong>Phone:</strong> +91-2697-265001
        </div>
        <div style="font-size: 0.93rem; line-height: 1.8; color: var(--text-muted);">
          <strong style="color: var(--text-main); display: block; margin-bottom: 0.4rem;">Sponsoring Body</strong>
          Shri Charotar Moti Sattavis Patidar Kelavani Mandal<br>
          (Non-profit trust, 100+ years of educational service)<br><br>
          <strong style="color: var(--text-main);">University Status Granted:</strong> July 7, 2009<br>
          <strong style="color: var(--text-main);">Campus Founded:</strong> 2000 (CITC)<br>
          <strong style="color: var(--text-main);">Total Programs:</strong> 70+ (UG, PG, Ph.D.)<br>
          <strong style="color: var(--text-main);">NAAC Grade:</strong> <span style="color: var(--success, #16a34a); font-weight: 700;">A+ (Outstanding)</span>
        </div>
      </div>
    </div>
  </article>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
