import csv
import os
import re

DATASET_DIR = 'dataset'
SQL_INIT_FILE = 'db_init.sql'
DB_NAME = 'movies_rating.db'

TABLES = {
    'movies': {
        'file': 'movies.csv',
        'table': 'movies',
        'columns': [
            ('id', 'INTEGER PRIMARY KEY'),
            ('title', 'TEXT'),
            ('year', 'INTEGER'),
            ('genres', 'TEXT')
        ],
        'delimiter': ','
    },
    'ratings': {
        'file': 'ratings.csv',
        'table': 'ratings',
        'columns': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('user_id', 'INTEGER'),
            ('movie_id', 'INTEGER'),
            ('rating', 'REAL'),
            ('timestamp', 'INTEGER')
        ],
        'delimiter': ','
    },
    'tags': {
        'file': 'tags.csv',
        'table': 'tags',
        'columns': [
            ('id', 'INTEGER PRIMARY KEY AUTOINCREMENT'),
            ('user_id', 'INTEGER'),
            ('movie_id', 'INTEGER'),
            ('tag', 'TEXT'),
            ('timestamp', 'INTEGER')
        ],
        'delimiter': ','
    },
    'users': {
        'file': 'users.txt',
        'table': 'users',
        'columns': [
            ('id', 'INTEGER PRIMARY KEY'),
            ('name', 'TEXT'),
            ('email', 'TEXT'),
            ('gender', 'TEXT'),
            ('register_date', 'TEXT'),
            ('occupation', 'TEXT')
        ],
        'delimiter': '|'
    }
}


def generate_sql_script():
    """
    Генерирует SQL-скрипт для создания и наполнения базы данных.
    """
    with open(SQL_INIT_FILE, 'w', encoding='utf-8') as f:
        f.write("BEGIN TRANSACTION;\n\n")

        for config in TABLES.values():
            table_name = config['table']
            file_path = os.path.join(DATASET_DIR, config['file'])

            columns_with_types = ", ".join([f"{col[0]} {col[1]}" for col in config['columns']])

            insert_columns_list = [col[0] for col in config['columns'] if 'AUTOINCREMENT' not in col[1].upper()]
            columns_for_insert = ", ".join(insert_columns_list)

            f.write(f"DROP TABLE IF EXISTS {table_name};\n")
            f.write(f"CREATE TABLE {table_name} ({columns_with_types});\n\n")

            try:
                with open(file_path, 'r', encoding='utf-8') as data_file:
                    if config['file'].endswith('.csv'):
                        next(data_file)

                    reader = csv.reader(data_file, delimiter=config['delimiter'])

                    for row in reader:
                        if not row: continue

                        if table_name == 'movies':
                            if len(row) != 3: continue

                            movie_id = row[0]
                            title_with_year = row[1]
                            genres = row[2]

                            title = title_with_year
                            year = 'NULL'

                            match = re.search(r'\((\d{4})\)$', title_with_year)
                            if match:
                                year = match.group(1)
                                title = title_with_year[:match.start()].strip()

                            safe_title = title.replace("'", "''")
                            safe_genres = genres.replace("'", "''")

                            values = f"'{movie_id}', '{safe_title}', {year}, '{safe_genres}'"
                            f.write(f"INSERT INTO {table_name} ({columns_for_insert}) VALUES ({values});\n")

                        else:
                            values = ", ".join([f"'{str(val).replace("'", "''")}'" for val in row])
                            f.write(f"INSERT INTO {table_name} ({columns_for_insert}) VALUES ({values});\n")

            except FileNotFoundError:
                print(f"Предупреждение: Файл {file_path} не найден. Таблица {table_name} будет создана пустой.")
            except Exception as e:
                print(f"Произошла ошибка при обработке файла {file_path}: {e}")

            f.write("\n")

        f.write("COMMIT;\n")


if __name__ == "__main__":
    generate_sql_script()
    print(f"SQL-скрипт '{SQL_INIT_FILE}' успешно сгенерирован.")
