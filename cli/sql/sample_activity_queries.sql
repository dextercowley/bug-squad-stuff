# Total points by user
SELECT u.name, u.id,
SUM(t.activity_points) AS total_points,
SUM(
CASE WHEN t.activity_group = 'Tracker' THEN t.activity_points ELSE 0 END
) AS tracker_points,
SUM(
CASE WHEN t.activity_group = 'Test' THEN t.activity_points ELSE 0 END
) AS test_points,
SUM(
CASE WHEN t.activity_group = 'Code' THEN t.activity_points ELSE 0 END
) AS code_points
FROM jos_code_activity_detail AS a
JOIN jos_users AS u
ON a.user_id = u.id
JOIN jos_code_activity_types AS t
ON a.activity_type = t.activity_type
WHERE date(a.activity_date) BETWEEN '2010-01-01' AND '2013-12-31'
GROUP BY u.id
HAVING SUM(t.activity_points) > 500
ORDER BY SUM(t.activity_points) DESC

# Total points by user
SELECT u.name, u.id,
SUM(t.activity_points) AS total_points
FROM jos_code_activity_detail AS a
JOIN jos_users AS u
ON a.user_id = u.id
JOIN jos_code_activity_types AS t
ON a.activity_type = t.activity_type
WHERE date(a.activity_date) BETWEEN '2010-01-01' AND '2013-12-31'
GROUP BY u.id
HAVING SUM(t.activity_points) > 500
ORDER BY SUM(t.activity_points) DESC

# Time series activity by type
SELECT t.activity_group, 
SUM(
CASE WHEN DATE(a.activity_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -7 DAY)) AND Date(now())
  THEN t.activity_points ELSE 0 END
)
 AS last_week,
SUM(
CASE WHEN DATE(a.activity_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -14 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -7 DAY)) 
  THEN t.activity_points ELSE 0 END
)
 AS two_weeks_ago,
SUM(
CASE WHEN DATE(a.activity_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -21 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -14 DAY)) 
  THEN t.activity_points ELSE 0 END
)
 AS three_weeks_ago,
SUM(
CASE WHEN DATE(a.activity_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -28 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -21 DAY)) 
  THEN t.activity_points ELSE 0 END
)
 AS four_weeks_ago
 FROM `jos_code_activity_detail` AS a
JOIN jos_users AS u ON u.id = a.user_id
JOIN jos_code_activity_types AS t ON t.activity_type = a.activity_type
WHERE date(a.activity_date) > Date(DATE_ADD(now(), INTERVAL -28 DAY))
GROUP BY t.activity_group

# Create and close activity by time series
SELECT  
SUM(
CASE WHEN DATE(t.created_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -7 DAY)) AND Date(now())
  THEN 1 ELSE 0 END
)
 AS created_last_week,
SUM(
CASE WHEN DATE(t.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -7 DAY)) AND Date(now())
  THEN 1 ELSE 0 END
)
 AS closed_last_week,
SUM(
CASE WHEN DATE(t.created_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -14 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -7 DAY)) 
  THEN 1 ELSE 0 END
)
 AS created_two_weeks_ago,
 SUM(
CASE WHEN DATE(t.close_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -14 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -7 DAY)) 
  THEN 1 ELSE 0 END
)
 AS closed_two_weeks_ago,
SUM(
CASE WHEN DATE(t.created_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -21 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -14 DAY)) 
  THEN 1 ELSE 0 END
)
 AS created_three_weeks_ago,
SUM(
CASE WHEN DATE(t.created_date) BETWEEN Date(DATE_ADD(now(), INTERVAL -28 DAY)) 
AND Date(DATE_ADD(now(), INTERVAL -21 DAY)) 
  THEN 1 ELSE 0 END
)
 AS created_four_weeks_ago
 FROM `jos_code_tracker_issues` AS t

WHERE date(t.created_date) > Date(DATE_ADD(now(), INTERVAL -28 DAY))

# Active Users
SELECT u.name, SUM(t.activity_points) AS total_points
FROM jos_code_activity_detail AS a
JOIN jos_code_activity_types AS t
ON a.activity_type = t.activity_type
JOIN jos_users AS u
ON a.user_id = u.id
WHERE date(a.activity_date) > Date(DATE_ADD(now(), INTERVAL -90 DAY))
GROUP BY u.id, u.name
HAVING SUM(t.activity_points) > 20
ORDER BY u.name ASC
