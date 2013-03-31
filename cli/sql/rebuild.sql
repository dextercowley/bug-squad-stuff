# type 1 created issues
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 1, i.jc_issue_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issues AS i
WHERE tracker_id = 3;

# type 2 - comments
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 2, i.response_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issue_responses AS i
WHERE tracker_id = 3;

# type 3 Insert status change activity
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 3, i.change_id, i.jc_change_by, i.jc_issue_id, i.change_date
FROM eem_code_tracker_issue_changes AS i
WHERE tracker_id = 3;

# 4 - tests done
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 4, i.response_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issue_responses AS i
WHERE tracker_id = 3
AND i.body LIKE '%@test%';

# type 5 Patch files
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 5, i.file_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issue_files AS i
WHERE tracker_id = 3
AND i.name LIKE '%patch%' OR i.name LIKE '%diff%';

# type 6 Pull requests in a comment
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 6, i.response_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issue_responses AS i
WHERE tracker_id = 3
AND (i.body LIKE '%/pull%' OR i.body LIKE '%/compare/%' OR i.body LIKE '%.diff');

# type 7 Pull requests in original bug report
INSERT INTO eem_code_activity_detail (activity_type, activity_xref_id, jc_user_id, jc_issue_id, activity_date)
SELECT 7, i.jc_issue_id, i.jc_created_by, i.jc_issue_id, i.created_date
FROM eem_code_tracker_issues AS i
WHERE tracker_id = 3
AND (description LIKE '%/pull%' OR description LIKE '%/compare/%' OR description LIKE '%.diff');

SELECT activity_type AS type, count(*)
FROM eem_code_activity_detail
GROUP BY activity_type