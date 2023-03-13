# Quotes

#### this project accomplishes two main goals:
1. **replete** a postgres **database with randomized quotes**. it does so by using the [favqs](https://favqs.com/) api.
2. **provide** a CRUD **api for** the **repleted database.** the api follows REST principles.

#### breakdown of directories:
1. `database/` 
    - `setup/create.sql`: file to create tables
    - `generate.mjs`: script to replete tables (requires a `.env` file with database connection information)
2. `api/`: holds the api for repleted databases (written in `php`. meant to run in `apache`.)

#### attributions:
written by Johan Jaeger.  
instance of project [hosted on replit.com.](https://quotes.jajaeger2.repl.co)