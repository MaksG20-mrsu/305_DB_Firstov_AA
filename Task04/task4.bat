#!/bin/bash
chcp 65001 > nul

sqlite3 movies_rating.db < db_init.sql

echo "1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили. В списке оставить первые 100 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "SELECT u1.name AS user1, u2.name AS user2, m.title FROM ratings r1 JOIN ratings r2 ON r1.movie_id = r2.movie_id AND r1.user_id < r2.user_id JOIN users u1 ON r1.user_id = u1.id JOIN users u2 ON r2.user_id = u2.id JOIN movies m ON r1.movie_id = m.id LIMIT 100;"
echo " "

echo "2. Найти 10 самых старых оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "SELECT m.title, u.name, r.rating, DATE(r.timestamp, 'unixepoch') AS rating_date FROM (SELECT *, ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY timestamp ASC) as rn FROM ratings) r JOIN users u ON r.user_id = u.id JOIN movies m ON r.movie_id = m.id WHERE r.rn = 1 ORDER BY r.timestamp ASC LIMIT 10;"
echo " "

echo "3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке 'Рекомендуем' для фильмов должно быть написано 'Да' или 'Нет'."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "WITH MovieStats AS (SELECT movie_id, AVG(rating) AS avg_rating FROM ratings GROUP BY movie_id), ExtRatings AS (SELECT MAX(avg_rating) AS max_r, MIN(avg_rating) AS min_r FROM MovieStats) SELECT m.title, m.year, ms.avg_rating, CASE WHEN ms.avg_rating = er.max_r THEN 'Да' ELSE 'Нет' END AS 'Рекомендуем' FROM movies m JOIN MovieStats ms ON m.id = ms.movie_id JOIN ExtRatings er ON ms.avg_rating = er.max_r OR ms.avg_rating = er.min_r ORDER BY m.year, m.title;"
echo " "

echo "4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-мужчины в период с 2011 по 2014 год."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "SELECT COUNT(r.rating) AS rating_count, AVG(r.rating) AS avg_rating FROM ratings r JOIN users u ON r.user_id = u.id WHERE u.gender = 'female' AND STRFTIME('%Y', r.timestamp, 'unixepoch') BETWEEN '2011' AND '2014';"
echo " "

echo "5. Составить список фильмов с указанием средней оценки и количества пользователей, которые их оценили. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "SELECT m.title, m.year, AVG(r.rating) AS average_rating, COUNT(r.user_id) AS user_count FROM movies m JOIN ratings r ON m.id = r.movie_id GROUP BY m.id, m.title, m.year ORDER BY m.year, m.title LIMIT 20;"
echo " "

echo "6. Определить самый распространенный жанр фильма и количество фильмов в этом жанре. Отдельную таблицу для жанров не использовать, жанры нужно извлекать из таблицы movies."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "WITH RECURSIVE genres_split(genre, remaining_genres) AS (SELECT '', genres || '|' FROM movies UNION ALL SELECT SUBSTR(remaining_genres, 0, INSTR(remaining_genres, '|')), SUBSTR(remaining_genres, INSTR(remaining_genres, '|') + 1) FROM genres_split WHERE remaining_genres != '') SELECT genre, COUNT(genre) AS genre_count FROM genres_split WHERE genre != '' GROUP BY genre ORDER BY genre_count DESC LIMIT 1;"
echo " "

echo "7. Вывести список из 10 последних зарегистрированных пользователей в формате 'Фамилия Имя|Дата регистрации' (сначала фамилия, потом имя)."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "SELECT name || '|' || register_date AS user_registration FROM users ORDER BY register_date DESC LIMIT 10;"
echo " "

echo "8. С помощью рекурсивного CTE определить, на какие дни недели приходился ваш день рождения в каждом году."
echo "--------------------------------------------------"
sqlite3 movies_rating.db -box "WITH RECURSIVE BirthdayCTE(BDate) AS (SELECT '2005-06-02' UNION ALL SELECT DATE(BDate, '+1 year') FROM BirthdayCTE WHERE DATE(BDate, '+1 year') <= DATE('now')) SELECT BDate AS BirthDate, CASE STRFTIME('%w', BDate) WHEN '0' THEN 'Воскресенье' WHEN '1' THEN 'Понедельник' WHEN '2' THEN 'Вторник' WHEN '3' THEN 'Среда' WHEN '4' THEN 'Четверг' WHEN '5' THEN 'Пятница' ELSE 'Суббота' END AS DayOfWeek FROM BirthdayCTE;"
echo " "