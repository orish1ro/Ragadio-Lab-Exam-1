<?php 
session_start(); 
include "db.php";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM students WHERE id_number = '$id'");
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'];
    $course = $_POST['course'];
    
    if (!empty($_POST['id_number'])) {
        $id = $_POST['id_number'];
        $query = "UPDATE students SET student_name='$student_name', email='$email', student_id='$student_id', course='$course' WHERE id_number='$id'";
    } else {
        $query = "INSERT INTO students (student_id, student_name, email, course) VALUES ('$student_id', '$student_name', '$email', '$course')";
    }
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    }
}

$u_name = "";
$u_email = "";
$u_student_id = "";
$u_course = "";
$u_id_number = "";

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM students WHERE id_number = '$id'");
    if ($row = mysqli_fetch_assoc($edit_query)) {
        $u_name = $row['student_name'];
        $u_email = $row['email'];
        $u_student_id = $row['student_id'];
        $u_course = $row['course'];
        $u_id_number = $row['id_number'];
    }
}

$query = "SELECT * FROM students";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Student Records</h2>
        
        <div class="add-form-container">
            <h3 style="margin-bottom: 15px;">
                <?php if($u_id_number != "") { echo "Edit Student"; } else { echo "Add New Student"; } ?>
            </h3>
            <form action="index.php" method="POST" class="add-form">
                <input type="hidden" name="id_number" value="<?php echo $u_id_number; ?>">
                
                <div class="form-group">
                    <input type="text" name="student_name" placeholder="Student Name" value="<?php echo $u_name; ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" value="<?php echo $u_email; ?>" required>
                </div>
                <div class="form-group">
                    <input type="number" name="student_id" placeholder="Student ID Number" value="<?php echo $u_student_id; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="course" placeholder="Course" value="<?php echo $u_course; ?>" required>
                </div>
                <button type="submit" class="submit-btn">
                    <?php if($u_id_number != "") { echo "Update Student"; } else { echo "Save Student"; } ?>
                </button>
                
                <?php if($u_id_number != "") { ?>
                    <a href="index.php" class="cancel-btn" style="display:block; text-align:center; margin-top:10px; color:#6b7280; text-decoration:none;">Cancel Edit</a>
                <?php } ?>
            </form>
        </div>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #e5e7eb;">

        <div class="records-container">
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { 
            ?>
                    <div class="student-card">
                        <div class="student-details">
                            <h4 class="student-name"><?php echo $row['student_name']; ?></h4>
                            <p class="student-info"><?php echo $row['email']; ?></p>
                            <p class="student-info"><?php echo $row['student_id']; ?></p>
                            <p class="student-info"><?php echo $row['course']; ?></p>
                        </div>
                        <div class="card-options">
                            <a href="index.php?edit=<?php echo $row['id_number']; ?>" class="action-btn edit-btn">Edit</a>
                            <a href="index.php?delete=<?php echo $row['id_number']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </div>
                    </div>
            <?php 
                } 
            } else {
                echo "<p>No student records found.</p>";
            } 
            ?>
        </div>
    </div>
</body>
</html>