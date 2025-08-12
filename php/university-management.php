<?php
header('Content-Type: application/json'); // Crucial: Tell browser to expect JSON

require_once 'connection.php';

$response = ['status' => 'error', 'message' => 'Invalid request.'];

// Determine if it's a GET request (for fetching data) or POST (for actions)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if a specific university is requested for editing
    if (isset($_GET['action']) && $_GET['action'] === 'fetch_single' && isset($_GET['id'])) {
        $university_id = $_GET['id'];
        $sql = "SELECT * FROM universities WHERE university_id = :university_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':university_id', $university_id, PDO::PARAM_INT);
        $stmt->execute();
        $university = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($university) {
            $response = ['status' => 'success', 'data' => $university];
        } else {
            $response = ['status' => 'error', 'message' => 'University not found.'];
        }
    } else {
        // Default action: Fetch all universities
        $sql = "SELECT * FROM universities ORDER BY university_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $universities = [];
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $universities[] = $row;
            }
        }
        $response = ['status' => 'success', 'data' => $universities];
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $university_name = $_POST['university_name'] ?? '';
        $image = $_FILES['image']['name'] ?? '';
        $founded = $_POST['founded'] ?? '';
        $website = $_POST['website'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $campus = $_POST['campus'] ?? '';
        $hours = $_POST['hours'] ?? '';
        $details = $_POST['details'] ?? '';
        $university_slug = $_POST['university_slug'] ?? '';

        if (empty($university_name) || empty($founded) || empty($website) || empty($phone) || empty($campus) || empty($hours) || empty($details)) {
            $response = ['status' => 'error', 'message' => 'All fields are required.'];
        } else {
            $target_dir = "../images/";
            $uploadOk = 1;
            $final_image_name = '';

            if ($image) {
                $target_file = $target_dir . basename($image);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    $response = ['status' => 'error', 'message' => "File is not an image."];
                    $uploadOk = 0;
                }
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $response = ['status' => 'error', 'message' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed for image."];
                    $uploadOk = 0;
                }
                $final_image_name = basename($image);
            } else {
                $final_image_name = 'default_university.png';
            }

            if ($uploadOk == 0) {
                // Error already set in $response
            } else {
                $check_sql = "SELECT university_id FROM universities WHERE university_name = :university_name";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bindParam(':university_name', $university_name);
                $check_stmt->execute();

                if ($check_stmt->rowCount() > 0) {
                    $response = ['status' => 'duplicate', 'message' => "University with this name already exists."];
                } else {
                    if ($image && !move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $final_image_name)) {
                        $response = ['status' => 'error', 'message' => "Sorry, there was an error uploading your image."];
                    } else {
                        $sql = "INSERT INTO universities (university_name, image, founded, website, phone, campus, hours, details, University_Slug) VALUES (:university_name, :image, :founded, :website, :phone, :campus, :hours, :details, :university_slug)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':university_name', $university_name);
                        $stmt->bindParam(':image', $final_image_name);
                        $stmt->bindParam(':founded', $founded);
                        $stmt->bindParam(':website', $website);
                        $stmt->bindParam(':phone', $phone);
                        $stmt->bindParam(':campus', $campus);
                        $stmt->bindParam(':hours', $hours);
                        $stmt->bindParam(':details', $details);
                        $stmt->bindParam(':university_slug', $university_slug);

                        if ($stmt->execute()) {
                            $response = ['status' => 'success', 'message' => 'University added successfully!'];
                        } else {
                            $response = ['status' => 'error', 'message' => 'Error adding university: ' . $stmt->errorInfo()[2]];
                        }
                    }
                }
            }
        }
    } elseif ($action === 'edit') {
        $university_id = $_POST['university_id'] ?? null;
        $university_name = $_POST['university_name'] ?? '';
        $current_image = $_POST['current_image'] ?? 'default_university.png';
        $image = $_FILES['image']['name'] ?? $current_image;
        $founded = $_POST['founded'] ?? '';
        $website = $_POST['website'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $campus = $_POST['campus'] ?? '';
        $hours = $_POST['hours'] ?? '';
        $details = $_POST['details'] ?? '';
        $university_slug = $_POST['university_slug'] ?? '';

        if (empty($university_id) || empty($university_name) || empty($founded) || empty($website) || empty($phone) || empty($campus) || empty($hours) || empty($details)) {
            $response = ['status' => 'error', 'message' => 'All fields are required for editing.'];
        } else {
            $target_dir = "../../images/";
            $uploadOk = 1;
            $final_image_name = $current_image;

            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    $response = ['status' => 'error', 'message' => "File is not an image."];
                    $uploadOk = 0;
                }
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $response = ['status' => 'error', 'message' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed for image."];
                    $uploadOk = 0;
                }
                $final_image_name = basename($_FILES["image"]["name"]);
            }

            if ($uploadOk == 0) {
                // Error already set in $response
            } else {
                if ($_FILES['image']['error'] === UPLOAD_ERR_OK && !move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $final_image_name)) {
                    $response = ['status' => 'error', 'message' => "Sorry, there was an error uploading your new image."];
                } else {
                    $check_sql = "SELECT university_id FROM universities WHERE university_name = :university_name AND university_id != :university_id";
                    $check_stmt = $conn->prepare($check_sql);
                    $check_stmt->bindParam(':university_name', $university_name);
                    $check_stmt->bindParam(':university_id', $university_id, PDO::PARAM_INT);
                    $check_stmt->execute();

                    if ($check_stmt->rowCount() > 0) {
                        $response = ['status' => 'duplicate', 'message' => "University with this name already exists."];
                    } else {
                        $sql = "UPDATE universities SET university_name = :university_name, image = :image, founded = :founded, website = :website, phone = :phone, campus = :campus, hours = :hours, details = :details, University_Slug = :university_slug WHERE university_id = :university_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':university_name', $university_name);
                        $stmt->bindParam(':image', $final_image_name);
                        $stmt->bindParam(':founded', $founded);
                        $stmt->bindParam(':website', $website);
                        $stmt->bindParam(':phone', $phone);
                        $stmt->bindParam(':campus', $campus);
                        $stmt->bindParam(':hours', $hours);
                        $stmt->bindParam(':details', $details);
                        $stmt->bindParam(':university_slug', $university_slug);
                        $stmt->bindParam(':university_id', $university_id, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            $response = ['status' => 'success', 'message' => 'University updated successfully!'];
                        } else {
                            $response = ['status' => 'error', 'message' => 'Error updating university: ' . $stmt->errorInfo()[2]];
                        }
                    }
                }
            }
        }
    } elseif ($action === 'delete') {
        $university_id = $_POST['university_id'] ?? null;
        if ($university_id) {
            try {
                $target_dir = "../../images/";
                $img_sql = "SELECT image FROM universities WHERE university_id = :university_id";
                $img_stmt = $conn->prepare($img_sql);
                $img_stmt->bindParam(':university_id', $university_id, PDO::PARAM_INT);
                $img_stmt->execute();
                $img_result = $img_stmt->fetch(PDO::FETCH_ASSOC);
                if ($img_result && $img_result['image'] && $img_result['image'] != 'default_university.png') {
                    $image_path = $target_dir . $img_result['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }

                $sql = "DELETE FROM universities WHERE university_id = :university_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':university_id', $university_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'University deleted successfully!'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Error deleting university: ' . $stmt->errorInfo()[2]];
                }
            } catch (PDOException $e) {
                error_log("Error deleting university: " . $e->getMessage());
                $response = ['status' => 'error', 'message' => 'Database error during deletion.'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'No university ID provided for deletion.'];
        }
    }
}

echo json_encode($response);
exit(); // Ensure no other output
?>