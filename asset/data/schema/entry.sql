create table if not exists entry
(
	id int auto_increment
		primary key,
	datetime_from datetime not null,
	datetime_to datetime not null,
	content varchar(1024) default '' not null,
	issue varchar(128) default '' not null
);

create index if not exists entry_datetime_from_index
	on entry (datetime_from);

create index if not exists entry_datetime_to_index
	on entry (datetime_to);

create index if not exists entry_id_index
	on entry (id);

create index if not exists entry_issue_index
	on entry (issue);

