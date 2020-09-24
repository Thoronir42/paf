CREATE TABLE commission__specification
(
    id                    INT         NOT NULL AUTO_INCREMENT,
    character_name        VARCHAR(64) NOT NULL,
    type                  VARCHAR(24) NOT NULL,
    character_description TEXT        NOT NULL,
    references_thread_id  INT         NOT NULL,

    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `specification_references_fq` FOREIGN KEY
        (references_thread_id) REFERENCES common__user_file_thread (id)
);

CREATE TABLE `commission__quote`
(
    id                 VARCHAR(10) NOT NULL,
    slug               VARCHAR(64) NOT NULL,
    issuer_person_id   VARCHAR(10) NOT NULL,
    supplier_person_id VARCHAR(10) NOT NULL,
    status             VARCHAR(24) NOT NULL,
    date_created       DATE        NOT NULL,
    specification_id   INT         NOT NULL,

    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `quote_supplier_fk` FOREIGN KEY
        (supplier_person_id) REFERENCES directory__person (id),
    CONSTRAINT `quote_issuer_fk` FOREIGN KEY
        (issuer_person_id) REFERENCES directory__person (id),

    CONSTRAINT `quote_specification_fk` FOREIGN KEY
        (specification_id) REFERENCES commission__specification (id)
);


CREATE TABLE commission__commission
(
    id                 VARCHAR(10) NOT NULL,
    supplier_person_id VARCHAR(10) NOT NULL,
    slug               VARCHAR(64) NOT NULL,
    status             VARCHAR(20) NOT NULL,
    customer_person_id VARCHAR(10) NOT NULL,
    specification_id   INT         NOT NULL,
    accepted_on        DATE        NOT NULL,
    comment_thread_id  VARCHAR(10) NULL,
    target_delivery    DATE        NULL,
    archived_on        DATETIME    NULL,

    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `commission_supplier_fk` FOREIGN KEY
        (supplier_person_id) REFERENCES directory__person (id),
    CONSTRAINT `commission_customer_fk` FOREIGN KEY
        (customer_person_id) REFERENCES directory__person (id),
    CONSTRAINT `commission_specification_fk` FOREIGN KEY
        (specification_id) REFERENCES commission__specification (id),
    CONSTRAINT `commission_comment_thread_fk` FOREIGN KEY
        (comment_thread_id) REFERENCES common__comment_thread (id)
);

CREATE TABLE commission__product
(
    id               VARCHAR(10) NOT NULL,
    slug             VARCHAR(64) NOT NULL,
    title            VARCHAR(64) NOT NULL,
    description      TEXT        NULL,
    type             VARCHAR(24) NOT NULL,
    owner_person_id  VARCHAR(10) NULL,
    commission_id    VARCHAR(10) NULL,
    photos_thread_id INT         NOT NULL,

    CONSTRAINT PRIMARY KEY (id),

    CONSTRAINT `product_owner_fk` FOREIGN KEY
        (owner_person_id) REFERENCES directory__person (id),
    CONSTRAINT `product_commission_fk` FOREIGN KEY
        (commission_id) REFERENCES commission__commission (id),
    CONSTRAINT `product_photos_fk` FOREIGN KEY
        (photos_thread_id) REFERENCES common__user_file_thread (id)
);
