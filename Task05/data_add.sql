INSERT OR IGNORE INTO users (name, email, gender, register_date, occupation_id)
VALUES
('Петров Петр Петрович', 'triple_petr@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Зайчикова Зайка Зайчиковна', 'zaika@gmail.com', 'female', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Иванов Иван Иванович', 'ivan@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Сидоров Георгий Ильич', 'sidr@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1)),
('Петрушкин Петр Петрович', 'petrusha@gmail.com', 'male', date('now'),
 (SELECT id FROM occupations ORDER BY id LIMIT 1));


INSERT OR IGNORE INTO movies (title, year)
VALUES
('Гарри Поттер (2001)', 2001),
('Интерстеллар (2014)', 2014),
('Мир Юрского периода (2015)', 2015);


INSERT OR IGNORE INTO genres (name) VALUES ('Sci-Fi');
INSERT OR IGNORE INTO genres (name) VALUES ('Action');
INSERT OR IGNORE INTO genres (name) VALUES ('Adventure');
INSERT OR IGNORE INTO genres (name) VALUES ('Fantasy');
INSERT OR IGNORE INTO genres (name) VALUES ('Drama');


INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Fantasy'
WHERE m.title = 'Гарри Поттер (2001)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Sci-Fi'
WHERE m.title = 'Интерстеллар (2014)';

INSERT OR IGNORE INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id FROM movies m JOIN genres g ON g.name = 'Action'
WHERE m.title = 'Мир Юрского периода (2015)';


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.8, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Гарри Поттер (2001)'
WHERE u.email = 'triple_petr@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 5.0, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Интерстеллар (2014)'
WHERE u.email = 'ivan@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT u.id, m.id, 4.9, strftime('%s','now')
FROM users u JOIN movies m ON m.title = 'Мир Юрского периода (2015)'
WHERE u.email = 'sidr@gmail.com'
AND NOT EXISTS (
    SELECT 1 FROM ratings r WHERE r.user_id = u.id AND r.movie_id = m.id
);
