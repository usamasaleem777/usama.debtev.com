<?php
if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
    $username = $_SESSION['user_name'];

    // Fetch all jobs with all columns
    $jobs = DB::query("SELECT * FROM job");

    // Fetch all tools with all columns
    $tools = DB::query("SELECT * FROM tools");

    // Fetch all crews with all columns
    $crews = DB::query("SELECT * FROM crew");

    if (isset($_GET['id'])) {
        $Id = $_GET['id'];

    } else {
        $Id = 0;
    }
    // Fetch daily report with all columns
    $daily_report = DB::queryFirstRow("SELECT * FROM daily_work_reports WHERE id = %i", $Id);

    // Fetch subcontractors with all columns
    $subcontractors = DB::query("SELECT * FROM subcontractors WHERE report_id = %i", $Id);

    // Fetch equipment usage with all columns
    $equipment = DB::query("SELECT * FROM equipment_usage WHERE report_id = %i", $Id);

    // Fetch material usage with all columns
    $material = DB::query("SELECT * FROM material_usage WHERE report_id = %i", $Id);

    // Fetch signature data with all columns
    $signature = DB::queryFirstRow("SELECT * FROM daily_work_reports WHERE id = %i", $Id);


}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #e74c3c;
        --light-gray: #ecf0f1;
        --dark-gray: #7f8c8d;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 10px;
        background-color: #f5f5f5;
        color: #333;
        line-height: 1.4;
        font-size: 13px;
    }

    .report-container {
        max-width: 1100px;
        margin: 0 auto;
        background-color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 3px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 12px;
    }

    .orders-table,
    .equipment-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .orders-table th {
        font-weight: 600;
    }

    .first-th {
        background-color: #FF5500;
        color: white;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
    }

    .form-input {
        width: 100%;
        background-color: var(--light-gray);
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-family: inherit;
        font-size: 12px;
    }

    .dotted-lines-input {
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        padding: 0 6px;
    }

    .dotted-line {
        border-bottom: 1px dashed #999;
        padding: 5px 0;
        margin: 0;
    }

    .dotted-line:last-child {
        border-bottom: none;
    }

    .form-input-dotted {
        width: 100%;
        border: none;
        background: var(--secondary-gray);
        padding: 3px;
        font-size: 12px;
        outline: none;
    }

    .form-input-dotted:focus {
        background-color: var(--secondary-gray);
    }

    .form-input:focus {
        outline: none;
    }

    input[type="date"].form-input,
    input[type="time"].form-input {
        height: 28px;
    }

    th,
    td {
        padding: 6px 8px;
        text-align: left;
    }

    th {
        background-color: var(--light-gray);
        color: var(--primary-color);
        font-weight: 600;
    }

    .section-title {
        background-color: Black;
        color: white;
        text-align: center;
        font-weight: 600;
        margin: 12px 0 10px 0;
        font-size: 14px;
        border-bottom: 1px solid #eee;
        padding-bottom: 2px;
    }

    .section-block {
        margin-bottom: 20px;
    }

    .work-description {
        line-height: 1.4;
        margin-bottom: 7px;
        font-size: 12px;
    }

    .work-items {
        padding-left: 15px;
        margin: 0;
    }

    .work-items li {
        margin-bottom: 6px;
        position: relative;
    }

    hr {
        border: none;
        border-top: 1px solid #ddd;
        margin: 15px 0;
    }

    .footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        align-items: center;
    }

    .weather {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    .signature {
        width: 180px;
        border-top: 1px solid #333;
        padding-top: 3px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        float: right;
        font-size: 12px;
    }

    .button-group {
        text-align: center;
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn {
        padding: 6px 15px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }

    .btn-print {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-save {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-clear {
        background-color: var(--dark-gray);
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .btn:active {
        transform: translateY(0);
    }

    @media print {
        .button-group {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .report-container {
            padding: 10px;
        }

        table {
            font-size: 11px;
        }

        .footer {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .button-group {
            flex-direction: column;
            gap: 8px;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
</head>

<body>
    <div class="report-container">
        <div class="report-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="margin: 0; font-size: 24px; color: rgb(0, 0, 0); text-decoration: none; border-bottom: none;">
                DAILY WORK REPORT</h1>
            <img src="https://corsproxy.io/?https://craftgc.com/wp-content/uploads/2025/04/logo-MAHAM-11.png"
                alt="Company Logo" crossorigin="anonymous" style="max-width: 250px; height: auto;">
        </div>
        <hr style="color: rgb(0, 0, 0); height: 3px; background-color:rgba(0, 0, 0, 0.97); margin-top: -3px;">
        <table>
            <!-- row1 -->
            <tr>
                <th class="first-th" colspan="2">CREW</th>
                <th class="first-th">JOB NUMBER</th>
                <th class="first-th">JOB LOCATION</th>
                <th class="first-th">DATE</th>
                <th class="first-th">REPORT NO</th>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($daily_report['crew_id']) ? htmlspecialchars($daily_report['crew_id']) : 'NA' ?>">
                </td>
                <td>
                    <input type="text" class="form-input" readonly value="<?php
                    if (!empty($daily_report['crew_id'])) {
                        $crew_info = DB::queryFirstRow("SELECT crew_name FROM crew WHERE crew_id = %i", $daily_report['crew_id']);
                        echo htmlspecialchars($crew_info['crew_name'] ?? 'NA');
                    } else {
                        echo 'NA';
                    }
                    ?>">
                </td>
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($daily_report['job_id']) ? htmlspecialchars($daily_report['job_id']) : 'NA' ?>">
                </td>
                <td>
                    <input type="text" class="form-input" readonly value="<?php
                    if (!empty($daily_report['job_id'])) {
                        $job_info = DB::queryFirstRow("SELECT job_state FROM job WHERE id = %i", $daily_report['job_id']);
                        echo htmlspecialchars($job_info['job_state'] ?? 'NA');
                    } else {
                        echo 'NA';
                    }
                    ?>">
                </td>
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($daily_report['report_date']) ? htmlspecialchars($daily_report['report_date']) : 'NA' ?>">
                </td>
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($Id) ? htmlspecialchars($Id) : 'NA' ?>">
                </td>
            </tr>
            <!-- ROW1 End -->
            <!-- ROW2 Start -->
            <tr>
                <th class="first-th">EMPLOYEE</th>
                <th class="first-th">MANAGER</th>
                <th class="first-th">SHIFT</th>
                <th class="first-th" colspan="2">WORKFORCE [ Craftsman : Subcontractors ]</th>
                <th class="first-th">TOTAL HEAD COUNT</th>
            </tr>
            <tr>
                <!-- Employee (already done) -->
                <td><input type="text" class="form-input" readonly
                        value="<?= !empty($username) ? htmlspecialchars($username) : 'NA' ?>">
                </td>

                <!-- Manager (already done) -->
                <td>
                    <input type="text" class="form-input" readonly value="<?php
                    if (!empty($user)) {
                        $user_info = DB::queryFirstRow("SELECT manager FROM users WHERE user_id = %i", $user);
                        echo htmlspecialchars($user_info['manager'] ?? 'NA');
                    } else {
                        echo 'NA';
                    }
                    ?>">
                </td>

                <!-- Shift (new) -->
                <td>
                    <input type="text" class="form-input" readonly value="<?php
                    if (!empty($daily_report['shift'])) {
                        echo $daily_report['shift'] == 1 ? 'Day' : ($daily_report['shift'] == 2 ? 'Night' : 'NA');
                    } else {
                        echo 'NA';
                    }
                    ?>">
                </td>

                <!-- Craft Count (new) -->
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($daily_report['craft_count']) ? htmlspecialchars($daily_report['craft_count']) : 'NA' ?>">
                </td>

                <!-- Subcontractors Count (new) -->
                <td>
                    <input type="text" class="form-input" readonly value="<?php
                    if (!empty($Id)) {
                        $subcontractor_count = DB::queryFirstField("SELECT COUNT(*) FROM subcontractors WHERE report_id = %i", $Id);
                        echo htmlspecialchars($subcontractor_count ?? '0');
                    } else {
                        echo 'NA';
                    }
                    ?>">
                </td>

                <!-- Total Head Count (new) -->
                <td>
                    <input type="text" class="form-input" readonly
                        value="<?= !empty($daily_report['total_head_count']) ? htmlspecialchars($daily_report['total_head_count']) : 'NA' ?>">
                </td>
            </tr>
            <!-- ROW2 End -->

        </table>
        <!-- ROW3 START -->
        <div class="section-title">EQUIPMENT & MATERIAL USAGE</div>
        <table class="equipment-table">
            <tr>
                <th class="first-th">EQUIPMENT NAME</th>
                <th class="first-th">QUANTITY</th>
                <th class="first-th">HOURS USAGE #</th>
                <th class="first-th">MATERIAL NAME</th>
                <th class="first-th">QUANTITY</th>
                <th class="first-th">NOTES</th>
            </tr>
            <?php
            // Get all equipment records with tool names
            $equipment = DB::query("SELECT eu.*, t.tool_name 
                           FROM equipment_usage eu
                           LEFT JOIN tools t ON eu.tool_id = t.tool_id
                           WHERE eu.report_id = %i", $Id);

            // Get all material records
            $materials = DB::query("SELECT * FROM material_usage WHERE report_id = %i", $Id);

            // Determine max rows to show (at least 2 empty rows if no data)
            $max_rows = max(2, count($equipment), count($materials));

            for ($i = 1; $i < $max_rows; $i++): // Start from 1 because first entry in db is empty/faulty
                $equipment_row = $equipment[$i] ?? null;
                $material_row = $materials[$i] ?? null;
                ?>
                <tr>
                    <!-- Equipment Name (from joined tools table) -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($equipment_row['tool_name']) ? htmlspecialchars($equipment_row['tool_name']) : 'NA' ?>">
                    </td>

                    <!-- Equipment Quantity -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($equipment_row['quantity']) ? htmlspecialchars($equipment_row['quantity']) : 'NA' ?>">
                    </td>

                    <!-- Hours Usage -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($equipment_row['hours_used']) ? htmlspecialchars($equipment_row['hours_used']) : 'NA' ?>">
                    </td>

                    <!-- Material Name -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($material_row['material_name']) ? htmlspecialchars($material_row['material_name']) : 'NA' ?>">
                    </td>

                    <!-- Material Quantity -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($material_row['quantity']) ? htmlspecialchars($material_row['quantity']) : 'NA' ?>">
                    </td>

                    <!-- Material Notes -->
                    <td>
                        <input type="text" class="form-input" readonly
                            value="<?= !empty($material_row['comments']) ? htmlspecialchars($material_row['comments']) : 'NA' ?>">
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
        <!-- ROW3 END -->

        <!-- ROW4 START -->
        <div class="section-title">WEATHER & SITE CONDITIONS</div>
        <table class="orders-table">
            <tr>
                <th class="first-th">SITE CONDITION</th>
                <th class="first-th">WEATHER CONDITION</th>
            </tr>
            <tr>
                <!-- Site Conditions -->
                <td style="vertical-align: middle;">
                    <div style="font-size: 14px; padding: 6px 0;">
                        <?php
                        if (!empty($daily_report['site_conditions'])) {
                            // Format: "Muddy,Flooded,Snow" → "Muddy, Flooded, Snow"
                            $formatted = str_replace(',', ', ', $daily_report['site_conditions']);
                            echo htmlspecialchars($formatted);
                        } else {
                            echo 'NA';
                        }
                        ?>
                    </div>
                </td>

                <!-- Weather Conditions -->
                <td style="vertical-align: middle;">
                    <div style="font-size: 14px; padding: 6px 0;">
                        <?php
                        if (!empty($daily_report['weather_conditions'])) {
                            $weather = json_decode($daily_report['weather_conditions'], true);
                            if ($weather) {
                                echo '<div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">';

                                // Temperature with icon
                                echo '<div style="display: flex; align-items: center; gap: 4px;">';
                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#fd7e14"><path d="M12 22c1.1 0 2-.9 2-2v-1h-4v1c0 1.1.9 2 2 2zm6-6v-5c0-3.1-2-5.6-4.8-6.3V3.5c0-.8-.7-1.5-1.5-1.5s-1.5.7-1.5 1.5v1.2C8 5.4 6 7.9 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>';
                                echo '<span style="font-size: 14px;">' . htmlspecialchars($weather['temperature'] ?? 'NA') . '°C</span>';
                                echo '</div>';

                                // Humidity with icon
                                echo '<div style="display: flex; align-items: center; gap: 4px;">';
                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#3498db"><path d="M12 2c-5.3 0-8 6-8 6s2.7 6 8 6 8-6 8-6-2.7-6-8-6zm0 10c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
                                echo '<span style="font-size: 14px;">' . htmlspecialchars($weather['humidity'] ?? 'NA') . '%</span>';
                                echo '</div>';

                                // Wind with icon
                                echo '<div style="display: flex; align-items: center; gap: 4px;">';
                                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#7f8c8d"><path d="M12 6c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm5-4h2v11h-2zm3.6 12.6c-.8-.8-2-.8-2.8 0-.8.8-.8 2 0 2.8l3.7 3.7c.4.4 1 .4 1.4 0 .4-.4.4-1 0-1.4l-3.7-3.7zM12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm5 8h2v7h-2z"/></svg>';
                                echo '<span style="font-size: 14px;">' . htmlspecialchars($weather['wind_speed'] ?? 'NA') . ' kph</span>';
                                echo '</div>';

                                // Condition with weather icon
                                echo '<div style="display: flex; align-items: center; gap: 4px;">';
                                if (!empty($weather['icon'])) {
                                    echo '<img src="' . htmlspecialchars($weather['icon']) . '" width="16" height="16" alt="Weather icon">';
                                } else {
                                    echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="#f1c40f"><path d="M12 2L4 12l8 10 8-10z"/></svg>';
                                }
                                echo '<span style="font-size: 14px;">' . htmlspecialchars($weather['condition'] ?? 'NA') . '</span>';
                                echo '</div>';

                                echo '</div>';
                            } else {
                                echo '<span style="font-size: 14px;">NA (Invalid weather data)</span>';
                            }
                        } else {
                            echo '<span style="font-size: 14px;">NA</span>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
        <!-- ROW4 END -->

        <div class="section-title">WORK PERFORMED TODAY</div>
        <div class="section-block">
            <div class="work-description">
                <ul class="work-items">
                    <li>5:00 am - Skeleton Crew on site. Conducted stretch & flex, Morning PTP, addressed the crews.
                        Brought out Ider & Rubin's Crews</li>
                    <li>9:00 am - Juan Sanchez identified that the South end is 2 inches too short (too narrow)</li>
                    <li>3:43 pm - Turner Safety alerted us that there was lightning 20 miles out</li>
                    <li>4:25 pm - Lightning within 10 miles, Turner Safety advised that we should expect to stand down
                    </li>
                    <li>4:45 pm - Lightning Stand down, we rolled up and moved the materials from the equipment yard
                    </li>
                    <li>Completed assembly of 20.5 Arches (still need to add the baseplates)</li>
                    <li>Approximately 2/3rds of the cable bracing was preassembled, sorted, and palletized</li>
                </ul>
            </div>
        </div>
        <table>
            <tr>
                <th class="first-th">DELAYS</th>
                <th class="first-th">EXTRA WORK</th>
                <th class="first-th">BACKCHARGES</th>
                <th class="first-th">CHANGES</th>
            </tr>
            <tr>
                <td><input type="text" class="form-input"></td>
                <td><input type="text" class="form-input"></td>
                <td><input type="text" class="form-input"></td>
                <td><input type="text" class="form-input"></td>
            </tr>
        </table>

        <div class="section-title">ITEMS REQUIRING FOLLOW UP</div>
        <div class="section-block">
            <div class="dotted-lines-input" id="follow-up">
                <div class="dotted-line"><input type="text" class="form-input-dotted"></div>
                <div class="dotted-line"><input type="text" class="form-input-dotted"></div>
                <div class="dotted-line"><input type="text" class="form-input-dotted"></div>
                <div class="dotted-line"><input type="text" class="form-input-dotted"></div>
                
            </div>
        </div>

        <div class="footer">
            <div class="weather">
            </div>
            <div class="signature">
                <span>Prepared by:</span>
                <input type="text" class="form-input" id="signature" style="width: 120px;">
            </div>
        </div>

        <div class="button-group">
            <button id="print-btn" class="btn btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z" />
                    <path
                        d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- html2pdf.js for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const printBtn = document.getElementById('print-btn');
            const buttonGroup = document.querySelector('.button-group');

            // Preload the logo image to ensure it's available
            const preloadImage = new Image();
            preloadImage.crossOrigin = "Anonymous";
            preloadImage.src = "https://corsproxy.io/?https://craftgc.com/wp-content/uploads/2025/04/logo-MAHAM-11.png";

            printBtn.addEventListener('click', async function () {
                buttonGroup.style.display = 'none';

                // Show loading indicator
                const loadingAlert = Swal.fire({
                    title: 'Generating PDF',
                    html: 'Preparing document...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    // Wait for images to load
                    await new Promise(resolve => {
                        if (document.images.length > 0) {
                            let loaded = 0;
                            Array.from(document.images).forEach(img => {
                                if (img.complete) {
                                    loaded++;
                                } else {
                                    img.addEventListener('load', () => {
                                        loaded++;
                                        if (loaded === document.images.length) resolve();
                                    });
                                    img.addEventListener('error', () => {
                                        loaded++;
                                        if (loaded === document.images.length) resolve();
                                    });
                                }
                            });
                            if (loaded === document.images.length) resolve();
                        } else {
                            resolve();
                        }
                    });

                    // Generate PDF
                    const element = document.querySelector('.report-container');
                    const opt = {
                        filename: `Daily_Work_Report_${new Date().toISOString().slice(0, 10)}.pdf`,
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            allowTaint: false,
                            logging: false,
                            scrollX: 0,
                            scrollY: 0,
                            windowWidth: element.scrollWidth,
                            windowHeight: element.scrollHeight
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    };

                    await html2pdf().set(opt).from(element).save();

                    loadingAlert.close();
                    Swal.fire({
                        title: 'Success!',
                        text: 'PDF downloaded successfully',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                } catch (err) {
                    console.error('PDF generation error:', err);
                    loadingAlert.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to generate PDF. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                } finally {
                    buttonGroup.style.display = '';
                }
            });
        });
    </script>
</body>

</html>