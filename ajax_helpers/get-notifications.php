<?php
require('../functions.php');

// Unread from applicants
$unreadApplicant = DB::query("
  SELECT first_name, last_name, created_at, 'applicant' AS source
  FROM applicants
  WHERE is_read = 0
  ORDER BY created_at DESC
");

// Unread from craft_contracting + employment_eligibility_verification + applicants
$unreadCraft = DB::query("
  SELECT a.first_name, a.last_name, c.created_at, 'craft' AS source
  FROM craft_contracting c
  JOIN applicants a ON c.email = a.email
  JOIN employment_eligibility_verification eev ON eev.id = c.id
  WHERE c.is_read = 0
    AND eev.ssn IS NOT NULL
    AND eev.ssn != ''
  ORDER BY c.created_at DESC
");

// Read (latest 5) from applicants
$readApplicant = DB::query("
  SELECT first_name, last_name, created_at, 'applicant' AS source
  FROM applicants
  WHERE is_read = 1
  ORDER BY created_at DESC
  LIMIT 5
");

// Read (latest 5) from craft_contracting + employment_eligibility_verification + applicants
$readCraft = DB::query("
  SELECT a.first_name, a.last_name, c.created_at, 'craft' AS source
  FROM craft_contracting c
  JOIN applicants a ON c.email = a.email
  JOIN employment_eligibility_verification eev ON eev.id = c.id
  WHERE c.is_read = 1
    AND eev.ssn IS NOT NULL
    AND eev.ssn != ''
  ORDER BY c.created_at DESC
  LIMIT 5
");

// Merge and sort
$unread = array_merge($unreadApplicant, $unreadCraft);
$read = array_merge($readApplicant, $readCraft);

// Sort by date descending
usort($unread, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
usort($read, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

echo json_encode([
  'unread_count' => count($unread),
  'unread' => $unread,
  'read' => $read
]);
