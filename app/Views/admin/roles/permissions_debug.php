<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
DEBUG: Hak Akses Role <?= esc($role['role_name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php 
// Load helper untuk debugging
helper('permission_debug');

// Debug semua data yang dikirim ke view
debug_view_data(get_defined_vars());

// Test koneksi database
test_database_connection();

// Debug permissions secara detail
debug_permissions($role['id'], $menus ?? null, $permissions ?? null);

// Debug struktur menu tree
debug_menu_tree($menus ?? []);
?>

<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>🔍 DEBUG: Hak Akses Role: <span class="text-primary"><?= esc($role['role_name']) ?></span></h1>
            <p class="text-warning">⚠️ Halaman ini hanya untuk debugging di development mode</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= form_open('admin/roles/update-permissions/' . $role['id']) ?>
        <?= $this->include('partials/flash_messages') ?>

        <!-- Tampilkan raw data untuk debugging -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">🔍 Raw Data Debugging</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Role Data:</h6>
                        <pre class="bg-light p-2"><?php print_r($role); ?></pre>
                    </div>
                    <div class="col-md-6">
                        <h6>Permissions Data:</h6>
                        <pre class="bg-light p-2"><?php print_r($permissions ?? 'NULL'); ?></pre>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Menus Data:</h6>
                        <pre class="bg-light p-2" style="max-height: 400px; overflow-y: auto;"><?php print_r($menus ?? 'NULL'); ?></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Test Buttons -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">🧪 Quick Test Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" onclick="testDatabaseQueries()">
                            Test Database Queries
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="testMenuModel()">
                            Test Menu Model
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="testPermissionData()">
                            Test Permission Data
                        </button>
                    </div>
                </div>
                <div id="testResults" class="mt-3" style="display: none;">
                    <h6>Test Results:</h6>
                    <div class="bg-light p-3 border rounded">
                        <div id="testOutput"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($menus)): ?>
            <div class="alert alert-success">
                ✅ Menus data tersedia: <?= count($menus) ?> menu utama ditemukan
            </div>
            
            <?php foreach ($menus as $menuIndex => $menu): ?>
                <div class="card mb-4" style="border: 2px solid #007bff;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="material-icons-outlined me-2"><?= esc($menu['menu_icon'] ?? 'menu') ?></i>
                            <?= esc($menu['menu_name'] ?? 'Unknown Menu') ?> 
                            <span class="badge bg-light text-dark ms-2">ID: <?= $menu['id'] ?? 'N/A' ?></span>
                            <span class="badge bg-info ms-1">Index: <?= $menuIndex ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Menu/Sub-Menu</th>
                                        <th class="text-center" style="width: 15%;">Debug Info</th>
                                        <th class="text-center" style="width: 10%;">View</th>
                                        <th class="text-center" style="width: 10%;">Add</th>
                                        <th class="text-center" style="width: 10%;">Edit</th>
                                        <th class="text-center" style="width: 10%;">Delete</th>
                                        <th style="width: 20%;">Raw Permission Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Menu Induk -->
                                    <tr class="bg-light">
                                        <td class="fw-bold">
                                            📁 <?= esc($menu['menu_name'] ?? 'Unknown') ?> 
                                            <small class="text-muted d-block">
                                                ID: <?= $menu['id'] ?? 'N/A' ?> | URL: <?= esc($menu['menu_url'] ?? 'N/A') ?>
                                            </small>
                                        </td>
                                        <td class="text-center small">
                                            <?php 
                                            $menuId = $menu['id'] ?? 0;
                                            $hasPermission = isset($permissions[$menuId]);
                                            ?>
                                            <span class="badge <?= $hasPermission ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $hasPermission ? 'HAS PERM' : 'NO PERM' ?>
                                            </span>
                                        </td>
                                        <?php
                                        $current_permissions = $permissions[$menu['id']] ?? [];
                                        ?>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_view]" 
                                                       value="1" 
                                                       id="view_<?= $menu['id'] ?>"
                                                       <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="view_<?= $menu['id'] ?>">
                                                    <?= isset($current_permissions['can_view']) ? $current_permissions['can_view'] : '0' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_add]" 
                                                       value="1" 
                                                       id="add_<?= $menu['id'] ?>"
                                                       <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="add_<?= $menu['id'] ?>">
                                                    <?= isset($current_permissions['can_add']) ? $current_permissions['can_add'] : '0' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_edit]" 
                                                       value="1" 
                                                       id="edit_<?= $menu['id'] ?>"
                                                       <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="edit_<?= $menu['id'] ?>">
                                                    <?= isset($current_permissions['can_edit']) ? $current_permissions['can_edit'] : '0' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_delete]" 
                                                       value="1" 
                                                       id="delete_<?= $menu['id'] ?>"
                                                       <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="delete_<?= $menu['id'] ?>">
                                                    <?= isset($current_permissions['can_delete']) ? $current_permissions['can_delete'] : '0' ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <details>
                                                <summary class="btn btn-outline-info btn-xs">Show</summary>
                                                <pre class="small mt-2"><?php print_r($current_permissions); ?></pre>
                                            </details>
                                        </td>
                                    </tr>
                                    
                                    <!-- Sub-Menu -->
                                    <?php if (!empty($menu['submenus'])): ?>
                                        <?php foreach ($menu['submenus'] as $submenuIndex => $submenu): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    📄 <i class="material-icons-outlined me-2"><?= esc($submenu['menu_icon'] ?? 'subdirectory_arrow_right') ?></i>
                                                    <?= esc($submenu['menu_name'] ?? 'Unknown Submenu') ?>
                                                    <small class="text-muted d-block">
                                                        ID: <?= $submenu['id'] ?? 'N/A' ?> | URL: <?= esc($submenu['menu_url'] ?? 'N/A') ?>
                                                        | Index: <?= $submenuIndex ?>
                                                    </small>
                                                </td>
                                                <td class="text-center small">
                                                    <?php 
                                                    $submenuId = $submenu['id'] ?? 0;
                                                    $hasSubmenuPermission = isset($permissions[$submenuId]);
                                                    ?>
                                                    <span class="badge <?= $hasSubmenuPermission ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $hasSubmenuPermission ? 'HAS PERM' : 'NO PERM' ?>
                                                    </span>
                                                </td>
                                                <?php
                                                $current_permissions = $permissions[$submenu['id']] ?? [];
                                                ?>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_view]" 
                                                               value="1" 
                                                               id="view_<?= $submenu['id'] ?>"
                                                               <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="view_<?= $submenu['id'] ?>">
                                                            <?= isset($current_permissions['can_view']) ? $current_permissions['can_view'] : '0' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_add]" 
                                                               value="1" 
                                                               id="add_<?= $submenu['id'] ?>"
                                                               <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="add_<?= $submenu['id'] ?>">
                                                            <?= isset($current_permissions['can_add']) ? $current_permissions['can_add'] : '0' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_edit]" 
                                                               value="1" 
                                                               id="edit_<?= $submenu['id'] ?>"
                                                               <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="edit_<?= $submenu['id'] ?>">
                                                            <?= isset($current_permissions['can_edit']) ? $current_permissions['can_edit'] : '0' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_delete]" 
                                                               value="1" 
                                                               id="delete_<?= $submenu['id'] ?>"
                                                               <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label small" for="delete_<?= $submenu['id'] ?>">
                                                            <?= isset($current_permissions['can_delete']) ? $current_permissions['can_delete'] : '0' ?>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <details>
                                                        <summary class="btn btn-outline-info btn-xs">Show</summary>
                                                        <pre class="small mt-2"><?php print_r($current_permissions); ?></pre>
                                                    </details>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                ⚠️ Tidak ada sub-menu untuk menu ini.
                                                <br><small>Kemungkinan: parent_id tidak sesuai atau tidak ada data submenu.</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">📊 Summary Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary"><?= count($menus) ?></h4>
                                <small>Parent Menus</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <?php 
                                $totalSubmenus = 0;
                                foreach($menus as $menu) {
                                    $totalSubmenus += count($menu['submenus'] ?? []);
                                }
                                ?>
                                <h4 class="text-info"><?= $totalSubmenus ?></h4>
                                <small>Total Submenus</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success"><?= count($permissions ?? []) ?></h4>
                                <small>Current Permissions</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <?php 
                                $totalCheckboxes = 0;
                                foreach($menus as $menu) {
                                    $totalCheckboxes += 4; // 4 permissions per menu
                                    $totalCheckboxes += count($menu['submenus'] ?? []) * 4; // 4 permissions per submenu
                                }
                                ?>
                                <h4 class="text-warning"><?= $totalCheckboxes ?></h4>
                                <small>Total Checkboxes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="alert alert-danger">
                ❌ <strong>TIDAK ADA DATA MENU YANG DITEMUKAN!</strong>
                <br><br>
                <strong>🔍 Kemungkinan penyebab:</strong>
                <ul class="mb-0">
                    <li>Tabel 'menus' kosong atau tidak ada</li>
                    <li>Method buildMenuTreeForPermissions() tidak berjalan dengan benar</li>
                    <li>Koneksi database bermasalah</li>
                    <li>Variable $menus tidak dikirim dari controller</li>
                    <li>Field is_active di tabel menus bernilai 0</li>
                </ul>
                <br>
                <strong>🛠️ Langkah debugging:</strong>
                <ol class="mb-0">
                    <li>Cek apakah tabel menus ada data: <code>SELECT * FROM menus;</code></li>
                    <li>Cek apakah MenuModel memiliki method buildMenuTreeForPermissions()</li>
                    <li>Cek log error di <code>writable/logs/</code></li>
                </ol>
            </div>
            
            <!-- Show database query results for debugging -->
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">🔍 Database Debug Info</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $db = \Config\Database::connect();
                        
                        echo "<h6>Menus Table Check:</h6>";
                        $menusQuery = $db->query("SELECT * FROM menus WHERE is_active = 1 ORDER BY parent_id, menu_order");
                        $menusResult = $menusQuery->getResultArray();
                        
                        if (empty($menusResult)) {
                            echo "<p class='text-danger'>❌ No active menus found in database</p>";
                            
                            // Check if any menus exist at all
                            $allMenusQuery = $db->query("SELECT * FROM menus");
                            $allMenusResult = $allMenusQuery->getResultArray();
                            
                            if (empty($allMenusResult)) {
                                echo "<p class='text-warning'>⚠️ No menus found at all. Table might be empty.</p>";
                            } else {
                                echo "<p class='text-info'>ℹ️ Found " . count($allMenusResult) . " menus but none are active.</p>";
                                echo "<pre class='bg-light p-2'>" . print_r($allMenusResult, true) . "</pre>";
                            }
                        } else {
                            echo "<p class='text-success'>✅ Found " . count($menusResult) . " active menus in database</p>";
                            echo "<pre class='bg-light p-2'>" . print_r($menusResult, true) . "</pre>";
                        }
                        
                    } catch (Exception $e) {
                        echo "<p class='text-danger'>❌ Database error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex gap-2 justify-content-start flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAllPermissions()">
                                <i class="material-icons-outlined">check_box</i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="uncheckAllPermissions()">
                                <i class="material-icons-outlined">check_box_outline_blank</i> Hapus Semua
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="checkViewOnly()">
                                <i class="material-icons-outlined">visibility</i> Hanya Lihat
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="showFormData()">
                                🔍 Show Form Data
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-light me-2">
                            <i class="material-icons-outlined">arrow_back</i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" onclick="logFormSubmission()">
                            <i class="material-icons-outlined">save</i> Simpan Perubahan (DEBUG)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script>
// Debug JavaScript Functions
console.log('🔍 DEBUG: Permission Debug View Loaded');

function checkAllPermissions() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    console.log('✅ All checkboxes checked:', checkboxes.length);
    showToast('All permissions selected', 'success');
}

