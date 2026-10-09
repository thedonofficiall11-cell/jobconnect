CREATE DATABASE IF NOT EXISTS buildmart CHARACTER SET utf8mb4;
USE buildmart;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(150) UNIQUE,phone VARCHAR(20),pass VARCHAR(255),role ENUM('customer','admin') DEFAULT 'customer',created TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE categories(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80));
CREATE TABLE products(id INT AUTO_INCREMENT PRIMARY KEY,cat_id INT,name VARCHAR(150),descr TEXT,price INT,unit VARCHAR(20),stock INT DEFAULT 0,seller VARCHAR(100),created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(cat_id) REFERENCES categories(id));
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,total INT,address TEXT,phone VARCHAR(20),txn VARCHAR(60),status ENUM('pending','paid','delivered','cancelled') DEFAULT 'pending',created TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE order_items(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT,product_id INT,qty INT,price INT);
CREATE TABLE chat_messages(id INT AUTO_INCREMENT PRIMARY KEY,chat_id VARCHAR(64),sender ENUM('user','bot','agent'),body TEXT,created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX(chat_id));
INSERT INTO categories(name) VALUES('Cement & Concrete'),('Steel & Rebar'),('Roofing'),('Bricks & Blocks'),('Plumbing'),('Electrical'),('Paint & Finishes'),('Timber');
INSERT INTO products(cat_id,name,descr,price,unit,stock,seller) VALUES
(1,'Portland Cement 50kg','CPJ 42.5 general purpose cement.',13500,'bag',500,'Cimerwa Depot'),
(2,'Rebar 12mm x 12m','High-tensile deformed steel bar.',14000,'piece',300,'Kigali Steel'),
(3,'Iron Sheet 3m (28g)','Pre-painted corrugated roofing sheet.',9500,'sheet',800,'RoofPro'),
(4,'Hollow Block 6-inch','Machine-pressed concrete block.',900,'piece',5000,'Nyabugogo Blocks'),
(5,'PVC Pipe 4-inch','Pressure-rated drainage pipe, 6m.',18000,'piece',150,'AquaFit'),
(6,'Electrical Cable 2.5mm','Twin-and-earth copper cable, 100m roll.',65000,'roll',60,'VoltMart'),
(7,'Wall Paint 20L','Weather-resistant emulsion, white.',48000,'bucket',90,'ColorWorks'),
(8,'Timber 2x4 (4m)','Treated pine, structural grade.',6500,'piece',400,'Rwanda Timber');
