CREATE TABLE offer__offer
(
    id                 varchar(4),
    slug               VARCHAR(24),
    supplier_person_id VARCHAR(10),

    type               VARCHAR(12),
    name               VARCHAR(120),
    description        TEXT,
    preview_image_path VARCHAR(30),
    standard_price     DECIMAL(13,4),

    CONSTRAINT PRIMARY KEY `offer__offer_pk` (id),
    CONSTRAINT `offer__offer_supplier_person_fk` FOREIGN KEY
        (supplier_person_id) REFERENCES directory__person (id)
);
