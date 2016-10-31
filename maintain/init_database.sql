DROP TABLE IF EXISTS user_item;
DROP TABLE IF EXISTS item;
DROP TABLE IF EXISTS room;
DROP TABLE IF EXISTS user;

CREATE TABLE user(
    user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    openid VARCHAR(255) NOT NULL,
    nickname VARCHAR(255) NOT NULL,
    sex INT NOT NULL,
    city VARCHAR(255) NOT NULL,
    province VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    headimgurl VARCHAR(255) NOT NULL
);

CREATE TABLE room(
    room_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    theme VARCHAR(255) NOT NULL,
    theme_description VARCHAR(255) NOT NULL,
    end_time INT NOT NULL,
    last_time INT NOT NULL,
    creator_id INT NOT NULL,
    member_uplimit INT NOT NULL DEFAULT 2147483647,
    will_notice TINYINT NOT NULL DEFAULT 0,
    anonymous TINYINT NOT NULL DEFAULT 0,
    is_viewable TINYINT NOT NULL DEFAULT 1,
    type TINYINT NOT NULL,
    available TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY(creator_id) REFERENCES user(user_id) ON UPDATE CASCADE
);

CREATE TABLE item(
    item_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    content VARCHAR(255) NOT NULL,
    room_id INT NOT NULL,
    available TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY(room_id) REFERENCES room(room_id) ON UPDATE CASCADE
);

CREATE TABLE user_item(
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    vote_time INT NOT NULL,
    value FLOAT NOT NULL,
    available TINYINT NOT NULL DEFAULT 1,
    PRIMARY KEY(user_id, item_id),
    FOREIGN KEY(user_id) REFERENCES user(user_id) ON UPDATE CASCADE,
    FOREIGN KEY(item_id) REFERENCES item(item_id) ON UPDATE CASCADE
);