# Introduction to Linux - Seminar: Deploying Web Apps with Nginx
This repository contains 3 demo projects to showcase the capabilities of Nginx as a web server, PHP/MySQL web server, and reverse proxy with load balancing. Each demo is desinged to run on Linux environment (specifically Ubuntu) and illustrates different use cases of Nginx configuration.
## Table of contents
- [Overview](#overview)
- [Project Structure](#project-structure)
- [Demo description](#demo-descriptions)
  - [Demo 1: Static HTML Web Server](#demo-1-static-html-web-server)
  - [Demo 2: PHP/MySQL Web Server](#demo-2-phpmysql-web-server)
  - [Demo 3: Reverse Proxy with Load Balancing](#demo-3-reverse-proxy-with-load-balancing)
- [Prerequisites](#prerequisites)
- [Installation and Setup](installation-and-setup)
  - [Configuring Virtual Hosts](configuring-virtual-hosts)
- [Running the Demos](#running-the-demos)

## Overview
This project demonstrates how to configure Nginx for various scenarios, including serving static content, hosting dynamic PHP/MySQL applications, and acting as a reverse proxy with load balancing for FastAPI applications running with Uvicorn. It serves as an introductory guide for setting up Nginx on Ubuntu.

## Project Structure
```
Intro-to-Linux_Nginx/
├── nginx_configs/              # NGINX configuration files
│   ├── demo1.test              # Config for static HTML server
│   ├── demo2.test              # Config for PHP/MySQL server
│   └── demo3.test              # Config for reverse proxy
├── demo1/
│   └── index.html
├── demo2/
│   ├── classes/
│   │   └── Users.php
│   ├── config/
│   |   └── database.php
|   ├── dashboard.php
|   ├── index.php
|   └── database.sql
├── demo3/
|   ├── server1.py
|   ├── server2.py
|   ├── server3.py
|   └── requirements.txt
└── README.md
```

## Demo descriptions
### Demo 1: Static HTML Web Server
- **Purpose**: Demonstrates Nginx as a web server serving a single static HTML file.
- **Details**: A simple Nginx configuration to serve `index.html` from the `demo1/` directory on port 80, accessible via `http://demo1.test`.
- **Use Case**: Basic web hosting for static content.
- **Files**:
  - `demo1/index.html`: The static HTML file.
  - `nginx_configs/demo1`: Nginx configuration file.
### Demo 2: PHP/MySQL Web Server
- **Purpose**: Shows Nginx configured to serve a PHP application with MySQL backend.
- **Details**: Nginx integrates with PHP-FPM to process PHP files and connects to a MySQL database initialized with `database.sql`, accessible via `http://demo2.test`.
- **Use Case**: Hosting dynamic web applications.
- **Files**:
  - `demo2/index.php`: Main PHP file.
  - `demo2/config/database.php`: Database connection configuration.
  - `demo2/database.sql`: SQL script to initialize the database.
  - `nginx_configs/demo2`: Nginx configuration file.
### Demo 3: Reverse Proxy with Load Balancing
- **Purpose**: Illustrates Nginx as a reverse proxy and load balancer for a FastAPI application.
- **Details**: Nginx forwards requests to multiple Uvicorn instances running a FastAPI app in a Python virtual environment, accessible via `http://demo3.test`.
- **Use Case**: High-traffic applications requiring load distribution.
- **Files**:
  - `demo3/server1.py`: FastAPI application on Server 1 (`localhost:8001`).
  - `demo3/server2.py`: FastAPI application on Server 2 (`localhost:8002`).
  - `demo3/server3.py`: FastAPI application on Server 3 (`localhost:8003`).
  - `nginx_configs/demo3`: Nginx configuration for reverse proxy and load balancing (`localhost:80`).

## Prerequisites
- Ubuntu 20.04 or later.
- Nginx installed (`sudo apt install nginx`).
- MySQL server (`sudo apt install mysql-server` for Demo 2).
- PHP and PHP-FPM (`sudo apt install php-fpm8.2 php8.2-mysql` for Demo 2).
- Python 3.8+ and pip (for Demo 3).

## Installation and Setup
1. **Clone the repository**:
   ```bash
   git clone https://github.com/hhanhLeO/Intro-to-Linux_Nginx.git
   cd Intro-to-Linux_Nginx
   ```

2. **Install Nginx**:
   ```bash
   sudo apt update
   sudo apt install nginx
   ```

3. **Install dependencies for Demo 2**:
   ```bash
   sudo apt install php8.2-fpm php8.2-mysql mysql-server
   ```

4. **Install Python dependencies for Demo 3**:
   ```bash
   python3 -m venv demo3/venv
   source demo3/venv/bin/activate
   pip install -r demo3/requirements.txt
   ```

5. **Configure file permissions**:
   ```bash
   sudo chown -R www-data:www-data demo1/html demo2/php
   sudo chmod -R 755 demo1/html demo2/php
   ```
### Configuring Virtual Hosts
To access the demos using custom domain names (`demo1.test`, `demo2.test`, `demo3.test`), you need to configure your `/etc/hosts` file and Nginx server blocks (virtual hosts).
**Edit `/etc/hosts`**:
Add the following lines to `/etc/hosts` to map the domain names to localhost:
```bash
  sudo nano /etc/hosts
```
Add:
```
  127.0.0.1 demo1.test
  127.0.0.1 demo2.test
  127.0.0.1 demo3.test
```
Save and exit.

## Running the Demos
### Demo 1
1. Create the root web directory for `demo1.test` and move the source code to the directory:
  ```bash
    sudo mkdir -p /var/www/demo1.test
    sudo cp demo1/* /var/www/demo1.test
  ```

2. Copy the Nginx configuration to `/etc/nginx/sites-available`:
  ```bash
    sudo cp nginx_configs/demo1.test /etc/nginx/sites-available/
    sudo ln -s /etc/nginx/sites-available/demo1.test /etc/nginx/sites-enabled/
  ```

4. Test configuration and reload Nginx:
  ```bash
    sudo nginx -t
    sudo systemctl reload nginx
  ```

5. Access: Open `http://demo1.test` in a browser.

### Demo 2
1. Create database and user in MySQL:
  ```bash
    sudo mysql < demo2/database.sql
    CREATE USER 'webuser'@'localhost' IDENTIFIED BY 'webuser123';
    GRANT ALL ON user_system.* TO 'webuser'@'localhost';
  ```

2. Create the root web directory for `demo2.test` and move the source code to the directory:
  ```bash
    sudo mkdir -p /var/www/demo2.test
    sudo cp demo2/* /var/www/demo2.test
  ```

3. Copy the Nginx configuration to `/etc/nginx/sites-available`:
  ```bash
    sudo cp nginx_configs/demo2.test /etc/nginx/sites-available/
    sudo ln -s /etc/nginx/sites-available/demo2.test /etc/nginx/sites-enabled/
  ```

4. Test configuration and reload Nginx:
  ```bash
    sudo nginx -t
    sudo systemctl reload nginx
  ```

5. Access: Open `http://demo2.test` in a browser.

### Demo 3
1. Navigate to the `demo3` directory and activate the virtual environment:
   ```bash
     cd demo3
     source venv/bin/activate
   ```

2. Start 3 Uvicorn instances for load balancing (in separate terminal sessions):
   ```bash
     uvicorn server1:app --host 127.0.0.1 --port 8001 &
     uvicorn server2:app --host 127.0.0.1 --port 8002 &
     uvicorn server3:app --host 127.0.0.1 --port 8003 &
   ```
   
3. Copy the Nginx configuration to `/etc/nginx/sites-available`:
  ```bash
    sudo cp nginx_configs/demo3.test /etc/nginx/sites-available/
    sudo ln -s /etc/nginx/sites-available/demo3.test /etc/nginx/sites-enabled/
  ```

4. Test configuration and reload Nginx:
  ```bash
    sudo nginx -t
    sudo systemctl reload nginx
  ```

5. Access: Open `http://demo3.test` in a browser.
6. Stop the Uvicorn instances: Press `Ctrl+C` in each terminal.
