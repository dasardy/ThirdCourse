CREATE TABLE Users(
    id_user BIGSERIAL NOT NULL PRIMARY KEY, 
    email VARCHAR(50) NOT NULL,
    user_password VARCHAR(50),
    user_name VARCHAR(50),
    jobtitle VARCHAR(50),
    num_area INTEGER,
    serv_num INTEGER
)
