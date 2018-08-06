update items set lat='6.444' where id=1;
update items set lat='6.444' where id=2;
update items set lat='6.444', lon='6.444' where id=8;
update items set lat='6.444', lon='6.444' where id=5;

select * from items;
select id,X(location),Y(location) from locations;