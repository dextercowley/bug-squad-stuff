# Closed by Month
SELECT COUNT(*) AS total_closed, 
SUM(
CASE WHEN DATE(i.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -30 DAY)) AND Date(now())
THEN 1 ELSE 0 END
) AS closed_last_30
FROM `jos_code_tracker_issues` AS i
WHERE DATE(i.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -180 DAY)) AND Date(now())