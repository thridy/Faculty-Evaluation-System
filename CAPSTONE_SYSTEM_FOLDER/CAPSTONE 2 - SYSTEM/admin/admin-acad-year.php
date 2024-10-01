<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Logo/mja-logo.png">
    
    <?php include('../header.php'); ?>
    <!--css file-->
    <link rel="stylesheet" href="../css/sidebar.css" />
    <link rel="stylesheet" href="../css/admin-elements.css" />
</head>
<body>
    <?php session_start(); ?>
    <?php include('navigation.php'); ?>
    
    <style>
        /* for the titles  */
        .classTitle {
            margin-top: 5%;
            padding-top: 2%;
        }

        /* small devices */
        @media (max-width: 767px) { 
            .classTitle {
                margin-top: 15%;
                padding-top: 6%;
            }
        }
    </style>
    <!-- CONTENTS HERE  -->
    <p class="classTitle fs-4 fw-bold ms-4 mb-2">Academic Year</p>
    <div class="mx-auto mb-4" style="height: 2px; background-color: #facc15; width:95%;"></div> <!-- YELLOW LINE -->

    <div class="container bg-white rounded border border-dark mb-5" style="width: 95%;">
        <!-- Display Bootstrap alert if message is set -->
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

        <div class="buttonContainer">
            <button class="btn custom-dark-brown-btn mt-3 mb-3 float-end" data-bs-toggle="modal" data-bs-target="#addNewModal"><i class="bi bi-plus-lg me-1"></i>Add New</button>
        </div>

        <hr class="bg-dark" style="border-width: 1px; margin-top: 70px; margin-bottom: 20px;"><!-- GRAY LINE -->

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6 d-flex align-items-center mb-3 mb-md-0">
                    <label for="entriesCount" class="form-label me-2 mb-0">Show</label>
                    <select id="entriesCount" class="form-select form-select-sm w-auto d-inline-block">
                        <option value="5">5</option>
                        <option value="10">10</option>
                    </select>
                    <span class="ms-2 mb-0">entries</span>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-md-end justify-content-center">
                    <p class="me-2 mb-0">Search:</p>
                    <input type="Search" id="searchInput" class="form-control form-control-sm w-auto" placeholder="">
                </div>
            </div>
        </div>

        <div class="table-responsive" style="margin-top:25px;">
            <table class="table table-striped table-hover table-bordered custom-table-border">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Quarter</th>
                        <th>Evaluation Status</th>
                        <th>Evaluation Period</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                <tbody class="text-center" id="tableBody">
                    <?php
                    // Define the query to fetch data
                    $query = "SELECT acad_year_id, year, quarter, is_active, evaluation_period 
                            FROM academic_year 
                            ORDER BY CAST(SUBSTRING_INDEX(year, '-', 1) AS UNSIGNED) DESC, quarter DESC";

                    // Execute the query
                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        die("Query failed: " . mysqli_error($conn));
                    }

                    $index = 1; // Initialize index for rows
                    if (mysqli_num_rows($result) > 0) {
                        // Fetch the data and display in the table
                        while ($subject = mysqli_fetch_assoc($result)) {
                            // Prepare variables for evaluation period check
                            $evaluationPeriod = isset($subject['evaluation_period']) ? new DateTime($subject['evaluation_period']) : null;
                            $evaluationPeriodFormatted = $evaluationPeriod ? $evaluationPeriod->format('Y-m-d\TH:i') : '';
                            $current_time = new DateTime(); // Get the current time
                            $evaluationMessage = '';

                            // Determine if the evaluation is open or ended
                            if ($evaluationPeriod) {
                                if ($evaluationPeriod < $current_time) {
                                    $evaluationMessage = "Evaluation ended at " . $evaluationPeriod->format('F d, Y - h:i A');
                                } else {
                                    $evaluationMessage = "Evaluation is open until " . $evaluationPeriod->format('F d, Y - h:i A');
                                }
                            } else {
                                $evaluationMessage = 'N/A'; // Handle if there's no evaluation period
                            }
                    ?>
                        <tr data-id="<?php echo $subject['acad_year_id']; ?>">
                            <td><?php echo $index++; ?></td> <!-- Increment index for each row -->
                            <td><?php echo htmlspecialchars($subject['year']); ?></td>
                            <td><?php echo htmlspecialchars($subject['quarter']); ?></td>
                            <td>
                                <?php 
                                if ($subject['is_active']) {
                                    echo '<span class="badge bg-success text-white">In Progress</span>';
                                } else {
                                    echo '<span class="badge bg-danger text-white">Closed</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo $evaluationMessage; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?php echo $subject['acad_year_id']; ?>" 
                                        data-year="<?php echo htmlspecialchars($subject['year']); ?>" 
                                        data-quarter="<?php echo htmlspecialchars($subject['quarter']); ?>" 
                                        data-status="<?php echo $subject['is_active'] ? 'In Progress' : 'Closed'; ?>"
                                        data-evaluation-period="<?php echo $evaluationPeriodFormatted; ?>" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm delete-btn" 
                                            data-id="<?php echo $subject['acad_year_id']; ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteConfirmationModal">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr id="noResultRow">
                            <td colspan="6">No result.</td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>


            </table>
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
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewModalLabel">New Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding new academic year -->
                    <form id="addAcademicForm" method="POST" action="actions/add-academic.php">
                        <div class="mb-3">
                            <label for="acadYear" class="form-label">Academic Year</label>
                            <input type="text" class="form-control" id="acadYear" name="acadYear" placeholder="2024-2025" required>
                        </div>
                        <div class="mb-3">
                            <label for="quarter" class="form-label">Quarter</label>
                            <input type="number" class="form-control" id="quarter" name="quarter" min="1" max="4" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="" selected hidden>Select Status</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>

                        <!-- New field for evaluation period -->
                        <div class="mb-3">
                            <label for="evaluationPeriod" class="form-label">Evaluation Period</label>
                            <input type="datetime-local" class="form-control" id="evaluationPeriod" name="evaluationPeriod" required>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary float-end">Add Academic Year</button>
                            <button type="button" class="btn btn-secondary float-end me-3" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

             
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for editing academic year -->
                    <form id="editAcademicForm" method="POST">
                        <input type="hidden" id="editAcadYearId" name="acadYearId">
                        <div class="mb-3">
                            <label for="editAcadYear" class="form-label">Academic Year</label>
                            <input type="text" class="form-control" id="editAcadYear" name="acadYear" required>
                        </div>
                        <div class="mb-3">
                            <label for="editQuarter" class="form-label">Quarter</label>
                            <input type="number" class="form-control" id="editQuarter" name="quarter" min="1" max="4" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-control" id="editStatus" name="status" required>
                                <option value="" selected hidden>Select Status</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEvaluationPeriod" class="form-label">Evaluation Period</label>
                            <input type="datetime-local" class="form-control" id="editEvaluationPeriod" name="evaluationPeriod" required>
                        </div>
                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary float-end" id="saveChangesBtn">Save Changes</button>
                            <button type="button" class="btn btn-secondary float-end me-3" data-bs-dismiss="modal">Close</button>
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
                    Are you sure you want to delete this academic year?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    
    <?php include('footer.php'); ?>

    <script>
        const academicYearInput = document.getElementById('acadYear');

        // Event listener to prevent non-numeric characters except for "-"
        academicYearInput.addEventListener('input', function (e) {
            const value = e.target.value;
            e.target.value = value.replace(/[^0-9\-]/g, '');
        });

        // Validate the format YYYY-YYYY on blur
        academicYearInput.addEventListener('blur', function () {
            const value = academicYearInput.value;
            const regex = /^\d{4}-\d{4}$/;

            if (!regex.test(value)) {
                alert("Please enter the academic year in the format YYYY-YYYY.");
                academicYearInput.value = '';
            }
        });

        // Client-side validation for the quarter field
        document.getElementById('addAcademicForm').addEventListener('submit', function (e) {
            const quarter = document.getElementById('quarter').value;
            if (quarter < 1 || quarter > 4) {
                e.preventDefault();
                alert("Quarter must be between 1 and 4.");
            }
        });

        const quarterInput = document.getElementById('quarter');
        quarterInput.addEventListener('input', function (e) {
            let value = e.target.value;
            if (isNaN(value) || value < 1 || value > 4) {
                e.target.value = ''; 
                alert("Please enter a number between 1 and 4.");
            }
        });

        quarterInput.addEventListener('keydown', function (e) {
            const invalidChars = ["-", "+", "e", "E", "."];
            const allowedControlKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
            if (!allowedControlKeys.includes(e.key) && invalidChars.includes(e.key)) {
                e.preventDefault();
            }
        });

        document.getElementById('searchInput').addEventListener('keyup', function () {
            var input = this.value.toLowerCase();
            var tableBody = document.getElementById('tableBody');
            var rows = tableBody.getElementsByTagName('tr');
            var visibleRowIndex = 1;
            var anyVisible = false;

            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var match = false;

                for (var j = 1; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }

                if (match) {
                    rows[i].style.display = ''; 
                    rows[i].getElementsByTagName('td')[0].textContent = visibleRowIndex++;
                    anyVisible = true;
                } else {
                    rows[i].style.display = 'none';
                }
            }

            var noResultRow = document.getElementById('noResultRow');
            if (!anyVisible) {
                if (!noResultRow) {
                    var newRow = document.createElement('tr');
                    newRow.id = 'noResultRow';
                    newRow.innerHTML = '<td colspan="6">No result.</td>';
                    tableBody.appendChild(newRow);
                } else {
                    noResultRow.style.display = '';
                }
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        });

        let currentPage = 1;
        let rowsPerPage = parseInt(document.getElementById('entriesCount').value);
        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.getElementsByTagName('tr');
        const totalRows = rows.length;
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        function displayTableRows() {
            let start = (currentPage - 1) * rowsPerPage;
            let end = start + rowsPerPage;

            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = 'none';
            }

            for (let i = start; i < end && i < totalRows; i++) {
                rows[i].style.display = '';
            }

            const showingStart = start + 1;
            const showingEnd = Math.min(end, totalRows);
            pageInfo.textContent = `Showing ${showingStart} to ${showingEnd} of ${totalRows} entries`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = end >= totalRows;
        }

        document.getElementById('entriesCount').addEventListener('change', function () {
            rowsPerPage = parseInt(this.value);
            currentPage = 1;
            displayTableRows();
        });

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                displayTableRows();
            }
        });

        nextBtn.addEventListener('click', function () {
            if ((currentPage * rowsPerPage) < totalRows) {
                currentPage++;
                displayTableRows();
            }
        });

        displayTableRows();

        let deleteId = null;
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                deleteId = this.getAttribute('data-id');
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (deleteId) {
                window.location.href = `actions/delete-academic.php?id=${deleteId}`;
            }
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                // Get the modal input elements
                const acadYearInput = document.getElementById('editAcadYear');
                const quarterInput = document.getElementById('editQuarter');
                const statusInput = document.getElementById('editStatus');
                const evaluationPeriodInput = document.getElementById('editEvaluationPeriod');
                const acadYearIdInput = document.getElementById('editAcadYearId');

                // Get the data from the clicked button
                const acadYearId = this.getAttribute('data-id');
                const year = this.getAttribute('data-year');
                const quarter = this.getAttribute('data-quarter');
                const status = this.getAttribute('data-status');
                const evaluationPeriod = this.getAttribute('data-evaluation-period');

                // Populate modal fields with the values from the selected row
                acadYearIdInput.value = acadYearId;
                acadYearInput.value = year;
                quarterInput.value = quarter;
                statusInput.value = status;

                // Populate the evaluation period with the correct formatted value
                if (evaluationPeriod) {
                    evaluationPeriodInput.value = evaluationPeriod; // Ensure it's in the correct datetime-local format
                } else {
                    evaluationPeriodInput.value = ''; // Clear if no data
                }

                // Check if the evaluation period has already ended
                const evaluationDate = new Date(evaluationPeriod);
                const currentDate = new Date();

                // If the evaluation period has ended, disable the status input
                if (evaluationDate < currentDate) {
                    statusInput.disabled = true;
                } else {
                    statusInput.disabled = false;
                }
            });
        });

        document.getElementById('editAcademicForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch('actions/edit-academic.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const acadYearId = data.updatedAcademicYear.acad_year_id;
                    const row = document.querySelector(`tr[data-id="${acadYearId}"]`);

                    if (row) {
                        // Update the changed row with the new data
                        row.querySelector('td:nth-child(2)').textContent = data.updatedAcademicYear.year;
                        row.querySelector('td:nth-child(3)').textContent = data.updatedAcademicYear.quarter;
                        row.querySelector('td:nth-child(4)').innerHTML = data.updatedAcademicYear.is_active ? 
                            '<span class="badge bg-success text-white">In Progress</span>' : 
                            '<span class="badge bg-danger text-white">Closed</span>';

                        // Get the evaluation period and check if it's in the past or future
                        const evaluationDate = new Date(data.updatedAcademicYear.evaluation_period);
                        const currentDate = new Date();

                        let evaluationPeriodMessage = '';
                        if (evaluationDate < currentDate) {
                            evaluationPeriodMessage = `Evaluation ended at ${evaluationDate.toLocaleString('en-US', { 
                                year: 'numeric', month: 'long', day: 'numeric', 
                                hour: '2-digit', minute: '2-digit', hour12: true 
                            })}`;
                        } else {
                            evaluationPeriodMessage = `Evaluation is open until ${evaluationDate.toLocaleString('en-US', { 
                                year: 'numeric', month: 'long', day: 'numeric', 
                                hour: '2-digit', minute: '2-digit', hour12: true 
                            })}`;
                        }

                        row.querySelector('td:nth-child(5)').textContent = evaluationPeriodMessage;

                        const editButton = row.querySelector('.edit-btn');
                        editButton.setAttribute('data-year', data.updatedAcademicYear.year);
                        editButton.setAttribute('data-quarter', data.updatedAcademicYear.quarter);
                        editButton.setAttribute('data-status', data.updatedAcademicYear.is_active ? 'In Progress' : 'Closed');
                        editButton.setAttribute('data-evaluation-period', data.updatedAcademicYear.evaluation_period);

                        // If the updated academic year is set to "In Progress", update other rows to "Closed"
                        if (data.updatedAcademicYear.is_active) {
                            document.querySelectorAll('tr').forEach(function (otherRow) {
                                const otherRowId = otherRow.getAttribute('data-id');
                                if (otherRowId && otherRowId !== acadYearId) {
                                    const statusCell = otherRow.querySelector('td:nth-child(4)');
                                    if (statusCell) {
                                        statusCell.innerHTML = '<span class="badge bg-danger text-white">Closed</span>';

                                        // Also update the status attribute in the edit button for that row
                                        const otherEditButton = otherRow.querySelector('.edit-btn');
                                        otherEditButton.setAttribute('data-status', 'Closed');
                                    }
                                }
                            });
                        }
                    }

                    const modalElement = document.getElementById('editModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

    </script>

</body>
</html>
