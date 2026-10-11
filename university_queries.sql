/*
 * Name: Nguyễn Huyền My
 * Student ID: 23070626
 * Assignment: INS3064 - Homework 5 (University Queries)
 */

USE university_db;

/*
 * Query 1: Basic SELECT
 * Description: Retrieves all columns from the students table to list every student in the database.
 * Expected Output: A complete list of 5 students with all their details.
 */
SELECT * FROM students;
/*
 * +----+--------------+-------------------+---------------------+--------+
 * | id | student_code | full_name         | email               | gender |
 * +----+--------------+-------------------+---------------------+--------+
 * |  1 | 23070626     | Lê Hải Đăng       | 23070626@vnu.edu.vn | Male   |
 * |  2 | 23070627     | Nguyễn Quỳnh Anh  | 23070627@vnu.edu.vn | Female |
 * |  3 | 23070628     | Trần Bảo Long     | 23070628@vnu.edu.vn | Male   |
 * |  4 | 23070629     | Vũ Thảo My        | 23070629@vnu.edu.vn | Female |
 * |  5 | 23070630     | Đinh Đức Trọng    | 23070630@vnu.edu.vn | Male   |
 * +----+--------------+-------------------+---------------------+--------+
 * (5 rows)
 */

/*
 * Query 2: WHERE with comparison
 * Description: Filters students to find those who have a GPA of 3.20 or higher.
 * Expected Output: A list of 3 high-achieving students.
 */
SELECT id, full_name, gpa 
FROM students 
WHERE gpa >= 3.20;
/*
 * +----+-------------------+------+
 * | id | full_name         | gpa  |
 * +----+-------------------+------+
 * |  1 | Lê Hải Đăng       | 3.20 |
 * |  2 | Nguyễn Quỳnh Anh  | 3.85 |
 * |  4 | Vũ Thảo My        | 3.50 |
 * +----+-------------------+------+
 * (3 rows)
 */

/*
 * Query 3: WHERE with AND/OR
 * Description: Finds all Female students who have a GPA greater than 3.0.
 * Expected Output: A filtered list of 2 female students meeting the GPA requirement.
 */
SELECT full_name, gender, gpa 
FROM students 
WHERE gender = 'Female' AND gpa > 3.00;
/*
 * +-------------------+--------+------+
 * | full_name         | gender | gpa  |
 * +-------------------+--------+------+
 * | Nguyễn Quỳnh Anh  | Female | 3.85 |
 * | Vũ Thảo My        | Female | 3.50 |
 * +-------------------+--------+------+
 * (2 rows)
 */

/*
 * Query 4: ORDER BY
 * Description: Sorts the list of students alphabetically by their full name in ascending order.
 * Expected Output: 5 student names arranged from A to Z based on their first character.
 */
SELECT id, full_name 
FROM students 
ORDER BY full_name ASC;
/*
 * +----+-------------------+
 * | id | full_name         |
 * +----+-------------------+
 * |  1 | Lê Hải Đăng       |
 * |  2 | Nguyễn Quỳnh Anh  |
 * |  3 | Trần Bảo Long     |
 * |  4 | Vũ Thảo My        |
 * |  5 | Đinh Đức Trọng    |
 * +----+-------------------+
 * (5 rows)
 */

/*
 * Query 5: LIMIT
 * Description: Returns the top 3 highest grades from the enrollments table.
 * Expected Output: The top 3 enrollment records sorted by grade in descending order.
 */
SELECT student_id, course_id, grade 
FROM enrollments 
ORDER BY grade DESC 
LIMIT 3;
/*
 * +------------+-----------+-------+
 * | student_id | course_id | grade |
 * +------------+-----------+-------+
 * |          1 |         4 |  4.00 |
 * |          2 |         3 |  4.00 |
 * |          2 |         1 |  3.80 |
 * +------------+-----------+-------+
 * (3 rows)
 */