function uncheckAllPermissions() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    console.log('❌ All checkboxes unchecked:', checkboxes.length);
    showToast('All permissions deselected', 'info');
}

function checkViewOnly() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (checkbox.name.includes('[can_view]')) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });
    console.log('👁️ Only view permissions checked');
    showToast('Only view permissions selected', 'info');
}

function showFormData() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    console.group('📋 Current Form Data:');
    let dataCount = 0;
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
        dataCount++;
    }
    console.log(`Total form fields: ${dataCount}`);
    console.groupEnd();
    
    // Show in UI
    let output = '<h6>Current Form Data:</h6><ul>';
    for (let [key, value] of formData.entries()) {
        output += `<li><code>${key}</code>: ${value}</li>`;
    }
    output += '</ul>';
    
    document.getElementById('testOutput').innerHTML = output;
    document.getElementById('testResults').style.display = 'block';
    
    showToast(`Found ${dataCount} form fields`, 'info');
}

function testDatabaseQueries() {
    console.log('🗄️ Testing database queries...');
    document.getElementById('testOutput').innerHTML = '<p>Database test would be performed here in actual implementation...</p>';
    document.getElementById('testResults').style.display = 'block';
    showToast('Database test triggered', 'info');
}

function testMenuModel() {
    console.log('🌳 Testing menu model...');
    const menus = <?= json_encode($menus ?? []) ?>;
    console.log('Menu data:', menus);
    
    let output = `<h6>Menu Model Test:</h6>
                  <p>Total parent menus: ${menus.length}</p>
                  <p>Menu structure:</p>
                  <pre>${JSON.stringify(menus, null, 2)}</pre>`;
    
    document.getElementById('testOutput').innerHTML = output;
    document.getElementById('testResults').style.display = 'block';
    showToast('Menu model test completed', 'success');
}

