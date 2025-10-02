<?php
if (isset($_POST['formtype'])) {
    @extract($_POST);

    if ($formtype == "edituser") {
        // Update user data
        DB::update("users", array(
            'user_name' => $user_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $user_email,
            'user_phone' => $user_phone,
            'password' => $password,
            'role_id' => $role_id,
            'manager_id' => $manager_id,
            'designation_id' => $designation_id,
            'joining_date' => $joining_date,
            'departure_date' => $departure_date,
            'basic_salary' => $basic_salary,
            'hourly_rate' => $hourly_rate,
            'per_word_rate' => $per_word_rate,
            'per_post_rate' => $per_post_rate,
            'auth_code' => $auth_code,
            'user_avatar_url' => $user_avatar_url,
            'last_modified_by' => $_SESSION['name'],
            'updated_at' => date("Y-m-d H:i:s"),
        ), 'user_id=%s', $user_id);

        // Update role in user_roles table
        DB::update("user_roles", array(
            'role_id' => $role_id,
            'assigned_at' => date("Y-m-d H:i:s")
        ), 'user_id=%s', $user_id);

        if (isset($btnSubmitClose)) {
            echo '<script type="text/javascript">window.location = "index.php?route=modules/users/view_agents";</script>';
        } else {
            echo '<script type="text/javascript">window.location = "index.php?route=modules/users/edituser&user_id=' . $user_id . '";</script>';
        }
    } elseif ($formtype == "createuser") {

        $user_name_count = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE email = %s", $user_email);
        $kioskID_exists_users = DB::queryFirstField("SELECT COUNT(*) FROM users WHERE kioskID = %s", $kioskID);
        if ($kioskID_exists_users) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
    Swal.fire({
        icon: "error",
        title: "Duplicate KIOSK ID",
        text: "KIOSK ID already exists. Please use a unique KIOSK ID.",
        confirmButtonColor: "#fe5500"
    }).then(() => {
        window.history.back();
    });
</script>';
            exit;
        }

        if ($user_name_count != 0) {
            die("User email already exists.");
        }

        // Start transaction for data consistency
        DB::startTransaction();

        try {
            // Insert into users table
            DB::insert("users", array(
                'user_name' => $user_name,
                'name' => $first_name . ' ' . $last_name,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $user_email,
                'phone' => $user_phone,
                'password' => $password, // ⚠️ Storing plain text password (UNSAFE!)
                'role_id' => $role_id,
                'kioskID' => $kioskID,
                'picture' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ));

            $user_id = DB::insertId();
            $applicant_exists = DB::queryFirstField("SELECT COUNT(*) FROM applicants WHERE email = %s", $user_email);
            if (!$applicant_exists) {
                // Insert into applicants table if not already present
                DB::insert("applicants", array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user_email,
                    'phone_number' => $user_phone,
                    'kioskID' => $kioskID,
                    'created_at' => date("Y-m-d H:i:s"),
                    'user_id' => $user_id,
                ));
                $applicant_id = DB::insertId();

                DB::insert("craft_contracting", array(
                    'id' => $applicant_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user_email,
                    'phone_number' => $user_phone,
                ));
            } else {
                // Update existing applicant data
                DB::update("applicants", array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone_number' => $user_phone,
                    'kioskID' => $kioskID,
                    'created_at' => date("Y-m-d H:i:s"),
                ), 'email=%s', $user_email);
                $applicant_data = DB::queryFirstRow("SELECT * FROM applicants WHERE email = %s", $user_email);
                $applicant_id = $applicant_data['id'];

                DB::update("craft_contracting", array(
                    'id' => $applicant_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $user_email,
                    'phone_number' => $user_phone,
                ), 'email=%s', $user_email);
            }

            // Insert into user_roles table
            DB::insert("user_roles", array(
                'user_id' => $user_id,
                'role_id' => $role_id,
                'assigned_at' => date("Y-m-d H:i:s")
            ));

            // Commit transaction
            DB::commit();

            if (isset($btnSubmitClose)) {
                echo '<script type="text/javascript">window.location = "index.php?route=modules/users/view_users&success=User+created+successfully";</script>';
            } else {
                echo '<script type="text/javascript">window.location = "index.php?route=modules/users/edituser&user_id=' . $user_id . '";</script>';
            }
        } catch (Exception $e) {
            // Rollback on error
            DB::rollback();
            die("Error creating user: " . $e->getMessage());
        }
    } else {
        die("Form type not recognized by system.");
    }
} else {
    die("No data posted.");
}