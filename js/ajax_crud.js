/**
 * =====================================================================
 * CAMPUS MANAGEMENT PORTAL - AJAX / FETCH CRUD CONTROLLER
 * Item 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
 * =====================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.getElementById('ajax-students-body');
  const searchInput = document.getElementById('ajax-search');
  const deptFilter = document.getElementById('ajax-dept-filter');
  const studentForm = document.getElementById('student-modal-form');
  const modalTitle = document.getElementById('student-modal-title');
  const modalBackdrop = document.getElementById('student-modal');
  const addBtn = document.getElementById('btn-open-add-student');

  if (!tableBody) return;

  // Initial fetch
  fetchStudents();

  // Search & Filter event listeners
  if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(fetchStudents, 250);
    });
  }

  if (deptFilter) {
    deptFilter.addEventListener('change', fetchStudents);
  }

  // Open Add Student Modal
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      resetStudentForm();
      if (modalTitle) modalTitle.textContent = 'Add New Student';
      openModal('student-modal');
    });
  }

  // Handle Form Submit (Add or Edit) via Fetch
  if (studentForm) {
    studentForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const studentId = document.getElementById('modal_student_id').value;
      const isEdit = Boolean(studentId);

      const payload = {
        id: studentId,
        roll_number: document.getElementById('modal_roll_number').value.trim(),
        first_name: document.getElementById('modal_first_name').value.trim(),
        last_name: document.getElementById('modal_last_name').value.trim(),
        email: document.getElementById('modal_email').value.trim(),
        phone: document.getElementById('modal_phone').value.trim(),
        department: document.getElementById('modal_department').value,
        semester: parseInt(document.getElementById('modal_semester').value, 10),
        gpa: parseFloat(document.getElementById('modal_gpa').value),
        status: document.getElementById('modal_status').value
      };

      const submitBtn = studentForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Saving...';

      try {
        const url = 'api/students.php';
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
          showToast(result.message || 'Student saved successfully!', 'success');
          closeModal(modalBackdrop);
          fetchStudents(); // Refresh data table
        } else {
          showToast(result.message || 'Operation failed. Check input details.', 'danger');
        }
      } catch (err) {
        console.error('AJAX Error:', err);
        showToast('Network error while processing request.', 'danger');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    });
  }

  // Event delegation on table for Edit and Delete buttons
  tableBody.addEventListener('click', async (e) => {
    // EDIT
    const editBtn = e.target.closest('.btn-ajax-edit');
    if (editBtn) {
      const studentData = JSON.parse(editBtn.getAttribute('data-student'));
      populateEditForm(studentData);
      if (modalTitle) modalTitle.textContent = `Edit Student (${studentData.roll_number})`;
      openModal('student-modal');
      return;
    }

    // DELETE
    const deleteBtn = e.target.closest('.btn-ajax-delete');
    if (deleteBtn) {
      const id = deleteBtn.getAttribute('data-id');
      const name = deleteBtn.getAttribute('data-name');

      if (confirm(`Are you sure you want to delete student "${name}"? This action cannot be undone.`)) {
        await deleteStudent(id, deleteBtn.closest('tr'));
      }
    }
  });

  // Fetch student list from REST API
  async function fetchStudents() {
    const q = searchInput ? encodeURIComponent(searchInput.value.trim()) : '';
    const dept = deptFilter ? encodeURIComponent(deptFilter.value.trim()) : '';

    tableBody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align: center; padding: 2rem;">
          <span style="color: var(--text-muted);">Loading students from API...</span>
        </td>
      </tr>
    `;

    try {
      const response = await fetch(`api/students.php?search=${q}&department=${dept}`);
      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        renderStudentRows(result.data);
      } else {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: var(--danger);">Failed to retrieve students.</td></tr>`;
      }
    } catch (err) {
      console.error('Fetch Students Error:', err);
      tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: var(--danger);">Error connecting to API.</td></tr>`;
    }
  }

  // Render rows dynamically into table
  function renderStudentRows(students) {
    if (students.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
            No students found matching your criteria.
          </td>
        </tr>
      `;
      return;
    }

    const badgeMap = {
      'Active': 'badge-success',
      'Inactive': 'badge-warning',
      'Graduated': 'badge-info',
      'Suspended': 'badge-danger'
    };

    tableBody.innerHTML = students.map((s) => `
      <tr id="student-row-${s.id}">
        <td><strong>${escapeHtml(s.roll_number)}</strong></td>
        <td>${escapeHtml(s.first_name)} ${escapeHtml(s.last_name)}</td>
        <td>${escapeHtml(s.email)}</td>
        <td>${escapeHtml(s.department)}</td>
        <td>Sem ${s.semester}</td>
        <td><strong>${parseFloat(s.gpa).toFixed(2)}</strong></td>
        <td><span class="badge ${badgeMap[s.status] || 'badge-primary'}">${escapeHtml(s.status)}</span></td>
        <td style="white-space: nowrap;">
          <button type="button" class="btn btn-outline btn-sm btn-ajax-edit" data-student='${JSON.stringify(s).replace(/'/g, "&apos;")}'>
            Edit
          </button>
          <button type="button" class="btn btn-danger btn-sm btn-ajax-delete" data-id="${s.id}" data-name="${escapeHtml(s.first_name + ' ' + s.last_name)}">
            Delete
          </button>
        </td>
      </tr>
    `).join('');
  }

  // Perform AJAX Delete
  async function deleteStudent(id, rowElement) {
    try {
      const response = await fetch(`api/students.php?id=${id}`, {
        method: 'DELETE',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const result = await response.json();

      if (result.success) {
        showToast(result.message || 'Student deleted successfully', 'success');
        if (rowElement) {
          rowElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          rowElement.style.opacity = '0';
          rowElement.style.transform = 'scale(0.95)';
          setTimeout(() => rowElement.remove(), 300);
        } else {
          fetchStudents();
        }
      } else {
        showToast(result.message || 'Could not delete student.', 'danger');
      }
    } catch (err) {
      console.error('Delete Error:', err);
      showToast('Network error while deleting record.', 'danger');
    }
  }

  function resetStudentForm() {
    studentForm.reset();
    document.getElementById('modal_student_id').value = '';
  }

  function populateEditForm(s) {
    document.getElementById('modal_student_id').value = s.id;
    document.getElementById('modal_roll_number').value = s.roll_number;
    document.getElementById('modal_first_name').value = s.first_name;
    document.getElementById('modal_last_name').value = s.last_name;
    document.getElementById('modal_email').value = s.email;
    document.getElementById('modal_phone').value = s.phone;
    document.getElementById('modal_department').value = s.department;
    document.getElementById('modal_semester').value = s.semester;
    document.getElementById('modal_gpa').value = s.gpa;
    document.getElementById('modal_status').value = s.status;
  }
});
