# Release Notes
SELECT CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END as Category, 
i.title as 'Issue Title', i.jc_issue_id, i.close_date
FROM `jos_code_tracker_issues` i
LEFT JOIN `jos_code_tracker_issue_tag_map` m 
ON i.issue_id = m.issue_id
AND m.tag_id IN (39,1,29,44,36,85,11,40,17,82,13,6,35,22,27,21,23,20,49,34,19,25,43,94,88)
WHERE DATE(close_date) BETWEEN '2011-01-10' AND '2011-02-22'
AND status_name IN('Fixed in SVN')
AND i.tracker_id = 3
ORDER BY CASE WHEN ISNULL(m.tag) THEN 'None' ELSE m.tag END, close_date asc

# Closed by Month
SELECT COUNT(*) AS total_closed, 
SUM(
CASE WHEN DATE(i.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -30 DAY)) AND Date(now())
THEN 1 ELSE 0 END
) AS closed_last_30
FROM `jos_code_tracker_issues` AS i
WHERE DATE(i.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -180 DAY)) AND Date(now())