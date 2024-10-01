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

    <p class="classTitle fs-4 fw-bold ms-4 mb-2">Teacher List</p>
    <div class="mx-auto mb-4" style="height: 2px; background-color: #facc15; width:95%;"></div>
    
    <div class="container bg-white rounded border border-dark mb-5" style="width: 95%;">
        <?php if (isset($_SESSION['message'])): ?>
            <div class='alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show mt-3' role='alert'>
                <?= $_SESSION['message']; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <div class="buttonContainer">
            <button id="addNewBtn" class="btn custom-dark-brown-btn mt-3 mb-3 float-end" data-bs-toggle="modal" data-bs-target="#addNewTeacherModal"><i class="bi bi-plus-lg me-1"></i>Add New</button>
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
                    $query = "SELECT teacher_id, lastName, firstName, middleName, email, avatar, department, created_at FROM teacher_account";
                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        die("Query failed: " . mysqli_error($conn));
                    }

                    $index = 1;
                    while ($teacher = mysqli_fetch_assoc($result)) {
                        $middleInitial = !empty($teacher['middleName']) ? strtoupper($teacher['middleName'][0]) . '.' : '';
                        $fullName = htmlspecialchars($teacher['firstName']) . ' ' . $middleInitial . ' ' . htmlspecialchars($teacher['lastName']);
                    ?>
                        <tr data-id="<?= $teacher['teacher_id']; ?>">
                            <td><?= $index++; ?></td>
                            <td><?= $fullName; ?></td>
                            <td><?= htmlspecialchars($teacher['email']); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-sm view-btn" 
                                        data-id="<?= $teacher['teacher_id']; ?>" 
                                        data-fullname="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>" 
                                        data-email="<?= htmlspecialchars($teacher['email'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        data-department="<?= htmlspecialchars($teacher['department'], ENT_QUOTES, 'UTF-8'); ?>"
                                        data-avatar="<?= !empty($teacher['avatar']) ? 'data:image/jpeg;base64,' . base64_encode($teacher['avatar']) : ''; ?>"
                                        data-created="<?= !empty($teacher['created_at']) ? htmlspecialchars($teacher['created_at'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                    <button class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?= $teacher['teacher_id']; ?>" 
                                        data-first-name="<?= htmlspecialchars($teacher['firstName']); ?>"
                                        data-middle-name="<?= htmlspecialchars($teacher['middleName']); ?>"
                                        data-last-name="<?= htmlspecialchars($teacher['lastName']); ?>"
                                        data-email="<?= htmlspecialchars($teacher['email']); ?>" 
                                        data-department="<?= htmlspecialchars($teacher['department']); ?>"
                                        data-avatar="<?= !empty($teacher['avatar']) ? 'data:image/jpeg;base64,' . base64_encode($teacher['avatar']) : '../Logo/img/default-profile.png'; ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTeacherModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $teacher['teacher_id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                    <!-- No result row placeholder -->
                    <tr id="noResultRow" style="display: none;">
                        <td colspan="4">No result.</td>
                    </tr>
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

    <!-- Add New Teacher Modal -->
    <div class="modal fade" id="addNewTeacherModal" tabindex="-1" aria-labelledby="addNewTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewTeacherModalLabel">Add New Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="addNewTeacherForm" method="POST" action="actions/add-teacher.php" enctype="multipart/form-data">
                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="teacherFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="teacherFirstName" name="teacherFirstName" required>
                        </div>
                        <!-- Middle Name -->
                        <div class="mb-3">
                            <label for="teacherMiddleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="teacherMiddleName" name="teacherMiddleName">
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="teacherLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="teacherLastName" name="teacherLastName" required>
                        </div>
                        <!-- Department -->
                        <div class="mb-3">
                            <label for="teacherDepartment" class="form-label">Department</label>
                            <select class="form-select" id="teacherDepartment" name="teacherDepartment" required>
                                <option value="">Select Department</option>
                                <option value="Junior High School">Junior High School</option>
                                <option value="Senior High School">Senior High School</option>
                                <option value="Both (JHS & SHS)">Both (JHS & SHS)</option>
                            </select>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="teacherEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="teacherEmail" name="teacherEmail" required>
                        </div>
                        <!-- Avatar -->
                        <div class="mb-3">
                            <label for="teacherAvatar" class="form-label">Avatar (optional, must be 1x1 aspect ratio)</label>
                            <input type="file" class="form-control" id="teacherAvatar" name="teacherAvatar" accept="image/*">
                        </div>
                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Teacher</button>
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
                    Are you sure you want to delete this teacher's account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteTeacherForm" method="POST" action="actions/delete-teacher.php">
                        <input type="hidden" id="deleteTeacherId" name="teacher_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Details Modal -->
    <div class="modal fade" id="viewTeacherModal" tabindex="-1" aria-labelledby="viewTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Teacher Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="admin-modal-body">
                    <!-- Profile Icon Placeholder -->
                    <div id="teacherProfileIconPlaceholder" class="profile-icon-container">
                        <img id="teacherProfileImage" class="rounded-circle" alt="Profile Picture" style="width: 80px; height: 80px; object-fit: cover; display: none;" />
                        <i id="teacherProfileIcon" class="bi bi-person-circle profile-icon" style="display: none;"></i>
                    </div>
                    <!-- Teacher Information -->
                    <h5 class="admin-name mb-1 mt-4" id="teacherName"></h5>
                    <p class="admin-email mb-2 fs-6" style="font-size: 0.9em;"><strong>Email Address: </strong><span id="teacherEmailed"></span></p>
                    <p class="admin-role mt-1 mb-4">
                        <strong>Department: </strong><span id="teacherDepartmento"></span>
                    </p>
                    <!-- Spacer for better visual separation -->
                    <div style="margin: 10px 0;"></div>
                    <p class="admin-info mt-4 mb-1">
                        <strong>Account created:</strong> <span id="teacherCreationDate"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Teacher Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeacherModalLabel">Edit Teacher Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="editTeacherForm" enctype="multipart/form-data">
                        <input type="hidden" id="editTeacherId" name="teacher_id">
                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="editTeacherFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editTeacherFirstName" name="teacherFirstName" required>
                        </div>
                        <!-- Middle Name -->
                        <div class="mb-3">
                            <label for="editTeacherMiddleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="editTeacherMiddleName" name="teacherMiddleName">
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="editTeacherLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editTeacherLastName" name="teacherLastName" required>
                        </div>
                        <!-- Department -->
                        <div class="mb-3">
                            <label for="editTeacherDepartment" class="form-label">Department</label>
                            <select class="form-select" id="editTeacherDepartment" name="teacherDepartment" required>
                                <option value="">Select Department</option>
                                <option value="Junior High School">Junior High School</option>
                                <option value="Senior High School">Senior High School</option>
                                <option value="Both (JHS & SHS)">Both (JHS & SHS)</option>
                            </select>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="editTeacherEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editTeacherEmail" name="teacherEmail" required>
                        </div>
                        <!-- Avatar -->
                        <div class="mb-3">
                            <label for="editTeacherAvatar" class="form-label">Avatar (optional, must be 1x1 aspect ratio)</label>
                            <input type="file" class="form-control" id="editTeacherAvatar" name="teacherAvatar" accept="image/*">
                            <div class="mb-3" id="editTeacherAvatarContainer">
                                <div class="text-center">
                                    <img id="editTeacherAvatarPreview" src="" alt="" class="rounded-circle mx-auto d-block mt-4" style="width: 100px; height: 100px; object-fit: cover; display: none;" />
                                </div>
                            </div>
                        </div>
                        <!-- Modal Footer -->
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
    
    <!-- JavaScript for Modal and Pagination -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchBox = document.getElementById('tableSearch');
            const tableBody = document.getElementById('tableBody');
            const entriesSelect = document.getElementById('entriesSelect');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');
            let noResultRow = document.getElementById('noResultRow');

            // Pagination Variables
            let currentPage = 1;
            let rowsPerPage = parseInt(entriesSelect.value);
            let rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultRow)'));
            let filteredRows = rows;  // Store filtered rows separately

            // Initialize total rows count
            let totalRows = rows.length;

            // Function to filter rows based on search input
            function filterTable(searchQuery) {
                filteredRows = rows.filter(function(row) {
                    let fullName = row.cells[1].textContent.toLowerCase();
                    let email = row.cells[2].textContent.toLowerCase();
                    return fullName.includes(searchQuery) || email.includes(searchQuery);
                });

                // Show only filtered rows
                rows.forEach(function(row) {
                    row.style.display = 'none';  // Hide all rows initially
                });

                filteredRows.forEach(function(row) {
                    row.style.display = '';  // Show only filtered rows
                });

                // If no rows match the search, show the "No result" row
                if (filteredRows.length === 0) {
                    noResultRow.style.display = '';
                } else {
                    noResultRow.style.display = 'none';
                }

                // Update the total visible rows and reset pagination
                totalRows = filteredRows.length;
                currentPage = 1;
                paginateTable();
            }

            // Function to paginate the visible rows
            function paginateTable() {
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.forEach(function(row, index) {
                    if (index >= start && index < end) {
                        row.style.display = '';  // Show the rows within the page range
                    } else {
                        row.style.display = 'none';  // Hide the rows outside the page range
                    }
                });

                pageInfo.textContent = `Showing ${Math.min(start + 1, totalRows)} to ${Math.min(end, totalRows)} of ${totalRows} entries`;

                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage >= Math.ceil(totalRows / rowsPerPage);
            }

            // Attach pagination to the number of entries selected
            entriesSelect.addEventListener('change', function () {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
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

            // Search functionality
            searchBox.addEventListener('keyup', function () {
                const searchQuery = searchBox.value.toLowerCase();
                filterTable(searchQuery);
            });

            // Initialize Pagination
            paginateTable();
        });

        //  Click event to all delete buttons
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const teacherId = this.getAttribute('data-id');
                document.getElementById('deleteTeacherId').value = teacherId;  // Pass teacher ID to hidden input in the form
            });
        });

        // JavaScript to handle the "View" button clicks
        document.querySelectorAll('.view-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                // Clear previous modal content
                document.getElementById('teacherName').textContent = '';
                document.getElementById('teacherEmailed').textContent = '';
                document.getElementById('teacherDepartmento').textContent = '';
                document.getElementById('teacherCreationDate').textContent = '';

                // Fetch data from the clicked button
                const fullName = this.getAttribute('data-fullname');
                const email = this.getAttribute('data-email');
                const department = this.getAttribute('data-department');
                const avatar = this.getAttribute('data-avatar') || '';
                const creationDateRaw = this.getAttribute('data-created') || '';
                
                // Format the creation date
                let formattedDate = 'N/A';
                if (creationDateRaw) {
                    const creationDate = new Date(creationDateRaw);
                    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
                    formattedDate = creationDate.toLocaleDateString('en-US', options);
                }

                // Set modal content
                document.getElementById('teacherName').textContent = fullName;
                document.getElementById('teacherEmailed').textContent = email;
                document.getElementById('teacherDepartmento').textContent = department;
                document.getElementById('teacherCreationDate').textContent = formattedDate;

                // Handle avatar/profile picture
                const profileIconPlaceholder = document.getElementById('teacherProfileIconPlaceholder');
                if (avatar) {
                    // Display the avatar image if available
                    profileIconPlaceholder.innerHTML = `<img src="${avatar}" alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
                } else {
                    // Display default icon if no avatar is available
                    profileIconPlaceholder.innerHTML = `<i class="bi bi-person-circle profile-icon text-white"></i>`;
                }

                // Show the modal after setting content
                const modal = new bootstrap.Modal(document.getElementById('viewTeacherModal'));
                modal.show();
            });
        });

        // Clean up modal and backdrop after closing
        document.getElementById('viewTeacherModal').addEventListener('hidden.bs.modal', function () {
            // Ensure that the modal and backdrop are removed after closing
            document.body.classList.remove('modal-open');
            
            // Remove modal backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Restore scrolling on the main body content
            document.body.style.overflow = 'auto';
        });

        // JavaScript to handle the "Edit" button clicks
        document.querySelectorAll('.edit-btn').forEach(function(button) {
            button.addEventListener('click', function () {
                const teacherId = this.getAttribute('data-id');
                const firstName = this.getAttribute('data-first-name');
                const middleName = this.getAttribute('data-middle-name');
                const lastName = this.getAttribute('data-last-name');
                const email = this.getAttribute('data-email');
                const department = this.getAttribute('data-department');
                const avatar = this.getAttribute('data-avatar');

                document.getElementById('editTeacherId').value = teacherId;
                document.getElementById('editTeacherFirstName').value = firstName;
                document.getElementById('editTeacherMiddleName').value = middleName;
                document.getElementById('editTeacherLastName').value = lastName;
                document.getElementById('editTeacherEmail').value = email;
                document.getElementById('editTeacherDepartment').value = department;

                const avatarPreview = document.getElementById('editTeacherAvatarPreview');

                // Set the avatar image to either the teacher's avatar or the default image
                avatarPreview.src = avatar;
                avatarPreview.alt = 'Teacher Avatar';
                avatarPreview.style.display = 'block';  // Show the image
            });
        });

        document.getElementById('editTeacherAvatar').addEventListener('change', function (event) {
            const [file] = event.target.files;
            const avatarPreview = document.getElementById('editTeacherAvatarPreview');

            if (file) {
                avatarPreview.src = URL.createObjectURL(file);
                avatarPreview.alt = 'Teacher Avatar';
                avatarPreview.style.display = 'block';
            } else {
                // If the file input is cleared, reset to the default image
                avatarPreview.src = '../Logo/img/default-profile.png';
                avatarPreview.alt = 'Default Avatar';
                avatarPreview.style.display = 'block';
            }
        });


        document.getElementById('editTeacherForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'actions/edit-teacher.php', true); // POST request to edit-teacher.php

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Hide the edit modal after success
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editTeacherModal'));
                        modal.hide();

                        // Update the row dynamically without page reload
                        const teacherRow = document.querySelector(`tr[data-id="${formData.get('teacher_id')}"]`);
                        const updatedFullName = `${formData.get('teacherFirstName')} ${formData.get('teacherMiddleName')[0]}. ${formData.get('teacherLastName')}`;

                        // Update the table row
                        teacherRow.cells[1].textContent = updatedFullName;
                        teacherRow.cells[2].textContent = formData.get('teacherEmail');

                        // Optionally update avatar
                        const avatarFile = document.getElementById('editTeacherAvatar').files[0];
                        if (avatarFile) {
                            const avatarUrl = URL.createObjectURL(avatarFile);
                            teacherRow.querySelector('.view-btn').setAttribute('data-avatar', avatarUrl);
                            teacherRow.querySelector('.edit-btn').setAttribute('data-avatar', avatarUrl);
                        }

                        // Update button attributes with new values
                        const editButton = teacherRow.querySelector('.edit-btn');
                        editButton.setAttribute('data-first-name', formData.get('teacherFirstName'));
                        editButton.setAttribute('data-middle-name', formData.get('teacherMiddleName'));
                        editButton.setAttribute('data-last-name', formData.get('teacherLastName'));
                        editButton.setAttribute('data-email', formData.get('teacherEmail'));
                        editButton.setAttribute('data-department', formData.get('teacherDepartment'));

                        // Update the view modal's fields asynchronously
                        const viewButton = teacherRow.querySelector('.view-btn');
                        viewButton.setAttribute('data-fullname', updatedFullName);
                        viewButton.setAttribute('data-email', formData.get('teacherEmail'));
                        viewButton.setAttribute('data-department', formData.get('teacherDepartment'));

                        if (avatarFile) {
                            viewButton.setAttribute('data-avatar', avatarUrl);
                        }

                        // Update the view modal if it's currently open
                        const viewModal = document.getElementById('viewTeacherModal');
                        if (viewModal.classList.contains('show')) {
                            document.getElementById('teacherName').textContent = updatedFullName;
                            document.getElementById('teacherEmailed').textContent = formData.get('teacherEmail');
                            document.getElementById('teacherDepartmento').textContent = formData.get('teacherDepartment');

                            if (avatarFile) {
                                document.getElementById('teacherProfileIconPlaceholder').innerHTML = `<img src="${avatarUrl}" alt="Profile Picture" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">`;
                            }
                        }

                        // Show the success message (similar to adding or deleting)
                        displaySuccessMessage(response.message);
                    } else {
                        displayErrorMessage(response.message || 'An error occurred while updating teacher details.');
                    }
                } else {
                    displayErrorMessage('An error occurred while updating teacher details.');
                }
            };

            xhr.send(formData); // Send the FormData object via AJAX
        });

        // Functions to display success and error messages
        function displaySuccessMessage(message) {
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('alert', 'alert-success', 'alert-dismissible', 'fade', 'show', 'mt-3');
            messageContainer.setAttribute('role', 'alert');
            messageContainer.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            const container = document.querySelector('.container');
            container.insertBefore(messageContainer, container.firstChild);

            // Auto-hide the message after 5 seconds
            setTimeout(() => {
                if (messageContainer) {
                    messageContainer.remove();
                }
            }, 5000);
        }

        function displayErrorMessage(message) {
            const messageContainer = document.createElement('div');
            messageContainer.classList.add('alert', 'alert-danger', 'alert-dismissible', 'fade', 'show', 'mt-3');
            messageContainer.setAttribute('role', 'alert');
            messageContainer.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            const container = document.querySelector('.container');
            container.insertBefore(messageContainer, container.firstChild);

            // Auto-hide the message after 5 seconds
            setTimeout(() => {
                if (messageContainer) {
                    messageContainer.remove();
                }
            }, 5000);
        }

    </script>

</body>
</html>
