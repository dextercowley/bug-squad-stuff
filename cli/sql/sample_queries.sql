# Points by user by activity type
SELECT t.activity_title, CONCAT(u.first_name, ' ', u.last_name) AS name, u.jc_user_id, count(*), MAX(t.activity_points) AS weight,
SUM(t.activity_points) AS total_points
FROM jos_code_activity_detail AS a
JOIN jos_code_users AS u
ON a.jc_user_id = u.jc_user_id
JOIN jos_code_activity_types AS t
ON a.activity_type = t.activity_type
WHERE date(a.activity_date) BETWEEN '2013-01-01' AND '2013-12-31'
GROUP BY t.activity_title, u.jc_user_id
ORDER BY SUM(t.activity_points) DESC

# Total points by user
SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.jc_user_id,
SUM(t.activity_points) AS total_points
FROM jos_code_activity_detail AS a
JOIN jos_code_users AS u
ON a.jc_user_id = u.jc_user_id
JOIN jos_code_activity_types AS t
ON a.activity_type = t.activity_type
WHERE date(a.activity_date) BETWEEN '2012-01-01' AND '2013-12-31'
GROUP BY u.jc_user_id
HAVING SUM(t.activity_points) > 50
ORDER BY SUM(t.activity_points) DESC
