<?php
require('../functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    error_log('Received POST data: ' . print_r($_POST, true));

    $step = $_POST['save_step'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;

    try {
        // Step 1: Create/Update Report
        if ($step == '1') {
            DB::startTransaction();

            // Validate required fields
            // $required = ['job_id', 'shift', 'crew_id', 'foreman_id', 'report_date'];
            // foreach ($required as $field) {
            //     if (empty($_POST[$field])) throw new Exception("Missing required field: $field");
            // }

            $foremanId = $_POST['foreman_id'];
            $reportDate = $_POST['report_date'];

            // Check existing report
            $existingReport = DB::queryFirstRow(
                "SELECT * FROM daily_work_reports 
                WHERE foreman_id = %i AND report_date = %s",
                $foremanId,
                $reportDate
            );

            // Handle locked report
            if ($existingReport && $existingReport['is_locked']) {
                throw new Exception('Report for this date is locked and cannot be modified');
            }

            // Prepare report data
            $reportData = [
                'job_id' => $_POST['job_id'] ?? "N/A",
                'report_date' => $reportDate,
                'shift' => $_POST['shift'] ?? "N/A",
                'crew_id' => $_POST['crew_id'] ?? "N/A",
                'foreman_id' => $foremanId,
                'weather_conditions' => json_encode([
                'temperature' => $_POST['weather']['temp'] ?? null,
                'humidity' => $_POST['weather']['humidity'] ?? null,
                'wind_speed' => $_POST['weather']['wind'] ?? null,
                'condition' => $_POST['weather']['condition'] ?? null,
                'icon' => $_POST['weather']['icon'] ?? null,
                'location' => $_POST['weather']['location'] ?? null,
                'manual_conditions' => $_POST['site_conditions'] ?? [] // Keep site conditions
                 ]),
                'site_conditions' => implode(',', $_POST['site_conditions'] ?? []),
                'superintendent_name' => $_SESSION['user_name'] ?? 'Unknown'
            ];

            // Update or create report
            if ($existingReport) {
                DB::update('daily_work_reports', $reportData, "foreman_id = %i", $foremanId);
                $reportId = $existingReport['id'];
            } else {
                DB::insert('daily_work_reports', $reportData);
                $reportId = DB::insertId();
            }

            DB::commit();

            echo json_encode([
                'success' => true,
                'report_id' => $reportId,
                'message' => 'Step 1 saved successfully'
            ]);
            exit;
        }

        // Subsequent Steps (2-6) - Unified Report Validation
        if (in_array($step, ['2', '3', '4', '5', '6'])) {
            $foremanId = $_POST['foreman_id'];
            $reportDate = $_POST['report_date'];

            // Get existing report and set report_id
            $existingReport = DB::queryFirstRow(
                "SELECT * FROM daily_work_reports 
         WHERE foreman_id = %i AND report_date = %s",
                $foremanId,
                $reportDate
            );
            if (!$existingReport)
                throw new Exception('Report not found');
            if ($existingReport['is_locked'])
                throw new Exception('Report is locked');

            // CRITICAL FIX: Set report_id from existing report
            $reportId = $existingReport['id'];

            // Handle specific steps
            switch ($step) {
                case '2': // Work Force
                    DB::startTransaction();

                    // FIX: Delete existing subcontractors for this report
                    DB::delete('subcontractors', 'report_id = %i', $reportId);

                    foreach ($_POST['subcontractors'] ?? [] as $sub) {
                        if (!empty($sub['name'])) {
                            DB::insert('subcontractors', [
                                'report_id' => $reportId, // Now uses actual ID
                                'subcontractor_name' => $sub['name'],
                                'headcount' => $sub['headcount'] ?? 0
                            ]);
                        }
                    }

                    // Update counts
                    $craftCount = (int) ($_POST['craft_count'] ?? 0);
                    $totalHeadCount = (int) ($_POST['total_head_count'] ?? 0);


                    DB::update('daily_work_reports', [
                        'craft_count' => $craftCount,
                        'total_head_count' => $totalHeadCount
                    ], "id = %i", $reportId); // Update by report_id

                    DB::commit();
                    break;

                case '3': // Equipment
                    DB::startTransaction();

                    // FIX: Delete existing equipment entries
                    DB::delete('equipment_usage', 'report_id = %i', $reportId);

                    foreach ($_POST['equipment'] ?? [] as $eq) {
                        if (!empty($eq['tool_id'])) {
                            DB::insert('equipment_usage', [
                                'report_id' => $reportId, // Actual ID
                                'tool_id' => $eq['tool_id'],
                                'quantity' => $eq['quantity'] ?? 0,
                                'hours_used' => $eq['hours_used'] ?? 0,
                                'comments' => $eq['comments'] ?? null
                            ]);
                        }
                    }

                    DB::commit();
                    break;

                case '4': // Materials
                    DB::startTransaction();

                    // FIX: Delete existing material entries
                    DB::delete('material_usage', 'report_id = %i', $reportId);

                    foreach ($_POST['materials'] ?? [] as $mat) {
                        if (!empty($mat['name'])) {
                            DB::insert('material_usage', [
                                'report_id' => $reportId, // Actual ID
                                'material_name' => $mat['name'],
                                'quantity' => $mat['quantity'] ?? 0,
                                'comments' => $mat['comments'] ?? null
                            ]);
                        }
                    }

                    DB::commit();
                    break;

                case '5': // Notes
                    DB::update('daily_work_reports', [
                        'notes' => $_POST['notes'] ?? ''
                    ], "foreman_id = %i", $foremanId);
                    break;

                case '6': // Lock Report
                    if ($_SESSION['role_id'] !== '6') {
                        throw new Exception('Only superintendents can lock reports');
                    }

                    $signature = $_POST['signature'] ?? '';

                    $lockForm = isset($_POST['lockForm']) ? $_POST['lockForm'] : "0" ; // Capture signature data

                    DB::startTransaction();
                    DB::update('daily_work_reports', [
                        'is_locked' => $lockForm,
                        'signature_data' => $signature // Add signature data update
                    ], "id = %i", $reportId); // Fix condition to use primary key

                    DB::commit();
                    break;
            }

            echo json_encode([
                'success' => true,
                'message' => "Step $step saved successfully"
            ]);
            exit;
        }

        throw new Exception('Invalid step specified');
    } catch (MeekroDBException $e) {
        DB::rollback();
        error_log('DB Error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        DB::rollback();
        error_log('Error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
