<?php
include('../db_conn.php');
if (isset($_GET['fetch_student']) && isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);  // Sanitize the input
    error_log('Fetching student with ID: ' . $student_id);  // Log student ID being fetched

    // Query to fetch the student details
    $query = "SELECT firstName, lastName, email, avatar, created_at 
              FROM student_account 
              WHERE student_id = $student_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    
        // Convert the LONGBLOB (binary image data) to base64 if avatar exists
        $avatarBase64 = '';
        if (!empty($row['avatar'])) {
            $avatarBase64 = 'data:image/png;base64,' . base64_encode($row['avatar']); // Assuming avatar is in PNG format
        }
    
        // Prepare the student data, returning base64 avatar if it exists, else null
        $student_data = [
            'success' => true,
            'fullname' => $row['firstName'] . ' ' . $row['lastName'],
            'email' => $row['email'],
            'avatar' => $avatarBase64,  // If no avatar, return empty string or null
            'created_at' => date('F j, Y, g:i A', strtotime($row['created_at']))
        ];
    
        echo json_encode($student_data);
    } else {
        error_log('Student not found: ' . $student_id);  // Log if student not found
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
    exit;  // Prevent the rest of the page from loading
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Logo/mja-logo.png">
    <title>Manage Students</title>
    <?php include('../header.php'); ?>
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/admin-elements.css" />
    <style>
        .breadcrumbs-container {
            margin-top: 5%;
            margin-bottom: 1.5%;
            padding-top: 2%;
            width: 98%;
        }
        .btn-back {
            background: none;
            border: none;
            padding: 0;
            color: #281313;
            margin-right: 35px;
        }
        .btn-back i {
            font-size: 2.5rem;
        }
        .btn-on {
            background: #281313;
            border: none;
            padding: 8px;
            color: #fafafa;
        }
        .btn-on:hover {
            background: #facc15;
            color: #281313;
        }
        .btn-back:hover {
            color: #facc15;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .suggestion-box {
            border: 1px solid #ddd;
            background: white;
            position: absolute;
            z-index: 1000;
            width: 90%;
            max-height: 150px;
            overflow-y: auto;
        }

        .suggestion-box div {
            padding: 10px;
            cursor: pointer;
        }

        .suggestion-box div:hover {
            background-color: #f1f1f1;
        }

        .modal-body {
            position: relative; /* Ensure suggestions are positioned properly */
        }
    </style>
</head>
<body>
    <?php 
        include('../db_conn.php');

        // Check if it's an AJAX request for student suggestions
        if (isset($_GET['q'])) {
            // Start with a clean buffer to prevent output of anything but JSON
            ob_clean();
            header('Content-Type: application/json');
        
            // Get the search term
            $search = mysqli_real_escape_string($conn, $_GET['q']);
            
            // Query to search for students by name
            $query = "SELECT student_id, firstName, lastName 
                      FROM student_account 
                      WHERE firstName LIKE '%$search%' OR lastName LIKE '%$search%' 
                      LIMIT 10";
        
            $result = mysqli_query($conn, $query);
        
            // Handle query error
            if (!$result) {
                echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
                exit;
            }
        
            $suggestions = [];
        
            while ($row = mysqli_fetch_assoc($result)) {
                $suggestions[] = [
                    'student_id' => $row['student_id'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName']
                ];
            }
        
            // Output the JSON response
            echo json_encode($suggestions);
            exit;
        }
       
        // If it's not an AJAX request, continue with the normal HTML page rendering
        session_start(); 
        include('navigation.php'); 

        // Get class_id, year, and quarter from the URL parameters
        $class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
        $year = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : '';
        $quarter = isset($_GET['quarter']) ? htmlspecialchars($_GET['quarter']) : '';

        if ($class_id == 0) {
            die("Invalid class ID.");
        }

        // Fetch class details
        $query = "SELECT cl.grade_level, cl.section, ay.acad_year_id, ay.year AS acad_year
                FROM class_list cl
                LEFT JOIN evaluation_list el ON cl.class_id = el.class_id
                LEFT JOIN academic_year ay ON el.acad_year_id = ay.acad_year_id
                WHERE cl.class_id = $class_id";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $class_details = mysqli_fetch_assoc($result);
        if (!$class_details) {
            die("No class found with the provided class ID.");
        }

        // Extract class details
        $grade_level = $class_details['grade_level'];
        $section = $class_details['section'];
        $acad_year_id = $class_details['acad_year_id'];
        $acad_year = $class_details['acad_year'];

        // Query to fetch students related to this class
        $student_query = "
            SELECT ser.students_eval_id, sa.student_id, CONCAT(sa.firstName, ' ', sa.lastName) AS student_name
            FROM students_eval_restriction ser
            JOIN student_account sa ON ser.student_id = sa.student_id
            JOIN evaluation_list el ON ser.evaluation_id = el.evaluation_id
            WHERE el.class_id = " . intval($class_id) . " 
            AND el.acad_year_id = " . intval($acad_year_id) . "
        ";

        $students_result = mysqli_query($conn, $student_query);

        if (!$students_result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $counter = 1;
    ?>
    
    <!-- Breadcrumbs -->
    <div class="breadcrumbs-container">
        <nav aria-label="breadcrumb" class="breadcrumbs ms-4 bg-white border rounded py-2 px-3" style="height: 40px; align-items: center; display: flex;">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="admin-dashboard.php" class="text-muted text-decoration-none">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="admin-eval-controls.php" class="text-muted text-decoration-none">
                        Evaluation Controls
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="manage-class.php?acad_year_id=<?php echo $acad_year_id; ?>&year=<?php echo $year; ?>&quarter=<?php echo $quarter; ?>" class="text-muted text-decoration-none">
                        Manage Class
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Manage Students</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 ms-4">
        <p class="classTitle fs-4 fw-bold mb-0">
            Manage Students for Grade <?php echo $grade_level . " - " . $section; ?> of A.Y. <?php echo $acad_year; ?> | Quarter <?php echo $quarter; ?>
        </p>
        <a href="manage-class.php?acad_year_id=<?php echo $acad_year_id; ?>&year=<?php echo $year; ?>&quarter=<?php echo $quarter; ?>" class="btn btn-back">
            <i class="fa-solid fa-circle-chevron-left"></i>
        </a>
    </div>

    <div class="mx-auto mb-5" style="height: 2px; background-color: #facc15; width:95%;"></div>

    <!-- Right: Add Student button -->
    <div class="d-flex justify-content-end me-4 mb-4">
        <button type="button" class="btn-on rounded me-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fa-solid fa-plus"></i> Add Student
        </button>
    </div>

    <!-- Table for Students -->
    <div class="container bg-white rounded border mb-5" style="width: 95%;">
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <!-- Left: Show entries -->
            <div class="text-start mb-2 ms-4 d-flex align-items-center">
                <label for="entriesCount" class="form-label me-2 mb-0">Show</label>
                <select id="entriesCount" class="form-select form-select-sm w-auto d-inline-block">
                    <option value="5">5</option>
                    <option value="15" selected>15</option> <!-- Default value is 15 -->
                    <option value="30">30</option>
                    <option value="50">50</option>
                </select>
                <span class="ms-2 mb-0">entries</span>
            </div>


            <!-- Search input field -->
            <div class="container mb-2">
                <div class="row justify-content-between align-items-center">
                    <div class="text-end">
                        <input type="search" id="searchInput" class="form-control w-25" placeholder="Search students..." style="width: 50%; float:right;" <?php echo (mysqli_num_rows($students_result) > 0) ? '' : 'disabled'; ?> >
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="margin-top:25px;">
            <table class="table table-striped table-hover table-bordered" id="studentsTable">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                
                <tbody class="text-center" id="studentsBody">
                    <?php
                    if (mysqli_num_rows($students_result) > 0) {
                        // Counter for student numbers
                        $counter = 1;
                        
                        // Loop through each student row
                        while ($row = mysqli_fetch_assoc($students_result)) {
                           
                    ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $counter++; ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td class="text-center align-middle">
                                <a href="#" 
                                    class="btn btn-outline-dark btn-sm view-btn" 
                                    data-bs-student-id="<?php echo $row['student_id']; ?>" 
                                    title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>


                                <a href="#" class="btn btn-outline-dark btn-sm btn-delete" data-student-id="<?php echo $row['student_id']; ?>" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    }
                    ?>
                    <!-- Row to display when no search results are found (initially hidden) -->
                    <tr id="noSearchResultsRow" style="display: none;">
                        <td colspan="3">No matching students found.</td>
                    </tr>
                </tbody>

            </table>
        </div>

        <!-- Pagination controls -->
        <div class="container mt-3 mb-3">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <p class="mb-0" id="pageInfo">Showing 1 to 3 of 3 entries</p>
                    <div class="d-flex">
                        <button id="prevBtn" class="btn btn-outline-primary me-2" disabled>
                            Previous
                        </button>
                        <button id="nextBtn" class="btn btn-outline-primary" disabled>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flash-message" id="flashMessage"></div>

    <!-- Modal for Adding Student -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student to This Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to add new student -->
                    <form id="addStudentForm" method="POST">
                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                        <input type="hidden" name="acad_year_id" value="<?php echo $acad_year_id; ?>">  <!-- Add this line -->
                        <input type="hidden" name="studentId" id="studentId">  <!-- This should be set dynamically -->
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Student Name</label>
                            <input type="text" id="studentName" name="studentName" class="form-control" autocomplete="off" required>
                            <div id="studentSuggestions" class="suggestion-box"></div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="addStudentForm" id="addStudentBtn">Add Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStudentModalLabel">Confirm the Removal of Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this student from the class?
                    <input type="hidden" id="deleteStudentEvalId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Delete Confirmation -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStudentModalLabel">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove <strong id="deleteStudentName"></strong> from this class?
                    <input type="hidden" id="deleteStudentId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">View Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center">
                    <!-- Student Avatar -->
                    <img id="studentAvatar" src="" alt="Avatar" class="rounded-circle mb-3"
                        style="width: 80px; height: 80px; object-fit: cover; display: none;">
                    
                    <!-- Default Icon (bi bi-person-circle) -->
                    <i id="defaultAvatarIcon" class="bi bi-person-circle"
                        style="font-size: 80px; color: #5a5a5a; display: none;"></i>

                    <!-- Student Full Name -->
                    <h5 class="modal-title mb-2" id="studentFullName"></h5>

                    <!-- Student Email -->
                    <p class="fs-6 mb-2"><strong>Email Address: </strong><span id="studentEmail"></span></p>

                    <!-- Account Creation Date -->
                    <p class="mt-3"><strong>Account created:</strong> <span id="studentCreatedAt"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBox = document.getElementById('searchInput');
        const tableBody = document.getElementById('studentsBody');
        const entriesSelect = document.getElementById('entriesCount');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');
        const noResultRow = document.getElementById('noSearchResultsRow');

        let rows = Array.from(tableBody.querySelectorAll('tr:not(#noSearchResultsRow)'));
        let filteredRows = rows;
        let currentPage = 1;
        let rowsPerPage = parseInt(entriesSelect.value);

        // Filter table based on search input
        function filterTable(searchQuery) {
            tableBody.innerHTML = '';
            currentPage = 1;
            filteredRows = rows.filter(row => {
                const studentName = row.cells[1].textContent.toLowerCase();
                return studentName.includes(searchQuery.toLowerCase());
            });
            if (filteredRows.length === 0) {
                noResultRow.style.display = '';
                tableBody.appendChild(noResultRow);
            } else {
                noResultRow.style.display = 'none';
                filteredRows.forEach(row => tableBody.appendChild(row));
            }
            paginateTable();
        }

        // Pagination logic
        function paginateTable() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            pageInfo.textContent = `Showing ${Math.min(start + 1, filteredRows.length)} to ${Math.min(end, filteredRows.length)} of ${filteredRows.length} entries`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = end >= filteredRows.length;
        }

        searchBox.addEventListener('keyup', function () {
            const searchQuery = searchBox.value;
            filterTable(searchQuery);
        });

        entriesSelect.addEventListener('change', function () {
            rowsPerPage = parseInt(this.value);
            paginateTable();
        });

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                paginateTable();
            }
        });

        nextBtn.addEventListener('click', function () {
            if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) {
                currentPage++;
                paginateTable();
            }
        });

        paginateTable();

        // Suggestion box for adding students
        document.getElementById('studentName').addEventListener('input', function () {
            const input = this.value;
            const suggestionBox = document.getElementById('studentSuggestions');
            clearTimeout(this.delay);

            if (input.length >= 2) {
                this.delay = setTimeout(function () {
                    fetch('manage-student.php?q=' + encodeURIComponent(input))
                        .then(response => response.json())
                        .then(jsonData => {
                            suggestionBox.innerHTML = '';
                            if (jsonData.length > 0) {
                                jsonData.forEach(student => {
                                    const div = document.createElement('div');
                                    div.textContent = `${student.firstName} ${student.lastName}`;
                                    div.addEventListener('click', function () {
                                        document.getElementById('studentName').value = div.textContent;
                                        document.getElementById('studentId').value = student.student_id;
                                        suggestionBox.innerHTML = '';
                                    });
                                    suggestionBox.appendChild(div);
                                });
                            } else {
                                suggestionBox.innerHTML = '<div>No results found.</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching student suggestions:', error);
                        });
                }, 300);
            } else {
                suggestionBox.innerHTML = '';
            }
        });

        // Add student form submission
        document.getElementById('addStudentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('actions/add-student-process.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(jsonData => {
                    if (jsonData.success) {
                        displayFlashMessage('Student added successfully!', 'success');
                        const studentName = document.getElementById('studentName').value;
                        const studentsBody = document.getElementById('studentsBody');

                        // Dynamically add new row for the student
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="text-center align-middle"></td>
                            <td class="align-middle">${studentName}</td>
                            <td class="text-center align-middle">
                                <a href="#" 
                                class="btn btn-outline-dark btn-sm view-btn" 
                                data-bs-student-id="${jsonData.student_id}" 
                                title="View">
                                <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="#" 
                                class="btn btn-outline-dark btn-sm btn-delete" 
                                data-student-id="${jsonData.student_id}" 
                                title="Delete">
                                <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        `;

                        studentsBody.appendChild(newRow);
                        displayTableRows();

                        // Clear form
                        document.getElementById('studentName').value = '';
                        document.getElementById('studentId').value = '';

                        const addStudentModalElement = document.getElementById('addStudentModal');
                        const addStudentModal = bootstrap.Modal.getInstance(addStudentModalElement);
                        addStudentModal.hide();
                    } else if (jsonData.error) {
                        displayFlashMessage(jsonData.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    displayFlashMessage('An error occurred while adding the student.', 'error');
                });
        });

        // Use Event Delegation for dynamic View/Delete buttons
        document.getElementById('studentsBody').addEventListener('click', function (e) {
            if (e.target.closest('.view-btn')) {
                e.preventDefault();
                const button = e.target.closest('.view-btn');
                const studentId = button.getAttribute('data-bs-student-id');
                viewStudent(studentId);
            } else if (e.target.closest('.btn-delete')) {
                e.preventDefault();
                const studentId = e.target.closest('.btn-delete').getAttribute('data-student-id');
                const studentName = e.target.closest('tr').querySelector('td:nth-child(2)').textContent;
                confirmDeleteStudent(studentId, studentName);
            }
        });

        // View student details
        function viewStudent(studentId) {
            fetch('manage-student.php?fetch_student=1&student_id=' + studentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('studentFullName').textContent = data.fullname;
                        document.getElementById('studentEmail').textContent = data.email;

                        const avatarImg = document.getElementById('studentAvatar');
                        const defaultIcon = document.getElementById('defaultAvatarIcon');
                        if (data.avatar) {
                            avatarImg.src = data.avatar;
                            avatarImg.style.display = 'block';
                            defaultIcon.style.display = 'none';
                        } else {
                            avatarImg.style.display = 'none';
                            defaultIcon.style.display = 'block';
                        }

                        document.getElementById('studentCreatedAt').textContent = data.created_at;
                        const viewModal = new bootstrap.Modal(document.getElementById('viewStudentModal'));
                        viewModal.show();
                    } else {
                        displayFlashMessage('Failed to fetch student details.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching student details:', error);
                });
        }

        // Confirm student deletion
        function confirmDeleteStudent(studentId, studentName) {
            document.getElementById('deleteStudentName').textContent = studentName;
            document.getElementById('deleteStudentId').value = studentId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteStudentModal'), {});
            deleteModal.show();
        }

        // Handle student deletion
        document.getElementById('confirmDeleteButton').addEventListener('click', function () {
            const studentId = document.getElementById('deleteStudentId').value;

            fetch('actions/delete-student-process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ student_id: studentId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`.btn-delete[data-student-id="${studentId}"]`).closest('tr').remove();
                        displayTableRows();
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteStudentModal'));
                        deleteModal.hide();
                        displayFlashMessage('Student removed successfully.', 'success');
                    } else {
                        displayFlashMessage('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    displayFlashMessage('An error occurred while removing the student.', 'error');
                });
        });

        // Update and display row numbers
        function displayTableRows() {
            const rows = document.querySelectorAll('#studentsBody tr');
            let count = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    row.querySelector('td:first-child').textContent = count++;
                }
            });
        }

        // Flash message display
        function displayFlashMessage(message, type = 'success') {
            let flashMessage = document.getElementById('flashMessage');
            if (!flashMessage) {
                flashMessage = document.createElement('div');
                flashMessage.id = 'flashMessage';
                flashMessage.className = 'flash-message';
                document.body.appendChild(flashMessage);
            }

            flashMessage.style.backgroundColor = type === 'error' ? '#dc3545' : '#28a745';
            flashMessage.innerText = message;
            flashMessage.style.display = 'block';
            setTimeout(() => flashMessage.style.opacity = '1', 100);
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.style.display = 'none', 500);
            }, 3000);
        }
    });
</script>


</body>
</html>