/*
 * Query 6: LIKE
 * Description: Searches for students whose full name starts with 'Ng'.
 * Expected Output: 1 student whose last name is Nguyen.
 */
SELECT id, full_name 
FROM students 
WHERE full_name LIKE 'Ng%';
/*
 * +----+-------------------+
 * | id | full_name         |
 * +----+-------------------+
 * |  2 | Nguyễn Quỳnh Anh  |
 * +----+-------------------+
 * (1 row)
 */

/*
 * Query 7: Aggregate — COUNT / SUM
 * Description: Counts the total number of students registered in the database.
 * Expected Output: A single column showing the total count of student records (5).
 */
SELECT COUNT(id) AS total_students 
FROM students;
/*
 * +----------------+
 * | total_students |
 * +----------------+
 * |              5 |
 * +----------------+
 * (1 row)
 */

/*
 * Query 8: Aggregate with GROUP BY + HAVING
 * Description: Groups enrollments by course and filters for courses that have more than 2 students enrolled.
 * Expected Output: Only course_id 2 (Lập trình 1) which has 3 students.
 */
SELECT course_id, COUNT(student_id) AS enrolled_count 
FROM enrollments 
GROUP BY course_id 
HAVING COUNT(student_id) > 2;
/*
 * +-----------+----------------+
 * | course_id | enrolled_count |
 * +-----------+----------------+
 * |         2 |              3 |
 * +-----------+----------------+
 * (1 row)
 */

/*
 * Query 9: JOIN — two tables
 * Description: Joins students and departments tables to show each student's name alongside their department name.
 * Expected Output: A list of 5 students matched with their respective faculty names.
 */
SELECT s.full_name, d.department_name AS faculty 
FROM students s 
JOIN departments d ON s.department_id = d.id;
/*
 * +-------------------+----------------------------+
 * | full_name         | faculty                    |
 * +-------------------+----------------------------+
 * | Lê Hải Đăng       | Khoa Các khoa học ứng dụng |
 * | Nguyễn Quỳnh Anh  | Khoa Kinh tế và Quản lý    |
 * | Trần Bảo Long     | Khoa Các khoa học ứng dụng |
 * | Vũ Thảo My        | Khoa Các khoa học ứng dụng |
 * | Đinh Đức Trọng    | Khoa Kinh tế và Quản lý    |
 * +-------------------+----------------------------+
 * (5 rows)
 */

/*
 * Query 10: JOIN — three or more tables
 * Description: Joins enrollments, students, and courses to display a readable transcript showing who studies what and their grade.
 * Expected Output: 10 enrollment records with student name, course title, and final grade.
 */
SELECT 
    s.full_name AS student_name, 
    c.title AS course_title, 
    e.grade 
FROM enrollments e 
JOIN students s ON e.student_id = s.id 
JOIN courses c ON e.course_id = c.id;
/*
 * +-------------------+--------------------------------------+-------+
 * | student_name      | course_title                         | grade |
 * +-------------------+--------------------------------------+-------+
 * | Lê Hải Đăng       | Lập trình 1                          |  3.50 |
 * | Lê Hải Đăng       | Lí thuyết xác suất và thống kê toán  |  4.00 |
 * | Nguyễn Quỳnh Anh  | Kinh tế vĩ mô                        |  3.80 |
 * | Nguyễn Quỳnh Anh  | Tổ chức và quản trị kinh doanh       |  4.00 |
 * | Trần Bảo Long     | Lập trình 1                          |  2.50 |
 * | Vũ Thảo My        | Lập trình 1                          |  3.80 |
 * | Đinh Đức Trọng    | Kinh tế chính trị Mác - Lênin        |  3.20 |
 * | Lê Hải Đăng       | Kĩ năng bổ trợ 2                     |  2.00 |
 * | Trần Bảo Long     | Kinh tế vĩ mô                        |  NULL |
 * | Đinh Đức Trọng    | Tổ chức và quản trị kinh doanh       |  NULL |
 * +-------------------+--------------------------------------+-------+
 * (10 rows)
 */