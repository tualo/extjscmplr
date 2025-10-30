delimiter ;

insert ignore into route_scopes (`scope`) values ('extjs.compiler');
insert ignore into route_scopes_permissions (`scope`, `group`, `allowed`) values ('extjs.compiler', 'administration', 1);

