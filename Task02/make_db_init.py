import csv
import os
import re

def extract_year(title):
    match = re.search(r'\((\d{4})\)', title)
    return match.group(1) if match else None

def clean_title(title):
    return re.sub(r'\s*\(\d{4}\)', '', title).strip()

def escape_sql(value):
    if value is None:
        return "NULL"
    return "'" + str(value).replace("'", "''") + "'"

def generate_sql_script():
    sql_script = []

    sql_script.append("DROP TABLE IF EXISTS movies;")
    sql_script.append("DROP TABLE IF EXISTS ratings;")
    sql_script.append("DROP TABLE IF EXISTS tags;")
    sql_script.append("DROP TABLE IF EXISTS users;")
    sql_script.append("")

    sql_script.append("CREATE TABLE movies (")
    sql_script.append("    id INTEGER PRIMARY KEY,")
    sql_script.append("    title TEXT NOT NULL,")
    sql_script.append("    year INTEGER,")
    sql_script.append("    genres TEXT")
    sql_script.append(");")
    sql_script.append("")

    sql_script.append("CREATE TABLE ratings (")
    sql_script.append("    id INTEGER PRIMARY KEY,")
    sql_script.append("    user_id INTEGER NOT NULL,")
    sql_script.append("    movie_id INTEGER NOT NULL,")
    sql_script.append("    rating REAL NOT NULL,")
    sql_script.append("    timestamp INTEGER NOT NULL")
    sql_script.append(");")
    sql_script.append("")

    sql_script.append("CREATE TABLE tags (")
    sql_script.append("    id INTEGER PRIMARY KEY,")
    sql_script.append("    user_id INTEGER NOT NULL,")
    sql_script.append("    movie_id INTEGER NOT NULL,")
    sql_script.append("    tag TEXT NOT NULL,")
    sql_script.append("    timestamp INTEGER NOT NULL")
    sql_script.append(");")
    sql_script.append("")

    sql_script.append("CREATE TABLE users (")
    sql_script.append("    id INTEGER PRIMARY KEY,")
    sql_script.append("    name TEXT NOT NULL,")
    sql_script.append("    email TEXT,")
    sql_script.append("    gender TEXT,")
    sql_script.append("    register_date TEXT,")
    sql_script.append("    occupation TEXT")
    sql_script.append(");")
    sql_script.append("")

    movies_values = []
    with open('../dataset/movies.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            title_clean = clean_title(row['title'])
            year = extract_year(row['title'])
            movies_values.append(f"({row['movieId']}, {escape_sql(title_clean)}, {year if year else 'NULL'}, {escape_sql(row['genres'])})")
    
    batch_size = 500
    for i in range(0, len(movies_values), batch_size):
        batch = movies_values[i:i + batch_size]
        sql_script.append(f"INSERT INTO movies (id, title, year, genres) VALUES {','.join(batch)};")
    sql_script.append("")

    ratings_values = []
    with open('../dataset/ratings.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for i, row in enumerate(reader, 1):
            ratings_values.append(f"({i}, {row['userId']}, {row['movieId']}, {row['rating']}, {row['timestamp']})")
    
    for i in range(0, len(ratings_values), batch_size):
        batch = ratings_values[i:i + batch_size]
        sql_script.append(f"INSERT INTO ratings (id, user_id, movie_id, rating, timestamp) VALUES {','.join(batch)};")
    sql_script.append("")

    tags_values = []
    with open('../dataset/tags.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for i, row in enumerate(reader, 1):
            tags_values.append(f"({i}, {row['userId']}, {row['movieId']}, {escape_sql(row['tag'])}, {row['timestamp']})")
    
    for i in range(0, len(tags_values), batch_size):
        batch = tags_values[i:i + batch_size]
        sql_script.append(f"INSERT INTO tags (id, user_id, movie_id, tag, timestamp) VALUES {','.join(batch)};")
    sql_script.append("")

    users_values = []
    with open('../dataset/users.txt', 'r', encoding='utf-8') as f:
        for line in f:
            parts = line.strip().split('|')
            if len(parts) == 6:
                users_values.append(f"({parts[0]}, {escape_sql(parts[1])}, {escape_sql(parts[2])}, {escape_sql(parts[3])}, {escape_sql(parts[4])}, {escape_sql(parts[5])})")
    
    for i in range(0, len(users_values), batch_size):
        batch = users_values[i:i + batch_size]
        sql_script.append(f"INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES {','.join(batch)};")
    sql_script.append("")

    return "\n".join(sql_script)

if __name__ == "__main__":
    sql_content = generate_sql_script()
    with open("db_init.sql", "w", encoding="utf-8") as f:
        f.write(sql_content)
    print("SQL-скрипт db_init.sql создан!")
