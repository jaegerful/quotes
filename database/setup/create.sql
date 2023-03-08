CREATE TABLE authors (
    id serial PRIMARY KEY,
    author text UNIQUE NOT NULL
);

CREATE TABLE categories (
    id serial PRIMARY KEY,
    category text UNIQUE NOT NULL
);

CREATE TABLE quotes (
    id serial PRIMARY KEY,
    quote text UNIQUE NOT NULL,
    author_id integer REFERENCES authors(id) NOT NULL,
    category_id integer REFERENCES categories(id) NOT NULL
);