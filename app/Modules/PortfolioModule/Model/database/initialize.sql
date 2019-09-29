CREATE TABLE portfolio__fursuit
(
    id              INT         NOT NULL AUTO_INCREMENT,
    slug            VARCHAR(64) NOT NULL,
    character_name  VARCHAR(64) NOT NULL,
    type            VARCHAR(24) NOT NULL,
    owner_person_id VARCHAR(10) NULL,
    issued_on       DATE        NOT NULL,
    completed_on    DATE        NOT NULL,

    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `fursuit_owner_fk` FOREIGN KEY
        (owner_person_id) REFERENCES common__person (id)
)
