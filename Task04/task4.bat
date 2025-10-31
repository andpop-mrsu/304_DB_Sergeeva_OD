#!/bin/bash
chcp 65001 2>/dev/null || true

sqlite3 movies_rating.db < db_init.sql

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили. В списке оставить первые 100 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT DISTINCT u1.name as user1, u2.name as user2, m.title as movie FROM ratings r1 JOIN ratings r2 ON r1.movie_id = r2.movie_id AND r1.user_id < r2.user_id JOIN users u1 ON r1.user_id = u1.id JOIN users u2 ON r2.user_id = u2.id JOIN movies m ON r1.movie_id = m.id ORDER BY u1.name, u2.name, m.title LIMIT 100;"
echo " "

echo "2. Найти 10 самых старых оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT m.title, u.name, r.rating, date(r.timestamp, 'unixepoch') as review_date FROM ratings r JOIN users u ON r.user_id = u.id JOIN movies m ON r.movie_id = m.id ORDER BY r.timestamp ASC LIMIT 10;"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке 'Рекомендуем' для фильмов должно быть написано 'Да' или 'Нет'."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH avg_ratings AS (SELECT m.id, m.title, m.year, AVG(r.rating) as avg_rating FROM movies m JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year), max_min AS (SELECT MAX(avg_rating) as max_rating, MIN(avg_rating) as min_rating FROM avg_ratings) SELECT ar.title, ar.year, ar.avg_rating, CASE WHEN ar.avg_rating = (SELECT max_rating FROM max_min) THEN 'Да' ELSE 'Нет' END as Рекомендуем FROM avg_ratings ar WHERE ar.avg_rating = (SELECT max_rating FROM max_min) OR ar.avg_rating = (SELECT min_rating FROM max_min) ORDER BY ar.year, ar.title;"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-мужчины в период с 2011 по 2014 год."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT COUNT(*) as количество_оценок, AVG(r.rating) as средняя_оценка FROM ratings r JOIN users u ON r.user_id = u.id WHERE u.gender = 'M' AND strftime('%Y', datetime(r.timestamp, 'unixepoch')) BETWEEN '2011' AND '2014';"
echo " "

echo "5. Составить список фильмов с указанием средней оценки и количества пользователей, которые их оценили. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT m.title, m.year, AVG(r.rating) as средняя_оценка, COUNT(DISTINCT r.user_id) as количество_оценивших FROM movies m JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year ORDER BY m.year, m.title LIMIT 20;"
echo " "

echo "6. Определить самый распространенный жанр фильма и количество фильмов в этом жанре. Отдельную таблицу для жанров не использовать, жанры нужно извлекать из таблицы movies."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE split(id, genre, rest) AS (SELECT id, substr(genres, 1, instr(genres || '|', '|') - 1), substr(genres, instr(genres || '|', '|') + 1) FROM movies UNION ALL SELECT id, substr(rest, 1, instr(rest || '|', '|') - 1), substr(rest, instr(rest || '|', '|') + 1) FROM split WHERE rest != '') SELECT trim(genre) AS genre, COUNT(DISTINCT id) AS count FROM split WHERE genre != '' GROUP BY genre ORDER BY count DESC LIMIT 1;"
echo " "

echo "7. Вывести список из 10 последних зарегистрированных пользователей в формате 'Фамилия Имя|Дата регистрации' (сначала фамилия, потом имя)."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "SELECT substr(name, instr(name, ' ') + 1) || ' ' || substr(name, 1, instr(name, ' ') - 1) || '|' || register_date as 'Фамилия Имя|Дата регистрации' FROM users ORDER BY register_date DESC LIMIT 10;"
echo " "

echo "8. С помощью рекурсивного CTE определить, на какие дни недели приходился ваш день рождения в каждом году."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE birthday_years(year) AS (SELECT 2000 UNION ALL SELECT year + 1 FROM birthday_years WHERE year < 2024) SELECT year, CASE CAST(strftime('%w', year || '-04-01') AS INTEGER) WHEN 0 THEN 'Воскресенье' WHEN 1 THEN 'Понедельник' WHEN 2 THEN 'Вторник' WHEN 3 THEN 'Среда' WHEN 4 THEN 'Четверг' WHEN 5 THEN 'Пятница' ELSE 'Суббота' END AS day_of_week FROM birthday_years;"
echo " "