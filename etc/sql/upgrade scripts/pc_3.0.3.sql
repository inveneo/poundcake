alter table radio_types add manufacturer varchar(255) after name;
update radio_types set manufacturer='Ubiquiti';