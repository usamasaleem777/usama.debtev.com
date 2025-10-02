<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete operation
    if (isset($_POST['btnDeleteEquipment'])) {
        $equipmentId = isset($_POST['id']) ? $_POST['id'] : null;

        if (empty($equipmentId)) {
            $_SESSION['error'] = "Invalid Equipment ID";
            echo "<script>window.location.href='index.php?route=modules/equipment/list_equipment';</script>";
            exit();
        }

        try {
            // First check if equipment is assigned to anyone
            $assigned = DB::queryFirstField("SELECT COUNT(*) FROM equipment_assignments WHERE id = %i", $equipmentId);
            if ($assigned > 0) {
                $_SESSION['error'] = "Cannot delete equipment that is currently assigned";
                echo "<script>window.location.href='index.php?route=modules/equipment/list_equipment';</script>";
                exit();
            }

            DB::delete('equipment', "id=%i", $equipmentId);
            $_SESSION['success'] = "Equipment deleted successfully";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting equipment: " . $e->getMessage();
        }

        echo "<script>window.location.href='index.php?route=modules/equipment/list_equipment';</script>";
        exit();
    }

    // Handle add/update operation
    if (isset($_POST['btnSubmitEquipment'])) {
        $equipmentId = isset($_POST['id']) ? $_POST['id'] : null;
        $equipmentName = isset($_POST['equipment_name']) ? trim($_POST['equipment_name']) : '';
        $serialNumber = isset($_POST['serial_number']) ? trim($_POST['serial_number']) : '';
        $equipmentType = isset($_POST['equipment_type']) ? trim($_POST['equipment_type']) : '';
        $purchaseDate = isset($_POST['purchase_date']) ? trim($_POST['purchase_date']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        // Handle file upload if a file was uploaded
        $equipmentImage = null;
        if (isset($_FILES['equipment_image']) && $_FILES['equipment_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/equipment/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileTmpPath = $_FILES['equipment_image']['tmp_name'];
            $fileName = basename($_FILES['equipment_image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = uniqid('equipment_', true) . '.' . $fileExtension;
                $destination = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $equipmentImage = $newFileName;
                } else {
                    $_SESSION['error'] = "Failed to upload image.";
                }
            } else {
                $_SESSION['error'] = "Invalid image format. Only JPG, JPEG, PNG, GIF allowed.";
            }
        }

        // Validate input
        if (empty($equipmentName)) {
            $_SESSION['error'] = "Equipment name cannot be empty";
            $redirect = $equipmentId ? "add_equipment&id=$equipmentId" : "list_equipment";
            echo "<script>window.location.href='index.php?route=modules/equipment/$redirect';</script>";
            exit();
        }

        if (empty($serialNumber)) {
            $_SESSION['error'] = "Serial number cannot be empty";
            $redirect = $equipmentId ? "add_equipment&id=$equipmentId" : "list_equipment";
            echo "<script>window.location.href='index.php?route=modules/equipment/$redirect';</script>";
            exit();
        }

        try {
            if (!empty($equipmentId)) {
                // Update existing equipment
                $updateData = [
                    'equipment_name' => $equipmentName,
                    'serial_number' => $serialNumber,
                    'equipment_type' => $equipmentType,
                    'purchase_date' => $purchaseDate,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($equipmentImage) {
                    $updateData['image_path'] = $equipmentImage;
                }

                DB::update('equipment', $updateData, "id=%i", $equipmentId);
                $_SESSION['success'] = "Equipment updated successfully";
            } else {
                // Check if equipment with same serial number already exists
                $existingEquipment = DB::queryFirstRow(
                    "SELECT * FROM equipment WHERE serial_number = %s", 
                    $serialNumber
                );

                if ($existingEquipment) {
                    $_SESSION['error'] = "Equipment with this serial number already exists";
                    echo "<script>window.location.href='index.php?route=modules/equipment/add_equipment';</script>";
                    exit();
                }

                // Insert new equipment
                $insertData = [
                    'equipment_name' => $equipmentName,
                    'serial_number' => $serialNumber,
                    'equipment_type' => $equipmentType,
                    'purchase_date' => $purchaseDate,
                    'status' => $status,
                    'added_date' => date('Y-m-d H:i:s')
                ];

                if ($equipmentImage) {
                    $insertData['image_path'] = $equipmentImage;
                }

                DB::insert('equipment', $insertData);
                $_SESSION['success'] = "New equipment added successfully";
            }

            echo "<script>window.location.href='index.php?route=modules/equipment/list_equipment';</script>";
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Error saving equipment: " . $e->getMessage();
            $redirect = $equipmentId ? "add_equipment&id=$equipmentId" : "add_equipment";
            echo "<script>window.location.href='index.php?route=modules/equipment/$redirect';</script>";
            exit();
        }
    }
}

// Redirect if accessed directly
echo "<script>window.location.href='index.php?route=modules/equipment/list_equipment';</script>";
exit();