CREATE TABLE `ss_settings__option_node`
(
    id                int          NOT NULL AUTO_INCREMENT,
    fqn               varchar(192) NOT NULL,
    type              varchar(24)  NOT NULL,
    parent_section_id int          NULL,
    caption           varchar(720) NULL,
    string_value      varchar(120) NULL,
    int_value         int          NULL,

    CONSTRAINT ss_settings_option_node_pk
        PRIMARY KEY (id)
);