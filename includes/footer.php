  </main>

  <!-- Footer -->
  <footer class="site-footer" role="contentinfo">
    <div class="container">
      <div class="footer-top" style="justify-content: center; text-align: center;">

        <!-- Brand + Logo -->
        <div style="max-width: 520px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
          <div style="margin-bottom: 0.75rem;">
            <img
              src="<?= base_url('assets/images/charusat_official_logo.png') ?>"
              alt="CHARUSAT Logo"
              height="44"
              style="object-fit: contain; margin: 0 auto;"
            >
          </div>
          <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-muted); margin-bottom: 0.4rem;">
            CHARUSAT Campus Portal &mdash; Charotar University of Science &amp; Technology, Changa, Gujarat.
          </p>
          <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">
            NAAC A+ Accredited &bull; Est. 2009 &bull; Changa, Anand District, Gujarat &bull; info@charusat.ac.in
          </p>
        </div>

      </div>

      <div class="footer-bottom" style="text-align: center; justify-content: center;">
        <p>&copy; <?= date('Y') ?> CHARUSAT Campus Portal &mdash; Charotar University of Science &amp; Technology. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Core Scripts with Cache-Busting -->
  <script src="<?= base_url('js/main.js?v=' . filemtime(__DIR__ . '/../js/main.js')) ?>"></script>
  <?php if (isset($extraScript)): ?>
    <script src="<?= base_url($extraScript . '?v=' . (file_exists(__DIR__ . '/../' . $extraScript) ? filemtime(__DIR__ . '/../' . $extraScript) : time())) ?>"></script>
  <?php endif; ?>
</body>
</html>
