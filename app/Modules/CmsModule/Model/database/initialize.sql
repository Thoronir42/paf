CREATE TABLE `cms__page`
(
    id      INT          NOT NULL AUTO_INCREMENT,
    slug    VARCHAR(64)  NOT NULL,
    title   VARCHAR(128) NOT NULL,
    content TEXT,

    CONSTRAINT PRIMARY KEY (id)
);
