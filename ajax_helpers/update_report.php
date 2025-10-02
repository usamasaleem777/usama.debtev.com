<?php
require('../functions.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Get input data
    $reportId = $_POST['report_id'] ?? 0;
    $changes = json_decode($_POST['changes'], true);

    // Validate
    if (!$reportId) {
        throw new Exception('Missing report ID');
    }
    if (empty($changes)) {
        throw new Exception('No changes to save');
    }

    // Check if the report exists
    $report = DB::queryFirstRow("SELECT * FROM daily_work_reports WHERE id = %i", $reportId);
    if (!$report) {
        throw new Exception('Report not found');
    }

    // Only allow updates if report is not locked or user is superintendent
    // if ($report['is_locked'] && ($_SESSION['role_id'] != 6)) {
    //     throw new Exception('Report is locked and cannot be modified');
    // }

    // Process each change
    foreach ($changes as $change) {
        $table = $change['table'] ?? null;
        $id = $change['id'] ?? null;
        $field = $change['field'] ?? null;
        $value = $change['value'] ?? null;
        
        if (!$table || !$field) continue;

        // Handle main report table differently
        if ($table === 'daily_work_reports') {
            // Update main report using the report ID
            DB::update('daily_work_reports', [$field => $value], "id = %i", $reportId);
        } 
        // Handle child tables (subcontractors, equipment, materials)
        else {
            // Validate record exists and belongs to report
            $record = DB::queryFirstRow(
                "SELECT * FROM `$table` WHERE id = %i AND report_id = %i", 
                (int)$id, 
                (int)$reportId
            );
            
            if (!$record) {
                throw new Exception("Record $id in $table not found for report $reportId");
            }
            
            DB::update($table, [$field => $value], "id = %i", (int)$id);
        }
    }

    $response['success'] = true;
    $response['message'] = 'Changes saved successfully';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (MeekroDBException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
