<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete operation
    if (isset($_POST['btnDeletetool'])) {
        $toolId = isset($_POST['tool_id']) ? $_POST['tool_id'] : null;

        if (empty($toolId)) {
            $_SESSION['error'] = "Invalid tool ID";
            echo "<script>window.location.href='index.php?route=modules/tools/list_tools';</script>";
            exit();
        }

        try {
            DB::delete('tools', "tool_id=%i", $toolId);
            $_SESSION['success'] = "tool deleted successfully";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting tool: " . $e->getMessage();
        }

        echo "<script>window.location.href='index.php?route=modules/tools/list_tools';</script>";
        exit();
    }

    // Handle add/update operation
    if (isset($_POST['btnSubmittool'])) {
        $toolId = isset($_POST['tool_id']) ? $_POST['tool_id'] : null;
        $toolTitle = isset($_POST['tool_name']) ? trim($_POST['tool_name']) : '';
        $toolDescription = isset($_POST['tool_description']) ? trim($_POST['tool_description']) : '';
        $toolQuantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';

        // Handle file upload if a file was uploaded
        $toolPicture = null;
        if (isset($_FILES['tool_picture']) && $_FILES['tool_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/tools/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileTmpPath = $_FILES['tool_picture']['tmp_name'];
            $fileName = basename($_FILES['tool_picture']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = uniqid('tool_', true) . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $toolPicture = $newFileName;
                } else {
                    $_SESSION['error'] = "Failed to upload image.";
                }
            } else {
                $_SESSION['error'] = "Invalid image format. Only JPG, JPEG, PNG, GIF allowed.";
            }
        }

        // Validate input
        if (empty($toolTitle)) {
            $_SESSION['error'] = "tool title cannot be empty";
            $redirect = $toolId ? "add_tool&tool_id=$toolId" : "list_tools";
            echo "<script>window.location.href='index.php?route=modules/tools/$redirect';</script>";
            exit();
        }

        try {
            if (!empty($toolId)) {
                // Prepare update data
                $updateData = [
                    'tool_name' => $toolTitle,
                    'tool_description' => $toolDescription,
                    'quantity' => $toolQuantity,
                ];

                if ($toolPicture) {
                    $updateData['tool_picture'] = $toolPicture;
                }

                DB::update('tools', $updateData, "tool_id=%i", $toolId);
                $_SESSION['success'] = "tool updated successfully";
            } else {
                // Insert new record
                // Check if tool already exists (by name — and optionally, job_id/shift_id if needed)
                $existingTool = DB::queryFirstRow("SELECT * FROM tools WHERE tool_name = %s", $toolTitle);

                if ($existingTool) {
                    // Tool exists — update quantity
                    $newQuantity = $existingTool['quantity'] + (int)$toolQuantity;

                    $updateData = [
                        'quantity' => $newQuantity,
                        'tool_description' => $toolDescription,
                    ];

                    if ($toolPicture) {
                        $updateData['tool_picture'] = $toolPicture;
                    }

                    DB::update('tools', $updateData, "tool_id=%i", $existingTool['tool_id']);
                    $_SESSION['success'] = "Tool quantity updated (existing tool)";
                } else {
                    // Tool doesn't exist — insert new
                    $insertData = [
                        'tool_name' => $toolTitle,
                        'tool_description' => $toolDescription,
                        'quantity' => $toolQuantity,
                    ];

                    if ($toolPicture) {
                        $insertData['tool_picture'] = $toolPicture;
                    }

                    DB::insert('tools', $insertData);
                    $_SESSION['success'] = "Tool added successfully";
                }
            }

            echo "<script>window.location.href='index.php?route=modules/tools/list_tools';</script>";
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error saving tool: " . $e->getMessage();
            $redirect = $toolId ? "list_tools&tool_id=$toolId" : "list_tools";
            echo "<script>window.location.href='index.php?route=modules/tools/$redirect';</script>";
            exit();
        }
    }
}

// Redirect if accessed directly
echo "<script>window.location.href='index.php?route=modules/tools/list_tools';</script>";
exit();
