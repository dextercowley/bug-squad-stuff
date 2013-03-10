# type 1 created issues
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 1, i.jc_issue_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issues AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3;

# type 2 - comments
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 2, i.response_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issue_responses AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3;

# type 3 Insert status change activity
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 3, i.change_id, u.id, i.jc_issue_id, i.change_date
FROM jos_code_tracker_issue_changes AS i
JOIN jos_users AS u
ON i.change_by = u.id
WHERE tracker_id = 3;

# 4 - tests done
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 4, i.response_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issue_responses AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3
AND i.body LIKE '%@test%';

# type 5 Patch files
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 5, i.file_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issue_files AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3
AND i.name LIKE '%patch%' OR i.name LIKE '%diff%';

# type 6 Pull requests in a comment
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 6, i.response_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issue_responses AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3
AND (i.body LIKE '%/pull%' OR i.body LIKE '%/compare/%' OR i.body LIKE '%.diff');

# type 7 Pull requests in original bug report
INSERT INTO jos_code_activity_detail (activity_type, activity_xref_id, user_id, jc_issue_id, activity_date)
SELECT 7, i.jc_issue_id, u.id, i.jc_issue_id, i.created_date
FROM jos_code_tracker_issues AS i
JOIN jos_users AS u
ON i.created_by = u.id
WHERE tracker_id = 3
AND (description LIKE '%/pull%' OR description LIKE '%/compare/%' OR description LIKE '%.diff');

SELECT activity_type AS type, count(*)
FROM jos_code_activity_detail
GROUP BY activity_type