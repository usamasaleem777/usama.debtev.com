<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id']) && isset($_POST['tools'])) {
    $job_ids = $_POST['job_id'];
    $tools_all = $_POST['tools'];

    foreach ($job_ids as $index => $job_id) {
        foreach ($tools_all as $tool_id => $toolData) {
            // Check if the tool is selected in this job block
            if (isset($toolData['selected'][$index]) && isset($toolData['quantity'][$index])) {
                $qty_raw = $toolData['quantity'][$index];
                $assigned_qty = is_array($qty_raw) ? (int) implode('', $qty_raw) : (int) $qty_raw;

                // Fetch current available quantity from tools table
                $tool = DB::queryFirstRow("SELECT quantity FROM tools WHERE tool_id = %i", $tool_id);

                if ($tool) {
                    $available_qty = (int)$tool['quantity'];

                    if ($assigned_qty > $available_qty) {
                        $_SESSION['error'] = "Not enough quantity available for tool ID $tool_id.";
                        echo "<script>window.location.href='index.php?route=modules/tools/assign_tools2';</script>";
                        exit;
                    }

                    // Insert into job_tool_assignments
                    DB::insert('job_tool_assignments', [
                        'job_id' => $job_id,
                        'tool_id' => $tool_id,
                        'assigned_quantity' => $assigned_qty
                    ]);
                }
            }
        }

        $_SESSION['success'] = "Tools successfully assigned to job.";
        echo "<script>window.location.href='index.php?route=modules/tools/assign_tools2';</script>";
        exit;
    }
}
