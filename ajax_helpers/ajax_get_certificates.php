<?php


require('../functions.php');

try {


    $applicantId = (int) $_POST['applicant_ID'];

    // File Upload Handling
    $licenseFiles = [];
    $uploadDir = __DIR__ . '/uploads/certifications/';

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'applicant_id' => $applicantId,
            'message' => 'Failed to create directory: $uploadDir'
        ]);
    }

    // Check if files are uploaded
    if (!empty($_FILES['license_files']['name'][0])) {
        foreach ($_FILES['license_files']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['license_files']['error'][$key] !== UPLOAD_ERR_OK) {
                error_log("Upload error at index $key: " . $_FILES['license_files']['error'][$key]);
                continue;
            }

            // Validate MIME type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tmpName);
            $validTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'application/pdf' => 'pdf'
            ];

            if (!isset($validTypes[$mimeType])) {
                error_log("Invalid MIME type: $mimeType");
                continue;
            }

            // Generate unique file name
            $ext = $validTypes[$mimeType];
            $filename = sprintf(
                "certification_%s_%s.%s",
                uniqid(),
                bin2hex(random_bytes(4)),
                $ext
            );
            $destination = $uploadDir . $filename;

            // ✅ Move uploaded file
            if (!move_uploaded_file($tmpName, $destination)) {
                error_log("Failed to move uploaded file: $filename");
                continue;
            }

            // ✅ Insert into database
            try {
                DB::insert('certification_files', [
                    'applicant_id' => $applicantId,
                    'file_name' => $filename,
                ]);
            } catch (Exception $dbErr) {
                error_log("DB insert failed: " . $dbErr->getMessage());
                echo json_encode([
                    'success' => false,
                    'applicant_id' => $applicantId,
                    'message' => 'Database insert failed'
                ]);
            }


            $licenseFiles[] = $filename;
        }
    } else {
        echo json_encode([
            'success' => false,
            'applicant_id' => $applicantId,
            'message' => 'No files uploaded'
        ]);
    }

    echo json_encode([
        'success' => true,
        'applicant_id' => $applicantId,
        'files_uploaded' => count($licenseFiles),
        'total_files' => count($licenseFiles)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
