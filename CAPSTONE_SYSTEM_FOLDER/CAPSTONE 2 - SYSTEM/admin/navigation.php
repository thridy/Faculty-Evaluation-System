<div class="sidebar close">
  <ul class="nav-list" style="margin-top: 30px;">
    <li>
      <a href="admin-dashboard.php" >
        <i class="bi bi-house"></i>
        <span class="link-name">Dashboard</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-dashboard.php" class="link-name" style="font-size:12px;">Dashboard</a></li>
      </ul>
    </li>

    <li>
      <a href="admin-eval-controls.php" >
        <i class="bi bi-gear"></i>
        <span class="link-name">Evaluation Controls</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-eval-controls.php" class="link-name" style="font-size:12px;">Evaluation Controls</a></li>
      </ul>
    </li>

    <li>
      <div class="icon-link">
        <a href="#">
          <i class="bi bi-people"></i> 
          <span class="link-name">Users Account</span>
        </a>
        <i class="bi bi-caret-down arrow"></i>
      </div>

      <ul class="sub-menu">
        <li><a class="link-name" style="font-size:12px;">Users Account</a></li>
        <li><a href="admin-list.php" style="font-size:12px;">Admin List</a></li>
        <li><a href="teacher-list.php" style="font-size:12px;">Teacher List</a></li>
        <li><a href="student-list.php" style="font-size:12px;">Student List</a></li>
        <li><a href="staff-list.php" style="font-size:12px;">Staff List</a></li>
      </ul>
    </li>

    <li>
      <div class="icon-link">
        <a href="#">
          <i class="bi bi-alarm"></i>
          <span class="link-name">Daily Time Record</span>
        </a>
        <i class="bi bi-caret-down arrow"></i>
      </div>

      <ul class="sub-menu">
        <li><a href="#" class="link-name" style="font-size:12px;">Daily Time Record</a></li>
        <li><a href="#" style="font-size:12px;">Faculty DTR</a></li>
        <li><a href="#" style="font-size:12px;">Staff DTR</a></li>
      </ul>
    </li>

    <li>
      <a href="admin-acad-year.php">
        <i class="bi bi-calendar-event"></i>
        <span class="link-name">Academic Year</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-acad-year.php" class="link-name" style="font-size:12px;">Academic Year</a></li>
      </ul>
    </li>

    <li>
      <a href="admin-classes.php">
        <i class="bi bi-book"></i>
        <span class="link-name">Classes</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-classes.php" class="link-name" style="font-size:12px;">Classes</a></li>
      </ul>
    </li>

    <li >
      <a href="admin-subjects.php">
        <i class="bi bi-card-list"></i>
        <span class="link-name">Subjects</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-subjects.php" class="link-name" style="font-size:12px;">Subjects</a></li>
      </ul>
    </li>

    <li>
      <a href="admin-questions.php">
        <i class="bi bi-ui-checks"></i>
        <span class="link-name">Questionnaires</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-questions.php" class="link-name" style="font-size:12px;">Questionnaires</a></li>
      </ul>
    </li>

    <li>
      <a href="admin-criteria.php">
        <i class="bi bi-funnel"></i>
        <span class="link-name">Evaluation Criteria</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="admin-criteria.php" class="link-name" style="font-size:12px;">Evaluation Criteria</a></li>
      </ul>
    </li>

    <li>
      <a href="#">
        <i class="bi bi-file-ruled"></i>
        <span class="link-name">Evaluation Report</span>
      </a>

      <ul class="sub-menu blank">
        <li><a href="#" class="link-name" style="font-size:12px;">Evaluation Report</a></li>
      </ul>
    </li>

    
  </ul>
</div>

