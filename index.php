<?php 
session_start(); 
include "db.php";

class StudentManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function createStudent($student_id, $name, $email, $course) {
        $formatted_name = ucwords(trim($name)); 
        $formatted_course = strtoupper(trim($course)); 
        $valid_id = abs((int)$student_id); 

        $query = "INSERT INTO students (student_id, student_name, email, course) VALUES ('$valid_id', '$formatted_name', '$email', '$formatted_course')";
        return $this->conn->query($query);
    }

    public function updateStudent($id, $student_id, $name, $email, $course) {
        $formatted_name = ucwords(trim($name));
        $formatted_course = strtoupper(trim($course));
        $valid_id = abs((int)$student_id);

        $query = "UPDATE students SET student_name='$formatted_name', email='$email', student_id='$valid_id', course='$formatted_course' WHERE id_number='$id'";
        return $this->conn->query($query);
    }

    public function deleteStudent($id) {
        $query = "DELETE FROM students WHERE id_number = '$id'";
        return $this->conn->query($query);
    }

    public function getAllStudents() {
        $query = "SELECT * FROM students";
        return $this->conn->query($query);
    }

    public function getStudentById($id) {
        $query = "SELECT * FROM students WHERE id_number = '$id'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc();
    }
}

$db = new Database();
$manager = new StudentManager($db->conn);

if (isset($_GET['delete'])) {
    $manager->deleteStudent($_GET['delete']);
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'];
    $course = $_POST['course'];
    
    if (!empty($_POST['id_number'])) {
        $manager->updateStudent($_POST['id_number'], $student_id, $student_name, $email, $course);
    } else {
        $manager->createStudent($student_id, $student_name, $email, $course);
    }
    
    header("Location: index.php");
    exit();
}

$u_name = "";
$u_email = "";
$u_student_id = "";
$u_course = "";
$u_id_number = "";

if (isset($_GET['edit'])) {
    $studentData = $manager->getStudentById($_GET['edit']);
    if ($studentData) {
        $u_name = $studentData['student_name'];
        $u_email = $studentData['email'];
        $u_student_id = $studentData['student_id'];
        $u_course = $studentData['course'];
        $u_id_number = $studentData['id_number'];
    }
}

$result = $manager->getAllStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records System</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="app-container">
    <div class="header-banner">
        <h2>Student Records</h2>
    </div>

    <div class="top-form-section">
        <h3><?php if($u_id_number != "") { echo "Edit Student Information"; } else { echo "Register New Student"; } ?></h3>
        
        <form action="index.php" method="POST" class="add-form">
            <input type="hidden" name="id_number" value="<?php echo $u_id_number; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="student_name" value="<?php echo $u_name; ?>" placeholder="e.g. Russel John Ragadio" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $u_email; ?>" placeholder="russeljohn@fake.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="number" name="student_id" value="<?php echo $u_student_id; ?>" placeholder="12345678" required>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course" value="<?php echo $u_course; ?>" placeholder="BSIT" required>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <?php if($u_id_number != "") { echo "Confirm Updates"; } else { echo "Add Student"; } ?>
            </button>
            
            <?php if($u_id_number != "") { ?>
                <a href="index.php" class="cancel-btn">Discard Changes</a>
            <?php } ?>
        </form>
    </div>

    <div class="bottom-records-section">
        <h3>Current Enrolled Students</h3>
        <div class="records-grid">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { 
            ?>
                    <div class="student-card">
                        <div class="card-header">
                            <span class="badge course-badge"><?php echo $row['course']; ?></span>
                            <span class="badge id-badge">ID: <?php echo $row['student_id']; ?></span>
                        </div>
                        <h4 class="student-name"><?php echo $row['student_name']; ?></h4>
                        <p class="student-email">📧 <?php echo $row['email']; ?></p>
                        
                        <div class="card-actions">
                            <a href="index.php?edit=<?php echo $row['id_number']; ?>" class="btn-edit">Edit</a>
                            <a href="index.php?delete=<?php echo $row['id_number']; ?>" class="btn-delete" onclick="return confirm('Remove this student from the database?');">Remove</a>
                        </div>
                    </div>
            <?php 
                } 
            } else {
                echo '<div class="empty-state">No students found in the database.</div>';
            } 
            ?>
        </div>
    </div>
</div>

</body>
</html>