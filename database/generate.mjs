'use strict'

import {config} from 'dotenv'
config()

import pg from 'pg'
const {Pool} = pg

const pool = new Pool({
    host: process.env.host,
    port: process.env.port,
    database: process.env.database,
    user: process.env.user,
    password: process.env.password,
    ssl: true
})

import https from 'https'

const options = {
    hostname: 'favqs.com',
    path: '/api/qotd',
    method: 'GET',
    timeout: 30000
}

function generate() {
    return new Promise((resolve, reject) => {

        const request = https.request(options, response => {       

            response.on('data', async data => {
                data = JSON.parse(data)
                data = data.quote
        
                /* if quote missing data. */
        
                if (data?.tags.length === 0 || !data?.author || !data?.body) {
                    reject('skipped invalid quote.')
                    return
                }
        
                /* hold identifiers for 'categories' and 'authors' entries. */
        
                const identifiers = {}
        
                /* insert entry for category. */
        
                const category = `INSERT INTO categories(category) VALUES ($1) ON CONFLICT (category) DO UPDATE SET category = categories.category RETURNING id;`
        
                try {
                    response = await pool.query(category, [data.tags[0]])
                }
                catch(error) {
                    reject(error)
                }

                identifiers.category = response.rows[0].id
                
                /* insert entry for author. */
        
                const author = `INSERT INTO authors(author) VALUES ($1) ON CONFLICT (author) DO UPDATE SET author = authors.author RETURNING id;`
        
                try {
                    response = await pool.query(author, [data.author])
                }
                catch(error) {
                    reject(error)
                }

                identifiers.author = response.rows[0].id
        
                /* insert entry for quote. */
        
                const quote = `INSERT INTO quotes(quote, author_id, category_id) VALUES ($1, $2, $3) ON CONFLICT (quote) DO NOTHING;`
        
                try {
                    await pool.query(quote, [data.body, identifiers.author, identifiers.category])
                }
                catch(error) {
                    reject(error)    
                }

                resolve()
            })
        })
        
        request.on('error', error => {
            reject(error)
        })

        request.end()
    })
}

async function batch(amount) {
    console.log(`\ngenerating batch...\n`)

    let count = 0
    
    while (count < amount) {
        try {
            await generate()
            ++count
        }
        catch(error) {
            console.error(error)
        }
    }

    return count
}

/* generate 'amount' entries. */

const amount = 45

batch(amount).then(count => 
    console.log(`\nbatch complete.\ninsertions made: ${count}.\n`)
)