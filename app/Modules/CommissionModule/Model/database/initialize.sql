CREATE TABLE commission__specification
(
    id                    INT         NOT NULL AUTO_INCREMENT,
    character_name        VARCHAR(64) NOT NULL,
    type                  VARCHAR(24) NOT NULL,
    character_description TEXT        NOT NULL,

    CONSTRAINT PRIMARY KEY (id)
);

CREATE TABLE `commission__quote`
(
    id               INT         NOT NULL AUTO_INCREMENT,
    slug             VARCHAR(64) NOT NULL,
    issuer_user_id   INT         NOT NULL,
    status           VARCHAR(24) NOT NULL,
    date_created     DATE        NOT NULL,
    specification_id INT         NOT NULL,

    CONSTRAINT PRIMARY KEY (id),
    CONSTRAINT `quote_issuer_fk` FOREIGN KEY
        (issuer_user_id) REFERENCES common__user (id),

    CONSTRAINT `quote_specification_fk` FOREIGN KEY
        (specification_id) REFERENCES commission__specification (id)
);


CREATE TABLE commission__paf_case
(
    id               INT         NOT NULL AUTO_INCREMENT,
    status           varchar(20) NOT NULL,
    customer_user_id int         NOT NULL,
    specification_id int         NOT NULL,
    accepted_on      DATE        NOT NULL,
    target_delivery  DATE        NULL,


    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `case_customer_fk` FOREIGN KEY
        (customer_user_id) REFERENCES common__user (id),
    CONSTRAINT `case_specification_fk` FOREIGN KEY
        (specification_id) REFERENCES commission__specification (id)
);

CREATE TABLE commission__fursuit_progress
(
    id          INT NOT NULL AUTO_INCREMENT,
    case_id     INT NOT NULL,

    head        INT NOT NULL DEFAULT -1,
    body        INT NOT NULL DEFAULT -1,
    arm_sleeves INT NOT NULL DEFAULT -1,
    paws        INT NOT NULL DEFAULT -1,
    tail        INT NOT NULL DEFAULT -1,
    leg_sleeves INT NOT NULL DEFAULT -1,
    hind_paws   INT NOT NULL DEFAULT -1,

    CONSTRAINT PRIMARY KEY (id),
    CONSTRAINT `progress_case_fk` FOREIGN KEY
        (case_id) REFERENCES commission__paf_case (id)
)
