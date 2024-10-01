<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Logo/mja-logo.png">
    <?php include('../header.php'); ?>
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/admin-elements.css" />
    <style>
        .profile-icon {
            font-size: 80px;
            color: #5a5a5a;
        }
        .admin-modal-body {
            text-align: center;
            background-color: #281313;
            color: white;
            padding: 20px;
        }
        .admin-modal-body .admin-name {
            font-size: 24px;
            font-weight: bold;
        }
        .admin-modal-body .admin-email {
            font-size: 18px;
        }
        .admin-modal-body .admin-info {
            font-size: 16px;
        }
        #cropImageBtn.btn-success, #cropImageBtn.btn-success:hover {
            background-color: #28a745;
            border-color: #28a745;
        }
        #cropImageBtn.btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .classTitle {
            margin-top: 5%;
            padding-top: 2%;
        }
        @media (max-width: 767px) { 
            .classTitle {
                margin-top: 15%;
                padding-top: 6%;
            }
        }
    </style>
</head>
<body>
    <?php 
    session_start(); 
    include('../db_conn.php');
    include('navigation.php'); 
    ?>

    <p class="classTitle fs-4 fw-bold ms-4 mb-2">Staff List</p>
    <div class="mx-auto mb-4" style="height: 2px; background-color: #facc15; width:95%;"></div>
    
    <div class="container bg-white rounded border border-dark mb-5" style="width: 95%;">
        <!-- Message Container -->
        <div id="messageContainer">
            <?php if (isset($_SESSION['message'])): ?>
                <div class='alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show mt-3' role='alert'>
                    <?= $_SESSION['message']; ?>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                <?php 
                // Clear the message after displaying
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
        </div>


        <div class="buttonContainer">
            <button id="addNewBtn" class="btn custom-dark-brown-btn mt-3 mb-3 float-end" data-bs-toggle="modal" data-bs-target="#addNewStaffModal"><i class="bi bi-plus-lg me-1"></i>Add New</button>
        </div>
        <hr class="bg-dark" style="border-width: 1px; margin-top: 70px; margin-bottom: 20px;">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Show <select class="form-select d-inline w-auto" name="entries" id="entriesSelect">
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select> entries</label>
            </div>
            <div class="col-md-6 text-end">
                <label>Search: <input type="search" class="form-control d-inline w-auto" id="tableSearch"></label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered custom-table-border">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center" id="tableBody">
                    <?php
                    $query = "SELECT staff_id, lastName, firstName, middleName, email, avatar, staff_role, created_at FROM staff_account";

                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        die("Query failed: " . mysqli_error($conn));
                    }

                    $index = 1;
                    while ($staff = mysqli_fetch_assoc($result)) {
                        $middleInitial = !empty($staff['middleName']) ? strtoupper($staff['middleName'][0]) . '.' : '';
                        $fullName = htmlspecialchars($staff['firstName']) . ' ' . $middleInitial . ' ' . htmlspecialchars($staff['lastName']);
                        $firstName=htmlspecialchars($staff['firstName']);
                        $middleName=htmlspecialchars($staff['middleName']);
                        $lastName=htmlspecialchars($staff['lastName']);

                        $avatarData = $staff['avatar'];
                        $avatarBase64 = !empty($avatarData) ? 'data:image/jpeg;base64,' . base64_encode($avatarData) : '';
                    ?>
                        <tr data-id="<?= $staff['staff_id']; ?>">
                            <td><?= $index++; ?></td>
                            <td><?= $fullName; ?></td>
                            <td><?= htmlspecialchars($staff['email']); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-sm view-btn" 
                                        data-id="<?= $staff['staff_id']; ?>" 
                                        data-fullname="<?= $fullName; ?>" 
                                        data-email="<?= $staff['email']; ?>" 
                                        data-avatar="<?= $avatarBase64; ?>" 
                                        data-role="<?= htmlspecialchars($staff['staff_role']); ?>"  
                                        data-created="<?= htmlspecialchars($staff['created_at']); ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewStaffModal">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                    <button class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?= $staff['staff_id']; ?>" 
                                        data-first-name="<?= $firstName; ?>"
                                        data-middle-name="<?= $middleName; ?>"
                                        data-last-name="<?= $lastName; ?>"
                                        data-email="<?= htmlspecialchars($staff['email']); ?>" 
                                        data-avatar="<?= $avatarBase64; ?>"
                                        data-role="<?= htmlspecialchars($staff['staff_role']); ?>"  
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editStaffModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $staff['staff_id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="bi bi-trash3-fill"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="container mt-3 mb-3">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <p class="mb-0" id="pageInfo">Showing 1 to <?= mysqli_num_rows($result); ?> of <?= mysqli_num_rows($result); ?> entries</p>
                    <div class="d-flex">
                        <button id="prevBtn" class="btn btn-outline-primary me-2" disabled>Previous</button>
                        <button id="nextBtn" class="btn btn-outline-primary">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add New Staff Modal -->
    <div class="modal fade" id="addNewStaffModal" tabindex="-1" aria-labelledby="addNewStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewStaffModalLabel">Add New Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNewStaffForm" method="POST" enctype="multipart/form-data" action="actions/add-staff.php">
                        <div class="mb-3">
                            <label for="staffFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="staffFirstName" name="staffFirstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffMiddleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="staffMiddleName" name="staffMiddleName">
                        </div>
                        <div class="mb-3">
                            <label for="staffLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="staffLastName" name="staffLastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffRole" class="form-label">Role</label>
                            <input type="text" class="form-control" id="staffRole" name="staffRole" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="staffEmail" name="staffEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="staffAvatar" class="form-label">Avatar (optional, must be 1x1 aspect ratio)</label>
                            <input type="file" class="form-control" id="staffAvatar" name="staffAvatar" accept="image/*">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this staff member's account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteStaffForm" method="POST" action="actions/delete-staff.php">
                        <input type="hidden" id="deleteStaffId" name="staff_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

   <!-- Staff Details Modal -->
    <div class="modal fade" id="viewStaffModal" tabindex="-1" aria-labelledby="viewStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStaffModalLabel">Staff Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="admin-modal-body">
                    <!-- Profile Icon Placeholder -->
                    <div id="staffProfileIconPlaceholder" class="profile-icon-container">
                        <img id="staffProfileImage" class="rounded-circle" alt="Profile Picture" style="width: 80px; height: 80px; object-fit: cover; display: none;" />
                        <i id="staffProfileIcon" class="bi bi-person-circle profile-icon" style="display: none;"></i>
                    </div>
                    <!-- Staff Information -->
                    <h5 class="admin-name mb-1 mt-4" id="staffName"></h5>
                    <p class="admin-email mb-2 fs-6" id="staffEmailed"></p>
                    <p class="admin-role mt-1 mb-4">
                        <strong>Staff Role: </strong><span id="staffRoled"></span>
                    </p>
                    <p class="admin-info mt-4 mb-1">
                        <strong>Account created:</strong> <span id="staffCreationDate"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Edit Staff Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="editStaffForm" enctype="multipart/form-data">
                    <input type="hidden" id="editStaffId" name="staff_id">

                    <div class="mb-3">
                        <label for="editStaffFirstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="editStaffFirstName" name="staffFirstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStaffMiddleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="editStaffMiddleName" name="staffMiddleName">
                    </div>
                    <div class="mb-3">
                        <label for="editStaffLastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="editStaffLastName" name="staffLastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStaffRole" class="form-label">Role</label>
                        <input type="text" class="form-control" id="editStaffRole" name="staffRole" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStaffEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editStaffEmail" name="staffEmail" required> <!-- Ensure the 'name' attribute matches the PHP backend -->
                    </div>
                    <div class="mb-3">
                        <label for="editStaffAvatar" class="form-label">Avatar (optional, must be 1x1 aspect ratio)</label>
                        <input type="file" class="form-control" id="editStaffAvatar" name="staffAvatar" accept="image/*">
                        <!-- Avatar preview -->
                        <div class="mb-3 mt-4 text-center">
                            <img id="editStaffAvatarPreview" src="" alt="Avatar Preview" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>

                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageContainer = document.createElement('div');
            const messageText = document.createElement('p');
            const messageType = '<?= $_SESSION['message_type'] ?? ''; ?>';
            const message = '<?= $_SESSION['message'] ?? ''; ?>';

            if (message) {
                // Append message to body
                messageContainer.classList.add('alert', `alert-${messageType}`, 'alert-dismissible', 'fade', 'show', 'mt-3');
                messageContainer.setAttribute('role', 'alert');
                messageText.textContent = message;
                messageContainer.appendChild(messageText);

                // Create close button
                const closeButton = document.createElement('button');
                closeButton.setAttribute('type', 'button');
                closeButton.classList.add('btn-close');
                closeButton.setAttribute('data-bs-dismiss', 'alert');
                closeButton.setAttribute('aria-label', 'Close');
                messageContainer.appendChild(closeButton);

                // Add message container to the top of the page (after the navigation)
                const bodyElement = document.querySelector('body');
                const navElement = document.querySelector('nav');
                bodyElement.insertBefore(messageContainer, navElement.nextSibling);

                // Hide the message after 5 seconds
                setTimeout(() => {
                    messageContainer.remove();
                }, 5000);
            }

            const searchBox = document.getElementById('tableSearch');
            const tableBody = document.getElementById('tableBody');
            let noResultRow = document.getElementById('noResultRow');

            // Check if the "No result" row exists and remove it if necessary
            if (!noResultRow) {
                noResultRow = document.createElement('tr');
                noResultRow.id = 'noResultRow';
                noResultRow.innerHTML = '<td colspan="4">No result.</td>';
                noResultRow.style.display = 'none';
                tableBody.appendChild(noResultRow);
            }

            searchBox.addEventListener('keyup', function () {
                let searchQuery = searchBox.value.toLowerCase();
                let tableRows = document.querySelectorAll('#tableBody tr:not(#noResultRow)');
                let visibleRows = 0;

                tableRows.forEach(function (row) {
                    let fullName = row.cells[1].textContent.toLowerCase();
                    let email = row.cells[2].textContent.toLowerCase();
                    if (fullName.includes(searchQuery) || email.includes(searchQuery)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // If no visible rows, display the "No result" row
                if (visibleRows > 0) {
                    noResultRow.style.display = 'none';
                } else {
                    noResultRow.style.display = '';
                }
            });

            // Attach click event to all delete buttons
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const staffId = this.getAttribute('data-id');
                    document.getElementById('deleteStaffId').value = staffId;  // Pass staff ID to hidden input in the form
                });
            });

            // Handle pagination
            const entriesSelect = document.getElementById('entriesSelect');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');

            let currentPage = 1;
            let rowsPerPage = parseInt(entriesSelect.value);
            let totalRows = tableBody.querySelectorAll('tr').length - 1; // Subtract 1 to exclude "No result" row

            // Initialize pagination
            paginateTable();

            entriesSelect.addEventListener('change', function () {
                rowsPerPage = parseInt(this.value);
                currentPage = 1; // Reset to the first page
                paginateTable();
            });

            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    paginateTable();
                }
            });

            nextBtn.addEventListener('click', function () {
                if (currentPage < Math.ceil(totalRows / rowsPerPage)) {
                    currentPage++;
                    paginateTable();
                }
            });

            function paginateTable() {
                const rows = tableBody.querySelectorAll('tr:not(#noResultRow)');
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    if (index >= start && index < end) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                pageInfo.textContent = `Showing ${Math.min(start + 1, totalRows)} to ${Math.min(end, totalRows)} of ${totalRows} entries`;

                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage === Math.ceil(totalRows / rowsPerPage);
            }

            // View Modal setup
            document.querySelectorAll('.view-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    // Clear previous modal content
                    document.getElementById('staffRoled').textContent = '';
                    document.getElementById('staffName').textContent = '';
                    document.getElementById('staffEmailed').textContent = '';
                    document.getElementById('staffCreationDate').textContent = '';

                    // Fetch data from the clicked row
                    const fullName = this.getAttribute('data-fullname');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');
                    const creationDateRaw = this.getAttribute('data-created');
                    const avatar = this.getAttribute('data-avatar');

                    const creationDate = new Date(creationDateRaw);
                    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
                    const formattedDate = creationDate.toLocaleDateString('en-US', options);

                    document.getElementById('staffName').textContent = fullName;
                    document.getElementById('staffEmailed').textContent = email;
                    document.getElementById('staffRoled').textContent = role;
                    document.getElementById('staffCreationDate').textContent = formattedDate;

                    const profileIconPlaceholder = document.getElementById('staffProfileIconPlaceholder');
                    if (avatar && avatar !== '') {
                        profileIconPlaceholder.innerHTML = `<img src="${avatar}" alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
                    } else {
                        profileIconPlaceholder.innerHTML = `<i class="bi bi-person-circle profile-icon text-white"></i>`;
                    }

                    const modal = new bootstrap.Modal(document.getElementById('viewStaffModal'));
                    modal.show();
                });
            });

            // Ensure the modal closes completely and backdrop is removed
            document.getElementById('viewStaffModal').addEventListener('hidden.bs.modal', function () {
                document.body.classList.remove('modal-open');
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.style.overflow = 'auto'; // Restore scrolling
            });

            // Edit Modal setup
            document.querySelectorAll('.edit-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const staffId = this.getAttribute('data-id');
                    const firstName = this.getAttribute('data-first-name');
                    const middleName = this.getAttribute('data-middle-name');
                    const lastName = this.getAttribute('data-last-name');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');
                    const avatar = this.getAttribute('data-avatar');

                    document.getElementById('editStaffId').value = staffId;
                    document.getElementById('editStaffFirstName').value = firstName;
                    document.getElementById('editStaffMiddleName').value = middleName;
                    document.getElementById('editStaffLastName').value = lastName;
                    document.getElementById('editStaffEmail').value = email;
                    document.getElementById('editStaffRole').value = role;

                    const avatarPreview = document.getElementById('editStaffAvatarPreview');
                    if (avatar && avatar !== '') {
                        avatarPreview.src = avatar;
                    } else {
                        avatarPreview.src = '../Logo/img/default-profile.png';  // Default avatar icon
                    }
                });
            });

            // Image preview when a new avatar is uploaded
            document.getElementById('editStaffAvatar').addEventListener('change', function (event) {
                const [file] = event.target.files;
                if (file) {
                    document.getElementById('editStaffAvatarPreview').src = URL.createObjectURL(file);
                }
            });

            // Handle the edit form submission using AJAX
            document.getElementById('editStaffForm').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(this);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'actions/edit-staff.php', true); // Send the form data via POST to edit-staff.php

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // Hide the modal after success
                                const modalElement = document.getElementById('editStaffModal');
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }

                                // Update the table dynamically without page reload
                                const staffRow = document.querySelector(`tr[data-id="${formData.get('staff_id')}"]`);
                                const middleName = formData.get('staffMiddleName');
                                const middleInitial = middleName ? `${middleName[0]}.` : '';
                                const updatedFullName = `${formData.get('staffFirstName')} ${middleInitial} ${formData.get('staffLastName')}`;

                                // Update row data
                                staffRow.cells[1].textContent = updatedFullName;
                                staffRow.cells[2].textContent = formData.get('staffEmail');

                                // Get the buttons in the row
                                const editButton = staffRow.querySelector('.edit-btn');
                                const viewButton = staffRow.querySelector('.view-btn');

                                // Update data attributes on the edit button
                                editButton.setAttribute('data-first-name', formData.get('staffFirstName'));
                                editButton.setAttribute('data-middle-name', formData.get('staffMiddleName'));
                                editButton.setAttribute('data-last-name', formData.get('staffLastName'));
                                editButton.setAttribute('data-email', formData.get('staffEmail'));
                                editButton.setAttribute('data-role', formData.get('staffRole'));

                                // Update data attributes on the view button
                                viewButton.setAttribute('data-fullname', updatedFullName);
                                viewButton.setAttribute('data-email', formData.get('staffEmail'));
                                viewButton.setAttribute('data-role', formData.get('staffRole'));

                                // Optionally handle avatar preview
                                const avatarFile = document.getElementById('editStaffAvatar').files[0];
                                if (avatarFile) {
                                    const reader = new FileReader();
                                    reader.onloadend = function() {
                                        const avatarBase64 = reader.result;
                                        editButton.setAttribute('data-avatar', avatarBase64);
                                        viewButton.setAttribute('data-avatar', avatarBase64);
                                    };
                                    reader.readAsDataURL(avatarFile);
                                }

                                // Display a success message
                                displayMessage('success', response.message);
                            } else {
                                // Handle the error case
                                displayMessage('danger', response.message || 'An error occurred while updating staff details.');
                            }
                        } catch (e) {
                            displayMessage('danger', 'Invalid server response.');
                        }
                    } else {
                        displayMessage('danger', 'An error occurred while updating staff details.');
                    }
                };

                xhr.send(formData); // Send the FormData object via AJAX
            });

            function displayMessage(type, message) {
                const messageContainer = document.getElementById('messageContainer');
                if (messageContainer) {
                    // Clear any existing messages
                    messageContainer.innerHTML = '';

                    // Create a new alert div
                    const alertDiv = document.createElement('div');
                    alertDiv.classList.add('alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show', 'mt-3');
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.innerHTML = `
                        ${message}
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    `;

                    // Append the alert to the message container
                    messageContainer.appendChild(alertDiv);

                    // Optionally, remove the message after 5 seconds
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 5000);
                }
            }

        });
    </script>



</body>
</html>
