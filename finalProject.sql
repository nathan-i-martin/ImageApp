DROP DATABASE IF EXISTS finalProjectNathanMartin;
CREATE DATABASE finalProjectNathanMartin;
USE finalProjectNathanMartin;

DROP TABLE IF EXISTS images;
CREATE TABLE images (
    imageId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    imageBlob MEDIUMBLOB NOT NULL, -- Allow us to store blobs up to ~16MB
    imageAlt VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    userId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    username VARCHAR(255) NOT NULL,
    userPassword VARCHAR(255) NOT NULL,
    userDescription VARCHAR(255) DEFAULT NULL,
    userImageId_fk INT NOT NULL DEFAULT 1,
    FOREIGN KEY (userImageId_fk) REFERENCES images(imageId) ON DELETE CASCADE
);

DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
    postId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    authorId_fk INT NOT NULL,
    imageId_fk INT NOT NULL DEFAULT 2,
    postDate INT NOT NULL, -- Decided to use UNIX Timestamps here which are INT. Largely because they don't give me a headache to think about
    postDescription VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (authorId_fk) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (imageId_fk) REFERENCES images(imageId) ON DELETE CASCADE
);

/*
    We don't create a PRIMARY KEY on this table because that would allow duplicate entries to be made which we don't want.
    By using a unique constraint, the combination of rootUser_fk and followedUser_fk are used to create the "primary key". This way if a duplicate
    entry is attempted, it will fail.
*/
DROP TABLE IF EXISTS usersFollowing;
CREATE TABLE usersFollowing (
    rootUser_fk INT NOT NULL, -- The user that is following
    followedUser_fk INT NOT NULL, -- The user to be followed
    FOREIGN KEY (rootUser_fk) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (followedUser_fk) REFERENCES users(userId) ON DELETE CASCADE,
    CONSTRAINT uc_Follow UNIQUE (rootUser_fk,followedUser_fk)
);

INSERT INTO images (imageBlob, imageAlt)
VALUES
("iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAABmJLR0QA/wD/AP+gvaeTAAADi0lEQVR4nO2cTUtVURSGH9MiBUsqJYhMy8I+iAbRxASptBo47MsaBP2A/kKTIJoFFSH9C0chRaFWGEVUphlC2aiENAujLG2wrmAXO7dz7tlr7+tZD7yj+7H2etl337PXWWeDYRiGYRiGYRhGoJT5HkABmoCjQAvQDGwBqnOvTQPjwAjQD9wBxjyMsWSpAM4CD4H5mBoAuoBy9VGXGMeAUeIbnK8RoEN57CVBFXCb4g3OVzdQqZhH0NQBT0nf5AU9AWrVsgmUOuRn7srkxUtJZs2uwu1MXmpmr1bJLDBcrMmFdEsls4DoQN/kBR1XyC8IKkjnEi6phsnIdfY5/Jm8oNPOswyAR/g3ut95lp5pwr/J88Ac0Og4179YoRkMKRCFQBnKW3Rto1uU40XRqhlM2+idyvGiaNYMpm30ZuV4UdRrBtM2urrwW9RYoxlM+w7LvHK8Qqjlrz2jM4sZrYS20T+V40XxQzOYttFfleNFMa0ZTNvoD8rxohjXDKZt9GvleFEMawbTNnpAOV4Uy7qCtw2pnIVQvWtwnKt3knQgpa0+51kGQBf+jT7pPMsAKEenl+NfGiIj9wwB2vFn9CGF/IKiG32Tb6pkFhiVSPeQlsmPyWinEkg/nMZ6PQxsUMopWGqBQdzO5Mw2OOZTifTFuViTM7tcRHEE+ZkXa/AQGby6iEs5cAapRcTZrs8hO75TBHhDI/SnshqRRpdWpFUh/6ms98ifaR/yVNY7D2M0DMMwDMMwDMMobULcGa4FdgF7gN3ADmAdUJN7rSb3vqlFmgTeIDWOV0j/iGonUiFCMHojUgA6DLQBW1P63jHgPnAXuAd8TOl7S4p9wFVk9mncXZkDXgBXgL0K+XmlHrgIPEPH3EJl1EvAdqcZK3MQ6CGMDqWlZnov0EkYy2hsVgEXgJf4N/N/9Rw4D6x04IcTOoG3+DcuqUaBEwQ8ww8gd0V8G5WWHgD7U3WoSCqQP5Zf+Dcnbf0GrhHActKA9Dz7NsS1BpEDA7zQhuzKfJugpUmUnx8nF/B7kQMvRc2geHDAemDCcUIhawKpvcQiSZ/wZaQukVWqEN96XQfyefBUKIr9dFmSi/IZ7LzPb8Q8qSFJ69SnBJ9ZbsQuuSYxuifBZ5YbKh5sAj7jf530pSkUT9JpB744TihETeKhHbgRuIFchcwmHHgpaDaX43Wkm9UwDMMwDMMwDMMwDCPz/AHwMcyzZEtWEAAAAABJRU5ErkJggg==","Default user profile image"),
("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","Placeholder Image");

