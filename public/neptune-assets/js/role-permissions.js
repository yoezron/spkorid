/**
 * Role Permissions Management JavaScript
 * Untuk meningkatkan UX pada halaman pengaturan hak akses role
 */

document.addEventListener("DOMContentLoaded", function () {
  initializePermissionHandlers();
  initializeBulkActions();
  initializeFormValidation();
});

/**
 * Initialize permission checkbox handlers
 */
function initializePermissionHandlers() {
  const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name*="permissions"]');

  allCheckboxes.forEach((checkbox) => {
    // Auto-check view permission when other permissions are checked
    if (checkbox.name.includes("[can_add]") || checkbox.name.includes("[can_edit]") || checkbox.name.includes("[can_delete]")) {
      checkbox.addEventListener("change", function () {
        if (this.checked) {
          autoCheckViewPermission(this);
        }
      });
    }

    // Prevent unchecking view if other permissions are still checked
    if (checkbox.name.includes("[can_view]")) {
      checkbox.addEventListener("change", function () {
        if (!this.checked) {
          checkOtherPermissions(this);
        }
      });
    }
  });
}

/**
 * Auto-check view permission when other permissions are checked
 */
function autoCheckViewPermission(checkbox) {
  const menuId = checkbox.name.match(/\[(\d+)\]/)[1];
  const viewCheckbox = document.querySelector(`input[name="permissions[${menuId}][can_view]"]`);

  if (viewCheckbox && !viewCheckbox.checked) {
    viewCheckbox.checked = true;
    showToast('Permission "Lihat" otomatis dicentang karena diperlukan untuk akses lainnya.', "info");
  }
}

/**
 * Check if other permissions are still checked when trying to uncheck view
 */
function checkOtherPermissions(viewCheckbox) {
  const menuId = viewCheckbox.name.match(/\[(\d+)\]/)[1];
  const otherPermissions = [
    document.querySelector(`input[name="permissions[${menuId}][can_add]"]`),
    document.querySelector(`input[name="permissions[${menuId}][can_edit]"]`),
    document.querySelector(`input[name="permissions[${menuId}][can_delete]"]`),
  ];

  const hasOtherChecked = otherPermissions.some((cb) => cb && cb.checked);

  if (hasOtherChecked) {
    viewCheckbox.checked = true;
    showToast('Permission "Lihat" tidak dapat dihapus karena permission lain masih aktif.', "warning");
  }
}

/**
 * Initialize bulk actions (Select All, Unselect All, etc.)
 */
function initializeBulkActions() {
  // Check all permissions
  const checkAllBtn = document.getElementById("checkAllBtn") || createBulkActionButton("checkAllBtn", "Pilih Semua", "check_box");
  checkAllBtn.addEventListener("click", function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      cb.checked = true;
    });
    showToast("Semua permission telah dipilih.", "success");
  });

  // Uncheck all permissions
  const uncheckAllBtn = document.getElementById("uncheckAllBtn") || createBulkActionButton("uncheckAllBtn", "Hapus Semua", "check_box_outline_blank");
  uncheckAllBtn.addEventListener("click", function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      cb.checked = false;
    });
    showToast("Semua permission telah dihapus.", "info");
  });

  // Check only view permissions
  const checkViewOnlyBtn = document.getElementById("checkViewOnlyBtn") || createBulkActionButton("checkViewOnlyBtn", "Hanya Lihat", "visibility");
  checkViewOnlyBtn.addEventListener("click", function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      if (cb.name.includes("[can_view]")) {
        cb.checked = true;
      } else {
        cb.checked = false;
      }
    });
    showToast('Hanya permission "Lihat" yang dipilih.', "info");
  });
}

/**
 * Create bulk action button if not exists
 */
function createBulkActionButton(id, text, icon) {
  const button = document.createElement("button");
  button.type = "button";
  button.id = id;
  button.className = "btn btn-outline-secondary btn-sm me-2";
  button.innerHTML = `<i class="material-icons-outlined">${icon}</i> ${text}`;

  return button;
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
  const form = document.querySelector('form[action*="update-permissions"]');

  if (form) {
    form.addEventListener("submit", function (e) {
      const checkedBoxes = document.querySelectorAll('input[type="checkbox"][name*="permissions"]:checked');

      if (checkedBoxes.length === 0) {
        e.preventDefault();
        showToast("Minimal pilih satu permission untuk role ini.", "warning");
        return false;
      }

      // Show loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons-outlined">hourglass_empty</i> Menyimpan...';
      }
    });
  }
}

/**
 * Show toast notification
 */
function showToast(message, type = "info") {
  // Remove existing toasts
  const existingToasts = document.querySelectorAll(".toast");
  existingToasts.forEach((toast) => toast.remove());

  const toastHtml = `
        <div class="toast align-items-center text-white bg-${getBootstrapColor(type)} border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="material-icons-outlined me-2">${getToastIcon(type)}</i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

  document.body.insertAdjacentHTML("beforeend", toastHtml);

  const toast = document.querySelector(".toast:last-child");
  const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
  bsToast.show();
}

/**
 * Get Bootstrap color class for toast type
 */
function getBootstrapColor(type) {
  switch (type) {
    case "success":
      return "success";
    case "warning":
      return "warning";
    case "error":
      return "danger";
    case "info":
    default:
      return "info";
  }
}

/**
 * Get icon for toast type
 */
function getToastIcon(type) {
  switch (type) {
    case "success":
      return "check_circle";
    case "warning":
      return "warning";
    case "error":
      return "error";
    case "info":
    default:
      return "info";
  }
}

/**
 * Export functions for external use
 */
window.RolePermissions = {
  checkAllPermissions: function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      cb.checked = true;
    });
  },
  uncheckAllPermissions: function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      cb.checked = false;
    });
  },
  checkViewOnly: function () {
    document.querySelectorAll('input[type="checkbox"][name*="permissions"]').forEach((cb) => {
      cb.checked = cb.name.includes("[can_view]");
    });
  },
};

// Legacy function support
function checkAllPermissions() {
  window.RolePermissions.checkAllPermissions();
}

function uncheckAllPermissions() {
  window.RolePermissions.uncheckAllPermissions();
}
