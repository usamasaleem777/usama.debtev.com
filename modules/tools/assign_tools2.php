<?php
$jobs = DB::query("SELECT id, job_title FROM job");
$tools = DB::query("SELECT tool_id, tool_name, quantity FROM tools");
?>

<div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
    <div style="margin-top: 10px;">
        <ol class="breadcrumb float-sm-right mt-2">
            <li class="breadcrumb-item">
                <a href="index.php" style="color: #fe5500">
                    <i class="fas fa-home me-1"></i>Job Assignment
                </a>
            </li>
            <li class="breadcrumb-item active">Assign Tools to Job</li>
        </ol>
    </div>
</div>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid pt-4">

            <!-- Page Header -->


            <!-- Assignment Form -->
            <div class="row1">
                <form method="POST" action="index.php?route=modules/tools/processJobAssignment">
                    <div id="job-assignments-wrapper">
                        <!-- First Job Assignment Block -->
                        <div class="job-assignment-block mb-4 border p-3 rounded shadow-sm bg-white">

                            <!-- Select Job -->
                            <?php $selected_job_id = isset($_GET['id']) ? $_GET['id'] : null; ?>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Select Job</label>
                                    <select name="job_id[]" class="form-select" required>
                                        <option value="">-- Select Job --</option>
                                        <?php foreach ($jobs as $job): ?>
                                            <option value="<?= $job['id'] ?>"
                                                <?= $selected_job_id == $job['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($job['job_title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Tools Section -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Select Tools & Assign Quantities</label>

                                    <!-- Scrollable Tool Container -->
                                    <div style="max-height: 400px; overflow-y: auto;" class="tool-list">
                                        <?php foreach ($tools as $tool): ?>
                                            <div class="tool-row p-2 mb-3 border rounded bg-light">
                                                <div class="row align-items-center">
                                                    <div class="col-2 col-md-1 text-center">
                                                        <input type="checkbox" name="tools[<?= $tool['tool_id'] ?>][selected][]" value="1"
                                                            class="form-check-input mt-0 mt-md-2">
                                                    </div>
                                                    <div class="col-10 col-md-3 mb-2 mb-md-0">
                                                        <label class="form-label d-block d-md-none small">Tool Name</label>
                                                        <input type="text" class="form-control bg-light"
                                                            value="<?= htmlspecialchars($tool['tool_name']) ?>" readonly>
                                                    </div>
                                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                                        <label class="form-label d-block d-md-none small">Available Qty</label>
                                                        <input type="text" class="form-control bg-light"
                                                            value="<?= $tool['quantity'] ?>" readonly>
                                                    </div>
                                                    <div class="col-6 col-md-5">
                                                        <label class="form-label d-block d-md-none small">Assign Qty</label>
                                                        <input type="number" class="form-control"
                                                            name="tools[<?= $tool['tool_id'] ?>][quantity][]"
                                                            placeholder="Enter quantity" min="0" max="<?= $tool['quantity'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Another Job Button -->
                    <!-- <div class="text-start mb-3">
                        <button type="button" class="btn btn-outline-primary w-30 w-md-auto" onclick="addAnotherJob()">
                            + Add Another Job
                        </button>
                    </div> -->

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success w-30 w-md-auto"
                            style="background: linear-gradient(45deg, #FE5505, #FF8E53); color: white; border: none;">
                            Assign Tools
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Clone Template Script -->
<script>
    function addAnotherJob() {
        const wrapper = document.getElementById('job-assignments-wrapper');
        const firstBlock = wrapper.querySelector('.job-assignment-block');
        const clone = firstBlock.cloneNode(true);

        // Clear inputs in cloned block
        clone.querySelectorAll('select, input[type="number"], input[type="checkbox"]').forEach(input => {
            if (input.tagName === 'SELECT') input.selectedIndex = 0;
            else input.value = '';
            if (input.type === 'checkbox') input.checked = false;
        });

        // Add remove button if not already present
        let removeBtn = clone.querySelector('.remove-job-btn');
        if (!removeBtn) {
            const removeDiv = document.createElement('div');
            removeDiv.className = 'text-end mb-2';

            removeDiv.innerHTML = `
            <button type="button" class="btn btn-sm btn-danger remove-job-btn" onclick="removeJobBlock(this)">
                ❌ Remove Job
            </button>
        `;
            clone.prepend(removeDiv);
        }

        wrapper.appendChild(clone);
    }

    function removeJobBlock(button) {
        const block = button.closest('.job-assignment-block');
        const wrapper = document.getElementById('job-assignments-wrapper');
        if (wrapper.querySelectorAll('.job-assignment-block').length > 1) {
            block.remove();
        } else {
            alert("You must have at least one job assignment block.");
        }
    }
</script>
<style>
    @media (max-width: 768px) {

        .tool-row {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
        }

    }

    /* Medium screens */
    @media (min-width: 769px) and (max-width: 992px) {
        .tool-row {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
        }
    }
</style>