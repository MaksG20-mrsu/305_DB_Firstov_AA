INSERT INTO users (first_name, last_name, email, gender, occupation_id) VALUES
('Макисм', 'Шарунов', 'maxim.sharunov@mail.ru', 'male', (SELECT id FROM occupations WHERE name = 'programmer')),
('Владислав', 'Четайкин', 'vladislav.chetaykin@mail.ru', 'male', (SELECT id FROM occupations WHERE name = 'writer')),
('Илья', 'Тульсков', 'ilya.tulskov@mail.ru', 'male', (SELECT id FROM occupations WHERE name = 'other')),
('Данил', 'Снегирёв', 'danil.snegirev@mail.ru', 'male', (SELECT id FROM occupations WHERE name = 'executive')),
('Артём', 'Фирстов', 'artyom.firstov.2005@mail.ru', 'male', (SELECT id FROM occupations WHERE name = 'technician'));


INSERT INTO movies (title, year) VALUES
('Дюна: Часть вторая', 2024),
('Оппенгеймер', 2023),
('Интерстеллар', 2014);

INSERT OR IGNORE INTO genres (name) VALUES ('Sci-Fi'), ('Drama'), ('Biography'), ('Adventure');

INSERT INTO movie_genres (movie_id, genre_id) VALUES
((SELECT id FROM movies WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM genres WHERE name = 'Sci-Fi')),
((SELECT id FROM movies WHERE title = 'Дюна: Часть вторая'), (SELECT id FROM genres WHERE name = 'Adventure')),
((SELECT id FROM movies WHERE title = 'Оппенгеймер'), (SELECT id FROM genres WHERE name = 'Biography')),
((SELECT id FROM movies WHERE title = 'Оппенгеймер'), (SELECT id FROM genres WHERE name = 'Drama')),
((SELECT id FROM movies WHERE title = 'Интерстеллар'), (SELECT id FROM genres WHERE name = 'Sci-Fi')),
((SELECT id FROM movies WHERE title = 'Интерстеллар'), (SELECT id FROM genres WHERE name = 'Drama'));


INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES
(
    (SELECT id FROM users WHERE email = 'artyom.firstov.2005@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Дюна: Часть вторая'),
    5.0,
    strftime('%s', 'now')
),
(
    (SELECT id FROM users WHERE email = 'artyom.firstov.2005@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Оппенгеймер'),
    4.5,
    strftime('%s', 'now')
),
(
    (SELECT id FROM users WHERE email = 'artyom.firstov.2005@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Интерстеллар'),
    5.0,
    strftime('%s', 'now')
);