<div class="wrapper pb-5 pt-4" style="min-height: 100vh; height: auto; background-color: #fafafa;">
  <?php include('../db_conn.php');?>
  
  <nav class="navbar navbar-expand-lg " style="background-color: #44311f; display: flex; align-items: center; height: 65px; width: 100%; position: fixed; top: 0; z-index: 1000;">
      <div class="container-fluid">
          <i class="bi bi-list text-white" style="font-size: 1.5rem; cursor: pointer;"></i>
          <img src="../Logo/mja-logo.png" alt="Mary Josette Academy"  style="width: 50px; height: auto; cursor: pointer;" class="img-fluid ms-4">
          <a class="navbar-brand text-white fw-bold fs-6 d-none ms-2 d-lg-block" href="#">Evalu8: A Faculty Evaluation System</a>
          <div class="dropdown ms-auto me-4" style="position: relative;">
          <a class="nav-link dropdown-toggle d-flex align-items-center text-white-50" style="margin-right: 75px;" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">

          
        <?php

          $id = intval($_SESSION['user_id']);

          $query = "SELECT * FROM `admin_account` WHERE `admin_id` = '$id'";
          $result = mysqli_query($conn, $query);

          if (!$result) {
            die("Query Failed: " . mysqli_error($conn));
          }
          else {
            while ($row = mysqli_fetch_assoc($result)) { ?>
              
            <?php if (!empty($row['avatar'])): ?>
              <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['avatar']); ?>" alt="User" class="rounded-circle" width="40" height="40">
            <?php else: ?>
              <i class="bi bi-person-circle" style="font-size: 40px;"></i>
            <?php endif; ?>

            <span class="fname ms-2 text-white fw-bold"><?php echo strtoupper($row['lastName']).", ".strtoupper($row['firstName'])?></span>
          
          
      </a>
              
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink" style="position: fixed; top: 10%; right: 2%; z-index: 1050;">
        <li><a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#manageAccountModal">Manage Account</a></li>
        <li><a class="dropdown-item text-dark" href="../login/logout.php">Logout</a></li>
    </ul>

    </div>
  </div>
</nav>

<!-- Manage Account Modal -->
<div class="modal fade" id="manageAccountModal" tabindex="-1" aria-labelledby="manageAccountLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manageAccountLabel">Manage Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="manageAccountForm" method="POST" action="actions/update-admin.php" enctype="multipart/form-data">
          <!-- First Name Field -->
          <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $row['firstName']; ?>" required>
          </div>

          <!-- Middle Name Field -->
          <div class="mb-3">
            <label for="middleName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo $row['middleName']; ?>">
          </div>

          <!-- Last Name Field -->
          <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $row['lastName']; ?>" required>
          </div>

          <!-- Email Field -->
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
          </div>

          <!-- Password Field (Leave blank if not changing) -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if not changing">
          </div>

          <!-- Avatar Input Field -->
          <div class="mb-3">
            <label for="avatarInput" class="form-label">Avatar</label>
            <input type="file" class="form-control" id="avatarInput" name="avatar" accept="image/*">
          </div>

          <!-- Image Preview and Cropping Section -->
          <div id="imagePreviewContainer" style="display:none;">
            <h5>Crop Your Image</h5>
            <img id="imagePreview" style="max-width: 100%; display: block;" />
            <button type="button" id="cropImageBtn" class="btn btn-primary mt-3">Crop Image</button>
          </div>

          <!-- Hidden Input for Cropped Image Data -->
          <input type="hidden" id="croppedImageData" name="croppedImageData">

          <!-- Avatar Display Section -->
          <div class="mb-3 text-center">
            <?php if (!empty($row['avatar'])): ?>
              <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['avatar']); ?>" alt="User Avatar" class="rounded-circle border" width="120" height="120">
            <?php else: ?>
              <img src="default-avatar.png" alt="Default Avatar" class="rounded-circle border" width="120" height="120">
            <?php endif; ?>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <?php	
    }
  } ?>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      let cropper;
      const avatarInput = document.getElementById('avatarInput');
      const imagePreview = document.getElementById('imagePreview');
      const imagePreviewContainer = document.getElementById('imagePreviewContainer');
      const croppedImageData = document.getElementById('croppedImageData');
      const cropImageBtn = document.getElementById('cropImageBtn');
      const cropSuccessMessage = document.createElement('div'); // Element to show success message
      cropSuccessMessage.className = 'alert alert-success mt-3';
      cropSuccessMessage.style.display = 'none'; // Hidden by default
      cropSuccessMessage.innerText = 'Image cropped successfully!';

      // Append the success message after the Crop button
      cropImageBtn.parentNode.insertBefore(cropSuccessMessage, cropImageBtn.nextSibling);

      avatarInput.addEventListener('change', function(event) {
          const files = event.target.files;
          if (files && files.length > 0) {
              const file = files[0];
              const reader = new FileReader();

              reader.onload = function(e) {
                  imagePreview.src = e.target.result;
                  imagePreviewContainer.style.display = 'block';
                  cropSuccessMessage.style.display = 'none'; // Hide success message when changing image

                  // Destroy previous cropper instance, if any
                  if (cropper) {
                      cropper.destroy();
                  }

                  // Initialize Cropper.js with 1x1 aspect ratio
                  cropper = new Cropper(imagePreview, {
                      aspectRatio: 1,
                      viewMode: 1,
                      autoCropArea: 1,
                      ready: function () {
                          // Adjust the cropper UI to better indicate cropping
                          cropper.setDragMode('move');
                      }
                  });
              };

              reader.readAsDataURL(file);
          }
      });

      cropImageBtn.addEventListener('click', function() {
          if (cropper) {
              // Get cropped image data as a Blob and convert it to base64
              const canvas = cropper.getCroppedCanvas();
              canvas.toBlob(function(blob) {
                  const reader = new FileReader();
                  reader.onload = function(e) {
                      // Store the base64 image data in the hidden input
                      croppedImageData.value = e.target.result;
                      
                      // Show success message and update Crop button style
                      cropSuccessMessage.style.display = 'block'; // Show success message
                      cropImageBtn.classList.add('btn-success'); // Change button style to indicate success
                      cropImageBtn.innerText = 'Cropped!'; // Update button text
                  };
                  reader.readAsDataURL(blob);
              }, 'image/jpeg');
          }
      });

      // Ensure cropped image data is set before form submission
      document.getElementById('manageAccountForm').addEventListener('submit', function(e) {
          if (!croppedImageData.value) {
              e.preventDefault();
              alert('Please crop the image before submitting the form.');
          }
      });
  });

  </script>