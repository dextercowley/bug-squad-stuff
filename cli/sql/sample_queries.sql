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

