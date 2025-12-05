CREATE TABLE
    users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        is_admin TINYINT (1) DEFAULT 0,
        password_hash VARCHAR(255) NOT NULL,
        profile_picture VARCHAR(255) DEFAULT 'default.png',
        failed_attempts INT DEFAULT 0,
        lock_until DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    );

CREATE TABLE
    recipes (
        recipe_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        cover_img_src VARCHAR(255) DEFAULT NULL,
        is_featured TINYINT (1) DEFAULT 0,
        cuisine_type VARCHAR(50),
        dietary_preference VARCHAR(50),
        difficulty ENUM ('Easy', 'Medium', 'Hard'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE SET NULL
    );

CREATE TABLE
    community_cookbook (
        entry_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(150) NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(255) NULL,
        claps INT UNSIGNED DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
    );

CREATE TABLE
    resources (
        resource_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        resource_type ENUM ('RecipeCard', 'Tutorial', 'Video', 'Infographic'),
        file_url VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        category ENUM ('Culinary', 'Educational') DEFAULT 'Culinary'
    );

CREATE TABLE
    contact_messages (
        message_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(150),
        message TEXT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    events (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        event_date DATETIME NOT NULL,
        cover_img_src VARCHAR(255) DEFAULT NULL,
        location VARCHAR(255) DEFAULT NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users (user_id) ON DELETE SET NULL
    );

CREATE TABLE
    culinary_trends (
        trend_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        cover_img_src VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );