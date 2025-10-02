<?php
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
    $username = $_SESSION['user_name'];
}
$jobs = DB::query("SELECT * FROM job");
$tools = DB::query("SELECT * FROM tools");
$crews = DB::query("SELECT * FROM crew");
$signature = DB::queryFirstRow(
    "SELECT * FROM application_signatures 
         WHERE user_id = %i",
    $user
);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Work Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #fd7e14;
            /* Orange */
            --secondary-color: #f8f9fa;
            /* Light gray */
        }

        body {
            background-color: white;
        }

        .form-section {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid var(--primary-color);
        }

        .section-title {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .step-progress {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
            padding: 0 10px;
        }

        .step-progress::before {
            content: '';
            position: absolute;
            top: 32px;
            /* adjust according to icon size */
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            z-index: 0;
        }

        .step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            text-align: center;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f1f1f1;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 8px;
        }

        .step.active .step-icon {
            background-color: #fff;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .step.completed .step-icon {
            background-color: var(--primary-color);
            color: #fff;
        }

        .step-label {
            font-size: 0.8rem;
            color: #6c757d;
            max-width: 80px;
            word-wrap: break-word;
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .step.completed .step-label {
            color: var(--primary-color);
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .step-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .btn-orange {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-orange:hover {
            background-color: #e67300;
            border-color: #e67300;
            color: white;
        }

        /* Compact form styles */
        .form-control,
        .form-select {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .form-label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .checkbox-item {
            min-width: 80px;
        }

        .form-check-input {
            margin-top: 0.15rem;
        }
    </style>
</head>

<body>
    <div class="container py-3">
        <h4 class="text-center mb-3"><?php echo lang("workform_Daily_Work_Report"); ?></h4>
        <form id="dailyReportForm">
            <!-- Step Progress Bar -->
            <div class="step-progress">
                <div class="step active" data-step="1">
                    <div class="step-icon"><i class="bi bi-info-circle"></i></div>
                    <div class="step-label"><?php echo lang("workform_Basic_Info"); ?></div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-icon"><i class="bi bi-people"></i></div>
                    <div class="step-label"><?php echo lang("workform_Work_Force"); ?></div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-icon"><i class="bi bi-tools"></i></div>
                    <div class="step-label"><?php echo lang("workform_Equipment"); ?></div>
                </div>

                <div class="step" data-step="4">
                    <div class="step-icon"><i class="bi bi-pen"></i></div>
                    <div class="step-label"><?php echo lang("workform_Materials"); ?></div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-icon"><i class="bi bi-journal-text"></i></div>
                    <div class="step-label"><?php echo lang("workform_Notes"); ?></div>
                </div>
                <div class="step" data-step="6">
                    <div class="step-icon"><i class="bi bi-pencil"></i></div>
                    <div class="step-label"><?php echo lang("workform_Signature"); ?></div>
                </div>

            </div>

            <!-- Step 1: Basic Information -->
            <div class="form-step active" data-step="1">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-info-circle"></i> <?php echo lang("workform_Basic_Information"); ?></h2>

                    <div class="row g-3">
                        <!-- Job Selection -->
                        <div class="col-md-6">
                            <label for="jobSelect" class="form-label"><?php echo lang("workform_job"); ?></label>
                            <select class="form-select" id="jobSelect" name="job_id">
                                <option value="" selected disabled><?php echo lang("workform_select_a_Job"); ?></option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id']; ?>">
                                        <?= $job['job_title'] . ' - ' . $job['job_address'] . ',' . $job['job_state']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <!-- Date Field -->
                        <div class="col-md-3">
                            <label for="reportDate" class="form-label"><?php echo lang("workform_Date"); ?></label>
                            <input type="date" class="form-control" id="reportDate" name="report_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>


                        <!-- Shift Selection -->
                        <div class="col-md-3">
                            <label for="shiftSelect" class="form-label"><?php echo lang("workform_shift"); ?></label>
                            <select class="form-select" id="shiftSelect" name="shift">
                                <option value="1"><?php echo lang("workform_Day"); ?></option>
                                <option value="2"><?php echo lang("workform_Night"); ?></option>
                            </select>
                        </div>

                        <!-- Crews Worked -->
                        <div class="col-12">
                            <label class="form-label"><?php echo lang("workform_Crews_Worked"); ?></label>
                            <div class="checkbox-group">
                                <?php foreach ($crews as $index => $crew): ?>
                                    <div class="form-check checkbox-item">
                                        <input class="form-check-input" type="radio"
                                            name="crew_id"
                                            id="crew<?= $index + 1 ?>"
                                            value="<?= $crew['crew_id'] ?>"
                                            <?= $index === 0 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="crew<?= $index + 1 ?>">
                                            <?= htmlspecialchars($crew['crew_name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($crews) === 0): ?>
                                    <div class="text-muted"><?php echo lang("workform_No crews available"); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Foreman -->
                        <div class="col-md-6">
                            <label for="foremanInput" class="form-label"><?php echo lang("Foreman"); ?></label>
                            <!-- Hidden input for foreman_id (user ID) -->
                            <input type="hidden" name="foreman_id" value="<?php echo $user; ?>">
                            <!-- Display username as read-only text -->
                            <input type="text" class="form-control" id="foremanInput" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        </div>

                        <!-- Weather -->
                        <div class="col-md-6">
                            <label for="weatherSelect" class="form-label"><?php echo lang("workform_Weather_Conditions"); ?></label>
                            <select class="form-select" id="weatherSelect" name="weather[]" multiple>
                                <option value="cloudy"><?php echo lang("workform_Cloudy"); ?></option>
                                <option value="rainy"><?php echo lang("workform_Rainy"); ?></option>
                                <option value="sunny"><?php echo lang("workform_Sunny"); ?></option>
                                <option value="hot"><?php echo lang("workform_Hot"); ?></option>
                                <option value="humid"><?php echo lang("workform_Humid"); ?></option>
                                <option value="snowy"><?php echo lang("workform_Snowy"); ?></option>
                                <option value="icy"><?php echo lang("workform_Icy"); ?></option>
                                <option value="windy"><?php echo lang("workform_Windy"); ?></option>
                            </select>
                            <div class="form-text"><?php echo lang("workform_Hold Ctrl/Cmd to select multiple"); ?></div>
                        </div>

                        <!-- Job Site Conditions -->
                        <div class="col-12">
                            <label class="form-label"><?php echo lang("workform_Job Site Conditions"); ?></label>
                            <div class="checkbox-group">
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" name="site_conditions[]" type="checkbox" id="condition1" value="Wet"> <label class="form-check-label" for="condition1"><?php echo lang("workform_Wet"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition2" value="Muddy">
                                    <label class="form-check-label" for="condition2"><?php echo lang("workform_Muddy"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition3" value="Pooled Water">
                                    <label class="form-check-label" for="condition3"><?php echo lang("workform_Pooled Water"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition4" value="Flooded">
                                    <label class="form-check-label" for="condition4"><?php echo lang("workform_Flooded"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition5" value="Ice">
                                    <label class="form-check-label" for="condition5"><?php echo lang("workform_Ice"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition6" value="Snow">
                                    <label class="form-check-label" for="condition6"><?php echo lang("workform_Snow"); ?></label>
                                </div>
                                <div class="form-check checkbox-item">
                                    <input class="form-check-input" type="checkbox" id="condition7" value="Good">
                                    <label class="form-check-label" for="condition7"><?php echo lang("workform_Good"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-navigation">
                    <div></div>
                    <button type="button" class="btn btn-orange next-step"><?php echo lang("workform_Next"); ?><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 2: Work Force -->
            <div class="form-step" data-step="2">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-people-fill"></i><?php echo lang("workform_Work Force"); ?></h2>

                    <div class="row g-3">
                        <!-- Craft Count -->
                        <div class="col-md-4">
                            <label for="craftCount" class="form-label"><?php echo lang("workform_Craft_Count"); ?></label>
                            <input type="number" class="form-control" name="craft_count" id="craftCount" min="0">
                        </div>

                        <!-- Subcontractors -->
                        <div class="col-12">
                            <label class="form-label"><?php echo lang("workform_Subcontractors"); ?></label>
                            <div id="subcontractorsContainer">
                                <div class="row dynamic-row g-2 align-items-end">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="subcontractors[][name]" placeholder="Subcontractor name">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="subcontractors[][headcount] placeholder=" Headcount" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2" id="addSubcontractor">
                                <i class="bi bi-plus"></i> <?php echo lang("workform_Add_Subcontractor"); ?>
                            </button>
                        </div>

                        <!-- Total Head Count -->
                        <div class="col-md-4">
                            <label for="totalHeadCount" class="form-label"><?php echo lang("workform_Total_Head_Count"); ?></label>
                            <input type="number" class="form-control" id="totalHeadCount" readonly>
                        </div>
                    </div>
                </div>

                <div class="step-navigation">
                    <button type="button" class="btn btn-outline-secondary prev-step"><i class="bi bi-arrow-left"></i><?php echo lang("workform_Previous"); ?></button>
                    <button type="button" class="btn btn-orange next-step"><?php echo lang("workform_Next"); ?> <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 3: Equipment -->
            <div class="form-step" data-step="3">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-tools"></i> <?php echo lang("workform_Equipment"); ?></h2>

                    <div id="equipmentContainer">
                        <div class="row dynamic-row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label"><?php echo lang("workform_Equipment_Name"); ?></label>
                                <select class="form-select" name="equipment[][tool_id]">
                                    <option value="" selected disabled>Select a tool</option>
                                    <?php foreach ($tools as $tool): ?>
                                        <option value="<?= $tool['tool_id']; ?>"><?= $tool['tool_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label"><?php echo lang("workform_Quantity"); ?></label>
                                <input type="number" name="equipment[][quantity]" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><?php echo lang("workform_Hours_Used"); ?></label>
                                <input type="number" name="equipment[][hours_used]" class="form-control" min="0" step="0.5">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?php echo lang("workform_Comments"); ?></label>
                                <input type="text" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-3" id="addEquipment">
                        <i class="bi bi-plus"></i> <?php echo lang("workform_Add_Equipment"); ?>
                    </button>
                </div>

                <div class="step-navigation">
                    <button type="button" class="btn btn-outline-secondary prev-step"><i class="bi bi-arrow-left"></i><?php echo lang("workform_Previous"); ?></button>
                    <button type="button" class="btn btn-orange next-step"><?php echo lang("workform_Next"); ?> <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 4: Notes -->
            <div class="form-step" data-step="4">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-box-seam"></i> <?php echo lang("workform_Materials"); ?></h2>

                    <div id="materialsContainer">
                        <div class="row dynamic-row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label"><?php echo lang("workform_Material_Name"); ?></label>
                                <input type="text" name="materials[][name]" class="form-control" placeholder="Name or select from inventory">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><?php echo lang("workform_Quantity"); ?></label>
                                <input type="number" name="materials[][quantity]" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><?php echo lang("workform_Comments"); ?></label>
                                <input type="text" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-3" id="addMaterial">
                        <i class="bi bi-plus"></i> <?php echo lang("workform_Add_Material"); ?>
                    </button>
                </div>

                <!-- In Step 4 -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-outline-secondary prev-step"><i class="bi bi-arrow-left"></i> <?php echo lang("workform_Previous"); ?></button>
                    <button type="button" class="btn btn-orange next-step"><?php echo lang("workform_Next"); ?> <i class="bi bi-arrow-right"></i></button>
                </div>
            </div>
            <!-- Step 5: Notes -->
            <div class="form-step" data-step="5">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-pencil"></i> <?php echo lang("workform_Notes"); ?></h2>
                    <textarea class="form-control notes-area" name="notes" placeholder="Enter any additional notes here..."></textarea>
                </div>

                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary prev-step">
                        <i class="bi bi-arrow-left"></i> <?php echo lang("workform_Previous"); ?>
                    </button>
                    <button type="button" class="btn btn-orange next-step">
                        <?php echo lang("workform_Next"); ?> <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
            <!-- Step 6: Signature -->
            <div class="form-step" data-step="6">
                <div class="form-section">
                    <h2 class="section-title"><i class="bi bi-pen-fill"></i><?php echo lang("workform_Signature"); ?></h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="superintendentName" class="form-label"><?php echo lang("workform_Superintendent"); ?></label>
                            <input type="text" class="form-control" id="superintendentName" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label"><?php echo lang("workform_Signature"); ?></label>
                            <div class="signature-pad" id="signaturePad"></div>
                            <?php if (!empty($signature['signature']) && strpos($signature['signature'], 'data:image/') === 0): ?>
                                <img src="<?= htmlspecialchars($signature['signature']) ?>" alt="Applicant Signature"
                                    style="background: transparent;">
                            <?php else: ?>
                                <!-- <div class="no-signature">No signature found</div> -->
                            <?php endif; ?>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lockForm" checked>
                                <label class="form-check-label" for="lockForm">
                                    <?php echo lang("workform_Lock form after submission (no further edits allowed)"); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary prev-step">
                        <i class="bi bi-arrow-left"></i><?php echo lang("workform_Previous"); ?>
                    </button>
                    <button type="submit" class="btn btn-orange">
                        <i class="bi bi-check-circle"></i> <?php echo lang("workform_Submit Report"); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Subcontractor Row
            document.getElementById('addSubcontractor')?.addEventListener('click', function() {
                const container = document.getElementById('subcontractorsContainer');
                const template = container.querySelector('.dynamic-row');
                const newRow = template.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                container.appendChild(newRow);
            });

            // Add Equipment Row
            document.getElementById('addEquipment')?.addEventListener('click', function() {
                const container = document.getElementById('equipmentContainer');
                const template = container.querySelector('.dynamic-row');
                const newRow = template.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelector('select').selectedIndex = 0;
                container.appendChild(newRow);
            });

            // Add Material Row
            document.getElementById('addMaterial')?.addEventListener('click', function() {
                const container = document.getElementById('materialsContainer');
                const template = container.querySelector('.dynamic-row');
                const newRow = template.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                container.appendChild(newRow);
            });

            // Delete Row Handler (using event delegation)
            function handleDeleteRow(e) {
                if (e.target.closest('.btn-outline-danger')) {
                    const container = e.currentTarget;
                    const rows = container.querySelectorAll('.dynamic-row');
                    if (rows.length <= 1) {
                        alert('At least one row must remain.');
                        return;
                    }
                    const row = e.target.closest('.dynamic-row');
                    row && row.remove();
                }
            }

            // Attach delete handlers to all containers
            document.getElementById('subcontractorsContainer')?.addEventListener('click', handleDeleteRow);
            document.getElementById('equipmentContainer')?.addEventListener('click', handleDeleteRow);
            document.getElementById('materialsContainer')?.addEventListener('click', handleDeleteRow);

            // Step navigation functionality
            const steps = document.querySelectorAll('.step');
            const formSteps = document.querySelectorAll('.form-step');
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            let currentStep = 1;

            // Initialize the form
            updateStepIndicator();

            // Next button click handler
            nextButtons.forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    if (!validateStep(currentStep)) return;

                    try {
                        const result = await saveStepProgress(currentStep);
                        if (result.success) {
                            navigateToStep(currentStep + 1);
                        } else {
                            throw new Error(result.message || 'Failed to save step progress');
                        }
                    } catch (error) {
                        console.error('Error saving step:', error);
                        alert('Error: ' + error.message);
                    }
                });
            });

            // Previous button click handler
            prevButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    navigateToStep(currentStep - 1);
                });
            });

            // Step header click handler
            steps.forEach(step => {
                step.addEventListener('click', function() {
                    const stepNumber = parseInt(this.getAttribute('data-step'));
                    if (stepNumber < currentStep) {
                        navigateToStep(stepNumber);
                    }
                });
            });

            // Form submission
            const form = document.getElementById('dailyReportForm');
            form?.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (currentStep !== steps.length) return;
                if (!validateStep(currentStep)) return;

                try {


                    const formData = new FormData(form);
                    formData.append('save_step', currentStep);

                    formData.append('is_final_submission', '1');

                    const response = await fetch('ajax_helpers/handle_daily_forms.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.message || 'Submission failed');
                    }

                    window.location.href = 'index.php?route=modules/craftman/thanks2';
                } catch (error) {
                    console.error('Submission error:', error);
                    alert('Error: ' + error.message);
                }
            });

            // Save step progress
            async function saveStepProgress(step) {
                const formData = new FormData(document.getElementById('dailyReportForm'));
                formData.append('save_step', step);

                // Handle dynamic rows
                document.querySelectorAll('.dynamic-row').forEach((row, index) => {
                    row.querySelectorAll('input, select').forEach(field => {
                        const name = field.name.replace('[]', `[${index}]`);
                        formData.append(name, field.value);
                    });
                });

                const response = await fetch('ajax_helpers/handle_daily_forms.php', {
                    method: 'POST',
                    body: formData
                });

                return await response.json();
            }

            // Navigate to step
            function navigateToStep(stepNumber) {
                if (stepNumber < 1 || stepNumber > steps.length) return;
                currentStep = stepNumber;
                updateStepIndicator();

                formSteps.forEach(step => {
                    step.classList.remove('active');
                    if (parseInt(step.getAttribute('data-step')) === currentStep) {
                        step.classList.add('active');
                    }
                });
            }

            // Update step indicators
            function updateStepIndicator() {
                steps.forEach((step, index) => {
                    const stepNumber = index + 1;
                    step.classList.remove('active', 'completed');
                    if (stepNumber < currentStep) {
                        step.classList.add('completed');
                    } else if (stepNumber === currentStep) {
                        step.classList.add('active');
                    }
                });
            }

            // Validate step fields
            function validateStep(stepIndex) {
                const currentStepElement = formSteps[stepIndex - 1];
                let isValid = true;

                currentStepElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                currentStepElement.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                currentStepElement.querySelectorAll('[required]').forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'This field is required';
                        field.after(feedback);
                    }
                });

                if (stepIndex === 1) {
                    const crewSelected = document.querySelector('[name="crew_id"]:checked');
                    if (!crewSelected) {
                        isValid = false;
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = 'Please select a crew';
                        document.querySelector('.checkbox-group')?.after(feedback);
                    }
                }

                return isValid;
            }
        });
    </script>


</body>

</html>