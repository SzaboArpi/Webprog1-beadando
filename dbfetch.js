const express = require('express');
const mysql = require('mysql2');

const app = express();
app.use(express.json());

// kapcsolat
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'utazas'
});

// CONNECT
db.connect(err => {
  if (err) throw err;
  console.log('MySQL connected');
});


// CREATE (új helyseg)
app.post('/helyseg', (req, res) => {
  const { az, nev, orszag } = req.body;

  const sql = 'INSERT INTO helyseg (az, nev, orszag) VALUES (?, ?, ?)';
  db.query(sql, [az, nev, orszag], (err, result) => {
    if (err) return res.status(500).json(err);
    res.json({ message: 'Helyseg létrehozva', id: result.insertId });
  });
});


// READ (összes)
app.get('/helyseg', (req, res) => {
  db.query('SELECT * FROM helyseg', (err, results) => {
    if (err) return res.status(500).json(err);
    res.json(results);
  });
});


// READ (egy rekord)
app.get('/helyseg/:id', (req, res) => {
  const sql = 'SELECT * FROM helyseg WHERE az = ?';
  db.query(sql, [req.params.id], (err, results) => {
    if (err) return res.status(500).json(err);
    res.json(results[0]);
  });
});


// UPDATE
app.put('/helyseg/:id', (req, res) => {
  const { nev, orszag } = req.body;

  const sql = 'UPDATE helyseg SET nev = ?, orszag = ? WHERE az = ?';
  db.query(sql, [nev, orszag, req.params.id], (err, result) => {
    if (err) return res.status(500).json(err);
    res.json({ message: 'Frissítve' });
  });
});


// DELETE
app.delete('/helyseg/:id', (req, res) => {
  const sql = 'DELETE FROM helyseg WHERE az = ?';
  db.query(sql, [req.params.id], (err, result) => {
    if (err) return res.status(500).json(err);
    res.json({ message: 'Törölve' });
  });
});


// szerver indítás
app.listen(3000, () => {
  console.log('Server fut: http://localhost:3000');
});s