-- All passwords here are `123`
INSERT INTO users (username, userPassword, userDescription)
VALUES
("_nathan_","$2y$10$rgp9IICWbikeaIdV.G7p.ObYiQKLHO/9fuhpel.Wv8kyk.5PKYytW","Lorem ipsum dolor sit amet, consectetur adipiscing elit"),
("john10","$2y$10$rgp9IICWbikeaIdV.G7p.ObYiQKLHO/9fuhpel.Wv8kyk.5PKYytW","Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."),
("cole-32","$2y$10$rgp9IICWbikeaIdV.G7p.ObYiQKLHO/9fuhpel.Wv8kyk.5PKYytW","Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.");

INSERT INTO posts (authorId_fk, postDescription, postDate)
VALUES
(1,"Lorem ipsum dolor sit amet, consectetur adipiscing elit",UNIX_TIMESTAMP()),
(2,"Lorem ipsum dolor sit amet, consectetur adipiscing elit",UNIX_TIMESTAMP()),
(3,"Lorem ipsum dolor sit amet, consectetur adipiscing elit",UNIX_TIMESTAMP()),
(3,"Lorem ipsum dolor sit amet, consectetur adipiscing elit",UNIX_TIMESTAMP()),
(1,"Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",UNIX_TIMESTAMP()),
(2,"Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",UNIX_TIMESTAMP()),
(2,"Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",UNIX_TIMESTAMP()),
(3,"Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",UNIX_TIMESTAMP()),
(3,"Adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",UNIX_TIMESTAMP()),
(1,"Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",UNIX_TIMESTAMP()),
(2,"Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",UNIX_TIMESTAMP()),
(3,"Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",UNIX_TIMESTAMP());


INSERT INTO usersFollowing (rootUser_fk, followedUser_fk)
VALUES
(1,2), -- _nathan_ follow john10
(1,3), -- _nathan_ follow cole-32
(2,3), -- john10 follow cole-32
(3,1); -- cole-32 follow _nathan_

-- TEST used to confirm that duplicate entry fails on usersFollowing
/*
INSERT INTO usersFollowing (rootUser_fk, followedUser_fk)
VALUES
(1,2); -- _nathan_ follow john10 <<<< THIS SHOULD CAUSE AN ERROR!!!!!
*/

SELECT * FROM images;
SELECT * FROM users;
SELECT * FROM posts;
SELECT * FROM usersFollowing;

/*
    ###############################
    #    EXAMPLE SELECT QUERIES   #
    ###############################
    Remove the comment around a query to test it out!
*/

-- Get users
/*
SELECT u.userId, u.username, u.userDescription, i.imageBlob, i.imageAlt
FROM users AS u
INNER JOIN images AS i ON u.userImageId_fk=i.imageId
WHERE u.userId = 1 -- Change to look at different users
ORDER BY u.username DESC;
*/

-- Get a particular user by username
/*
SELECT u.userId, u.username, u.userDescription, i.imageBlob, i.imageAlt
FROM users AS u
INNER JOIN images AS i ON u.userImageId_fk=i.imageId
WHERE u.username = "_nathan_";
*/

-- Get posts for a user
/*
SELECT p.postId, p.postDescription, p.postDate, i.imageBlob, i.imageAlt
FROM posts AS p
INNER JOIN images AS i ON p.imageId_fk=i.imageId
WHERE p.authorId_fk = 1 -- Change to look at different users
ORDER BY p.postDate DESC;
*/

-- Get a specific `post`
/*
SELECT p.postId, p.postDescription, p.postDate, i.imageBlob, i.imageAlt
FROM posts AS p
INNER JOIN images AS i ON p.imageId_fk=i.imageId
WHERE p.postId = 2; -- Change to look at different posts
*/

-- Get people a user is following
/*
SELECT u.userId, u.username, i.imageBlob, i.imageAlt
FROM usersFollowing AS uf
INNER JOIN users AS u ON uf.followedUser_fk=u.userId
INNER JOIN images AS i ON u.userImageId_fk=i.imageId
WHERE uf.rootUser_fk = 1; -- Change to look at different users
*/

-- Get people that are following a user
/*
SELECT u.userId, u.username, i.imageBlob, i.imageAlt
FROM usersFollowing AS uf
INNER JOIN users AS u ON uf.rootUser_fk=u.userId
INNER JOIN images AS i ON u.userImageId_fk=i.imageId
WHERE uf.followedUser_fk = 1; -- Change to look at different users
*/

-- Get a user's feed
/*
SELECT *
FROM posts AS p
LEFT JOIN usersFollowing AS uf ON p.authorId_fk=uf.followedUser_fk
INNER JOIN images AS i ON p.imageId_fk=i.imageId
WHERE uf.rootUser_fk = 1 -- Change to look at different users
ORDER BY p.postDate DESC
LIMIT 50;
*/

-- Set a user to follow some people
/*
SET @userId := 1; -- Change to effect different users
INSERT INTO usersFollowing (rootUser_fk, followedUser_fk)
VALUES
(@userId,1), -- user follow _nathan_
(@userId,2), -- user follow john10
(@userId,3); -- user follow cole-32
*/

-- Set a user to be followed by some people
/*
SET @userId := 1; -- Change to effect different users
INSERT INTO usersFollowing (rootUser_fk, followedUser_fk)
VALUES
(1,@userId), -- _nathan_ follow user
(2,@userId), -- john10 follow user
(3,@userId); -- cole-32 follow user
*/