CREATE TABLE IF NOT EXISTS {tablename} (
`post_id` int not null,
`user_id` int not null,
PRIMARY KEY (`post_id`,`user_id`),
`attributes` text
);