function testPermissionData() {
    console.log('🔐 Testing permission data...');
    const permissions = <?= json_encode($permissions ?? []) ?>;
    console.log('Permission data:', permissions);
    
    let output = `<h6>Permission Data Test:</h6>
                  <p>Total permissions: ${Object.keys(permissions).length}</p>
                  <p>Permission structure:</p>
                  <pre>${JSON.stringify(permissions, null, 2)}</pre>`;
    
    document.getElementById('testOutput').innerHTML = output;
    document.getElementById('testResults').style.display = 'block';
    showToast('Permission data test completed', 'success');
}

function logFormSubmission() {
    console.log('📤 Form submission triggered');
    showFormData();
    showToast('Form data logged to console', 'warning');
}

function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `${message} <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Debug pada DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DEBUG: DOM loaded');
    
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    console.log(`📋 Found ${checkboxes.length} checkboxes`);
    
    checkboxes.forEach((checkbox, index) => {
        console.log(`Checkbox ${index + 1}:`, {
            name: checkbox.name,
            value: checkbox.value,
            checked: checkbox.checked,
            id: checkbox.id
        });
        
        // Add change listener for debugging
        checkbox.addEventListener('change', function() {
            console.log(`Checkbox changed: ${this.name} = ${this.checked}`);
        });
    });
    
    // Log form data when submitting
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('📤 Form submitted');
            showFormData();
            
            // Prevent actual submission in debug mode
            if (confirm('This is debug mode. Do you want to actually submit the form?')) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
    }
    
    console.log('🎯 Debug setup completed');
});
</script>

<?= $this->endSection() ?>🔍 DEBUG: Hak Akses Role: <span class="text-primary"><?= esc($role['role_name']) ?></span></h1>
            <p class="text-warning">⚠️ Halaman ini hanya untuk debugging di development mode</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= form_open('admin/roles/update-permissions/' . $role['id']) ?>
        <?= $this->include('partials/flash_messages') ?>

        <!-- Tampilkan raw data untuk debugging -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">🔍 Raw Data Debugging</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Role Data:</h6>
                        <pre class="bg-light p-2"><?php print_r($role); ?></pre>
                    </div>
                    <div class="col-md-6">
                        <h6>Permissions Data:</h6>
                        <pre class="bg-light p-2"><?php print_r($permissions ?? 'NULL'); ?></pre>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Menus Data:</h6>
                        <pre class="bg-light p-2" style="max-height: 400px; overflow-y: auto;"><?php print_r($menus ?? 'NULL'); ?></pre>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($menus)): ?>
            <div class="alert alert-success">
                ✅ Menus data tersedia: <?= count($menus) ?> menu utama ditemukan
            </div>
            
            <?php foreach ($menus as $menu): ?>
                <div class="card mb-4" style="border: 2px solid #007bff;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="material-icons-outlined me-2"><?= esc($menu['menu_icon'] ?? 'menu') ?></i>
                            <?= esc($menu['menu_name'] ?? 'Unknown Menu') ?> 
                            <span class="badge bg-light text-dark ms-2">ID: <?= $menu['id'] ?? 'N/A' ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">Menu/Sub-Menu</th>
                                        <th class="text-center" style="width: 15%;">Debug Info</th>
                                        <th class="text-center" style="width: 10%;">View</th>
                                        <th class="text-center" style="width: 10%;">Add</th>
                                        <th class="text-center" style="width: 10%;">Edit</th>
                                        <th class="text-center" style="width: 10%;">Delete</th>
                                        <th style="width: 15%;">Raw Permission Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Menu Induk -->
                                    <tr class="bg-light">
                                        <td class="fw-bold">
                                            <?= esc($menu['menu_name'] ?? 'Unknown') ?> 
                                            <small class="text-muted d-block">ID: <?= $menu['id'] ?? 'N/A' ?> | URL: <?= esc($menu['menu_url'] ?? 'N/A') ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?php debug_checkbox_data($menu['id'], $permissions ?? []); ?>
                                        </td>
                                        <?php
                                        $current_permissions = $permissions[$menu['id']] ?? [];
                                        ?>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_view]" 
                                                       value="1" 
                                                       <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                <small class="d-block">
                                                    <?= isset($current_permissions['can_view']) ? $current_permissions['can_view'] : 'unset' ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_add]" 
                                                       value="1" 
                                                       <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                                <small class="d-block">
                                                    <?= isset($current_permissions['can_add']) ? $current_permissions['can_add'] : 'unset' ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_edit]" 
                                                       value="1" 
                                                       <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                <small class="d-block">
                                                    <?= isset($current_permissions['can_edit']) ? $current_permissions['can_edit'] : 'unset' ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="permissions[<?= $menu['id'] ?>][can_delete]" 
                                                       value="1" 
                                                       <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                <small class="d-block">
                                                    <?= isset($current_permissions['can_delete']) ? $current_permissions['can_delete'] : 'unset' ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <pre class="small"><?php print_r($current_permissions); ?></pre>
                                        </td>
                                    </tr>
                                    
                                    <!-- Sub-Menu -->
                                    <?php if (!empty($menu['submenus'])): ?>
                                        <?php foreach ($menu['submenus'] as $submenu): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <i class="material-icons-outlined me-2"><?= esc($submenu['menu_icon'] ?? 'subdirectory_arrow_right') ?></i>
                                                    <?= esc($submenu['menu_name'] ?? 'Unknown Submenu') ?>
                                                    <small class="text-muted d-block">
                                                        ID: <?= $submenu['id'] ?? 'N/A' ?> | URL: <?= esc($submenu['menu_url'] ?? 'N/A') ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <?php debug_checkbox_data($submenu['id'], $permissions ?? []); ?>
                                                </td>
                                                <?php
                                                $current_permissions = $permissions[$submenu['id']] ?? [];
                                                ?>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_add]" 
                                                               value="1" 
                                                               <?= isset($current_permissions['can_add']) && $current_permissions['can_add'] ? 'checked' : '' ?>>
                                                        <small class="d-block">
                                                            <?= isset($current_permissions['can_add']) ? $current_permissions['can_add'] : 'unset' ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_edit]" 
                                                               value="1" 
                                                               <?= isset($current_permissions['can_edit']) && $current_permissions['can_edit'] ? 'checked' : '' ?>>
                                                        <small class="d-block">
                                                            <?= isset($current_permissions['can_edit']) ? $current_permissions['can_edit'] : 'unset' ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $submenu['id'] ?>][can_delete]" 
                                                               value="1" 
                                                               <?= isset($current_permissions['can_delete']) && $current_permissions['can_delete'] ? 'checked' : '' ?>>
                                                        <small class="d-block">
                                                            <?= isset($current_permissions['can_delete']) ? $current_permissions['can_delete'] : 'unset' ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <pre class="small"><?php print_r($current_permissions); ?></pre>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada sub-menu.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-danger">
                ❌ Tidak ada data menu yang ditemukan! 
                <br><br>
                <strong>Kemungkinan penyebab:</strong>
                <ul>
                    <li>Tabel 'menus' kosong atau tidak ada</li>
                    <li>Method buildMenuTreeForPermissions() tidak berjalan dengan benar</li>
                    <li>Koneksi database bermasalah</li>
                    <li>Variable $menus tidak dikirim dari controller</li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex gap-2 justify-content-start">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAllPermissions()">
                                <i class="material-icons-outlined">check_box</i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="uncheckAllPermissions()">
                                <i class="material-icons-outlined">check_box_outline_blank</i> Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-light me-2">
                            <i class="material-icons-outlined">arrow_back</i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function checkAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    console.log('✅ All checkboxes checked');
}

function uncheckAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    console.log('❌ All checkboxes unchecked');
}

// Debug JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DEBUG: DOM loaded');
    
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    console.log('📋 Found checkboxes:', checkboxes.length);
    
    checkboxes.forEach((checkbox, index) => {
        console.log(`Checkbox ${index}:`, {
            name: checkbox.name,
            value: checkbox.value,
            checked: checkbox.checked
        });
    });
    
    // Log form data when submitting
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('📤 Form submitted');
            const formData = new FormData(form);
            console.log('📋 Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
        });
    }
});
</script>

<?= $this->endSection() ?>enu['id'] ?>][can_view]" 
                                                               value="1" 
                                                               <?= isset($current_permissions['can_view']) && $current_permissions['can_view'] ? 'checked' : '' ?>>
                                                        <small class="d-block">
                                                            <?= isset($current_permissions['can_view']) ? $current_permissions['can_view'] : 'unset' ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[<?= $subm