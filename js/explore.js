/**
 * CHARUSAT Student Portal - Course Catalog (JSON Fetch, Live Search & Filter)
 */

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('courses-list');
  const searchInput = document.getElementById('course-search');
  const deptFilter = document.getElementById('dept-filter');
  const countDisplay = document.getElementById('courses-count');

  if (!container) return;

  let allCourses = [];

  // Fetch JSON asynchronously
  async function loadCourses() {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
        <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid var(--border-color); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
        <p style="margin-top: 1rem; color: var(--text-muted);">Loading courses...</p>
      </div>
    `;

    try {
      // Bust cache to guarantee latest course data
      const response = await fetch('data/campus_courses.json?v=' + Date.now());
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      allCourses = await response.json();
      renderCourses(allCourses);
    } catch (err) {
      console.error('Fetch Error:', err);
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem; background: var(--danger-bg); border-radius: var(--radius-md); color: var(--danger);">
          <h3>Failed to load course catalog</h3>
          <p>Please check your connection or contact portal administration.</p>
          <button type="button" class="btn btn-primary btn-sm" id="btn-retry" style="margin-top: 0.5rem;">Retry Loading</button>
        </div>
      `;
      document.getElementById('btn-retry')?.addEventListener('click', loadCourses);
    }
  }

  // Filter & Search handler
  function filterCourses() {
    const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
    const selectedDept = (deptFilter ? deptFilter.value : '').trim();

    const filtered = allCourses.filter((course) => {
      const matchSearch =
        !query ||
        (course.title && course.title.toLowerCase().includes(query)) ||
        (course.code && course.code.toLowerCase().includes(query)) ||
        (course.department && course.department.toLowerCase().includes(query)) ||
        (course.description && course.description.toLowerCase().includes(query));

      const matchDept = !selectedDept || course.department === selectedDept;

      return matchSearch && matchDept;
    });

    renderCourses(filtered);
  }

  // Render cards to DOM (Clean - no instructor names, no seats left, no stray marks)
  function renderCourses(courses) {
    if (countDisplay) {
      countDisplay.textContent = `Showing ${courses.length} of ${allCourses.length} offerings`;
    }

    if (courses.length === 0) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: var(--radius-lg);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
          <h3>No matching courses found</h3>
          <p style="color: var(--text-muted);">Try adjusting your search keywords or removing department filters.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = courses.map((course) => `
      <article class="card" style="display: flex; flex-direction: column;">
        <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-subtle); padding: 1.25rem 1.25rem 0.75rem;">
          <div>
            <span class="badge badge-primary">${escapeHtml(course.code)}</span>
            <span class="badge badge-info" style="margin-left: 0.35rem;">${escapeHtml(course.level || '')}</span>
          </div>
          <span style="font-weight: 700; color: var(--accent); font-size: 0.9rem;">${course.credits} Credits</span>
        </div>
        <div class="card-body" style="flex: 1 1 auto; padding: 1.25rem;">
          <h3 style="font-size: 1.2rem; margin-bottom: 0.4rem; color: var(--text-main);">${escapeHtml(course.title)}</h3>
          <p style="font-size: 0.88rem; color: var(--accent); font-weight: 600; margin-bottom: 0.75rem;">
            🏛️ ${escapeHtml(course.department)} &bull; Sem ${course.semester}
          </p>
          <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.45; margin-bottom: 1rem;">
            ${escapeHtml(course.description)}
          </p>
          <div style="margin-bottom: 0.5rem; display: flex; gap: 0.35rem; flex-wrap: wrap;">
            ${(course.tags || []).map(tag => `<span class="badge" style="background: var(--bg-alt); color: var(--text-muted); font-size: 0.7rem;">#${escapeHtml(tag)}</span>`).join('')}
          </div>
        </div>
      </article>
    `).join('');
  }

  // Event Listeners
  if (searchInput) {
    searchInput.addEventListener('input', filterCourses);
  }

  if (deptFilter) {
    deptFilter.addEventListener('change', filterCourses);
  }

  // Initial Load
  loadCourses();
});

// Keyframe for spinner animation
const style = document.createElement('style');
style.textContent = `
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
