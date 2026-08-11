CREATE DATABASE IF NOT EXISTS gym_center
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_center;

DROP TABLE IF EXISTS notification;
DROP TABLE IF EXISTS payment;
DROP TABLE IF EXISTS user_membership;
DROP TABLE IF EXISTS membership_plan;
DROP TABLE IF EXISTS contact_message;
DROP TABLE IF EXISTS trainer;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS gym_user;
DROP TABLE IF EXISTS login;

CREATE TABLE login (
    login_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin','Trainer','User') NOT NULL,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active'
);

CREATE TABLE gym_user (
    u_id INT AUTO_INCREMENT PRIMARY KEY,
    login_id INT NOT NULL UNIQUE,
    u_name VARCHAR(100) NOT NULL,
    u_dob DATE NOT NULL,
    u_gender ENUM('Male','Female','Other') NOT NULL,
    u_phone VARCHAR(20) UNIQUE,
    u_email VARCHAR(100) UNIQUE,
    u_address VARCHAR(150),
    CONSTRAINT fk_user_login FOREIGN KEY (login_id)
        REFERENCES login(login_id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE admin (
    a_id INT AUTO_INCREMENT PRIMARY KEY,
    login_id INT NOT NULL UNIQUE,
    a_name VARCHAR(100) NOT NULL,
    a_dob DATE,
    a_phone VARCHAR(20) UNIQUE,
    a_email VARCHAR(100) UNIQUE,
    CONSTRAINT fk_admin_login FOREIGN KEY (login_id)
        REFERENCES login(login_id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE trainer (
    t_id INT AUTO_INCREMENT PRIMARY KEY,
    login_id INT NOT NULL UNIQUE,
    t_name VARCHAR(100) NOT NULL,
    t_dob DATE,
    t_phone VARCHAR(20) UNIQUE,
    t_email VARCHAR(100) UNIQUE,
    t_expertise VARCHAR(100) NOT NULL,
    CONSTRAINT fk_trainer_login FOREIGN KEY (login_id)
        REFERENCES login(login_id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE membership_plan (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(50) NOT NULL,
    duration ENUM('Monthly','Quarterly','Yearly') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active'
);

CREATE TABLE user_membership (
    membership_id INT AUTO_INCREMENT PRIMARY KEY,
    u_id INT NOT NULL,
    plan_id INT NOT NULL,
    t_id INT NULL,
    start_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    status ENUM('Active','Expired','Cancelled') NOT NULL DEFAULT 'Active',
    CONSTRAINT fk_um_user FOREIGN KEY (u_id)
        REFERENCES gym_user(u_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_um_plan FOREIGN KEY (plan_id)
        REFERENCES membership_plan(plan_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_um_trainer FOREIGN KEY (t_id)
        REFERENCES trainer(t_id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    membership_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_via ENUM('Cash','eSewa','Khalti','Mobile Banking') NOT NULL,
    payment_date DATE NOT NULL,
    remarks VARCHAR(255),
    CONSTRAINT fk_payment_membership FOREIGN KEY (membership_id)
        REFERENCES user_membership(membership_id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    u_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    notification_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Read','Unread') NOT NULL DEFAULT 'Unread',
    CONSTRAINT fk_notification_user FOREIGN KEY (u_id)
        REFERENCES gym_user(u_id)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE contact_message (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    sent_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Sample plans
INSERT INTO membership_plan (plan_name, duration, price, description) VALUES
('Basic', 'Monthly', 1500, 'Basic gym access'),
('Premium', 'Monthly', 2500, 'Gym access with trainer support'),
('Basic', 'Quarterly', 4000, 'Basic quarterly membership'),
('Premium', 'Yearly', 25000, 'Premium yearly membership');

-- Demo admin:
INSERT INTO login (username, password, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC0S9t7h0m2M4QJ8mS8W', 'Admin');

INSERT INTO admin (login_id, a_name, a_dob, a_phone, a_email)
VALUES (LAST_INSERT_ID(), 'System Admin', '2006-01-16', '9803748251', 'heismadridista4@gmail.com');
