create table if not exists tickles
(
    id       INTEGER not null primary key autoincrement,
    created  TIMESTAMP_TEXT,
    modified TIMESTAMP_TEXT,
    url      TEXT
);