CREATE TABLE Med_area(
    id_area BIGSERIAL NOT NULL PRIMARY KEY,
    num_area INTEGER NOT NULL,
    village_area  VARCHAR(50), 
    street_area  VARCHAR(50),
    house_area INTEGER NOT NULL
);

CREATE TABLE Patient(
    id_pat BIGSERIAL NOT NULL PRIMARY KEY,
    med_pol INTEGER NOT NULL,
    num_medbook INTEGER NOT NULL,
    firstname_pat VARCHAR(50) NOT NULL,
	name_pat VARCHAR(50) NOT NULL,
	fathername_pat VARCHAR(50),
	village_pat VARCHAR(50) NOT NULL,
	street_pat VARCHAR(50) NOT NULL,
	house_pat INTEGER NOT NULL,
	flat_pat INTEGER,
	datebirth_pat DATE NOT NULL,
	num_area INTEGER NOT NULL,
	id_area INTEGER REFERENCES Med_area(id_area) NOT NULL
);

CREATE TABLE Doctor(
	id_doc BIGSERIAL NOT NULL PRIMARY KEY,
	serv_num INTEGER NOT NULL,
	firstname_doc VARCHAR(50) NOT NULL,
	name_doc VARCHAR(50) NOT NULL,
	father_name VARCHAR(50) NOT NULL,
	specialization VARCHAR(50) NOT NULL,
	category VARCHAR(50) NOT NULL,
	salary REAL NOT NULL,
	num_area INTEGER
);

CREATE TABLE Diagnosis(
	id_diag BIGSERIAL NOT NULL PRIMARY KEY,
	name_diag VARCHAR(50) NOT NULL,
	desc_diag VARCHAR(500) NOT NULL
);

CREATE TABLE Goal_of_visit(
	id_goal BIGSERIAL NOT NULL PRIMARY KEY,
	name_goal VARCHAR NOT NULL,
	desc_goal VARCHAR(500) NOT NULL
);

CREATE TABLE Visit(
	id_visit BIGSERIAL NOT NULL PRIMARY KEY,
	id_pat  INTEGER REFERENCES Patient(id_pat) NOT NULL,
	id_doc  INTEGER REFERENCES Doctor(id_doc) NOT NULL,
	id_diag  INTEGER REFERENCES Diagnosis(id_diag) NOT NULL,
	id_goal  INTEGER REFERENCES Goal_of_visit(id_goal) NOT NULL,
	date_visit TIMESTAMP NOT NULL,
	status_visit VARCHAR NOT NULL,
	num_ticket INTEGER NOT NULL
);

/*PROCEDURES*/
CREATE OR REPLACE PROCEDURE add_med_area(
    a_num_area INTEGER,
    a_village_area  VARCHAR(50), 
    a_street_area  VARCHAR(50),
    a_house_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS(SELECT * FROM Med_area WHERE num_area = a_num_area) THEN
        RAISE EXCEPTION 'Area with this number % already exists!', a_num_area;
    ELSE
        INSERT INTO Med_area(
            num_area, village_area, street_area, house_area )
        VALUES(
            a_num_area, a_village_area, a_street_area, a_house_area 
        );
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE add_patient(
    a_med_pol INTEGER,
    a_num_medbook INTEGER,
    a_firstname_pat VARCHAR(50),
	a_name_pat VARCHAR(50),
	a_fathername_pat VARCHAR(50),
	a_village_pat VARCHAR(50),
	a_street_pat VARCHAR(50),
	a_house_pat INTEGER,
	a_flat_pat INTEGER,
	a_datebirth_pat DATE,
	a_num_area INTEGER,
	a_id_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF (EXISTS(SELECT * FROM Patient WHERE med_pol = a_med_pol) 
        OR 
        EXISTS(SELECT * FROM Patient WHERE num_medbook = a_num_medbook)) 
        THEN
        RAISE EXCEPTION 'A patient with the same policy or medical book number already exists!';
    ELSE
        INSERT INTO Patient(
            med_pol, num_medbook, firstname_pat, name_pat,
            fathername_pat, village_pat, street_pat, house_pat, 
            flat_pat, datebirth_pat, num_area, id_area
        )
        VALUES(
            a_med_pol, a_num_medbook, a_firstname_pat, a_name_pat,
            a_fathername_pat, a_village_pat, a_street_pat, a_house_pat, 
            a_flat_pat, a_datebirth_pat, a_num_area, a_id_area
        );
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE add_doctor(
	a_serv_num INTEGER,
	a_firstname_doc VARCHAR(50),
	a_name_doc VARCHAR(50),
	a_father_name VARCHAR(50),
	a_specialization VARCHAR(50),
	a_category VARCHAR(50),
	a_salary REAL,
	a_num_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF  EXISTS(SELECT * FROM Doctor WHERE serv_num = a_serv_num) 
        THEN
        RAISE EXCEPTION 'A doctor with this sevice number: % already exists!', a_serv_num;
    ELSE
        INSERT INTO Doctor(
            serv_num, firstname_doc, name_doc, father_name,
            specialization, category, salary, num_area
        )
        VALUES(
            a_serv_num, a_firstname_doc, a_name_doc, a_father_name,
            a_specialization, a_category, a_salary, a_num_area
        );
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE add_diagnosis(
	a_name_diag VARCHAR(50) ,
	a_desc_diag VARCHAR(500)
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF  EXISTS(SELECT * FROM Diagnosis WHERE name_diag = a_name_diag) 
        THEN
        RAISE EXCEPTION 'A diagnosis with this name: % already exists!', a_name_diag;
    ELSE
        INSERT INTO Diagnosis(
            name_diag, desc_diag
        )
        VALUES(
            a_name_diag, a_desc_diag
        );
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE add_goal(
	a_name_goal VARCHAR(50) ,
	a_desc_goal VARCHAR(500)
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS(SELECT * FROM Goal_of_visit WHERE name_goal = a_name_goal) 
        THEN
        RAISE EXCEPTION 'A goal with this name: % already exists!', a_name_goal;
    ELSE
        INSERT INTO Goal_of_visit(
            name_goal, desc_goal
        )
        VALUES(
            a_name_goal, a_desc_goal
        );
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE add_visit(
	a_id_pat  INTEGER,
	a_id_doc  INTEGER,
	a_id_diag  INTEGER,
	a_id_goal  INTEGER,
	a_date_visit TIMESTAMP,
	a_status_visit VARCHAR,
	a_num_ticket INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF  EXISTS(SELECT * FROM Visit WHERE num_ticket = a_num_ticket) 
        THEN
        RAISE EXCEPTION 'Visiting with this ticket: % already exists!', a_num_ticket;
    ELSE
        INSERT INTO Visit(
            id_pat, id_doc, id_diag, id_goal,
            date_visit, status_visit, num_ticket
        )
        VALUES(
            a_id_pat, a_id_doc, a_id_diag, a_id_goal,
            a_date_visit, a_status_visit, a_num_ticket
        );
    END IF;
END;
$$;

/*DELITING*/

CREATE OR REPLACE PROCEDURE del_area (
    del_num_area INTEGER)
LANGUAGE plpgsql
AS $$
BEGIN
    IF (EXISTS (SELECT * FROM Patient p, Med_area ma
			   WHERE ma.num_area = del_num_area
			   AND p.id_area = ma.id_area)
        OR
        EXISTS (SELECT * FROM Doctor d, Med_area ma
			   WHERE ma.num_area = del_num_area
			   AND d.num_area = ma.num_area)
        )
        THEN
        RAISE EXCEPTION 'It is not possible to delete an area because there are patients or doctors attached to it!';
    ELSE
        DELETE FROM Med_area WHERE num_area = del_num_area;
    END IF;
END;
$$
;

CREATE OR REPLACE PROCEDURE del_patient (
    del_med_pol INTEGER)
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS (SELECT * FROM Patient p, Visit v
			   WHERE p.med_pol = del_med_pol
			   AND p.id_pat = v.id_pat) THEN
        RAISE EXCEPTION 'The patient cannot be deleted because he has appointments!';
    ELSE
        DELETE FROM Patient WHERE med_pol = del_med_pol;
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE del_doctor (
    del_serv_num INTEGER)
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS (SELECT * FROM Doctor d, Visit v
			   WHERE d.serv_num = del_serv_num
			   AND d.id_doc = v.id_doc) THEN
        RAISE EXCEPTION 'It is impossible to remove the doctor because he has visits!';
    ELSE
        DELETE FROM Doctor WHERE serv_num = del_serv_num;
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE del_diag (
    del_name_diag VARCHAR(50))
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS (SELECT * FROM Diagnosis d, Visit v
			   WHERE d.name_diag = del_name_diag
			   AND d.id_diag = v.id_diag) THEN
        RAISE EXCEPTION 'It is impossible to delete a diagnosis because there are visits associated with it!';
    ELSE
        DELETE FROM Diagnosis WHERE name_diag = del_name_diag;
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE del_goal (
    del_name_goal VARCHAR(50))
LANGUAGE plpgsql
AS $$
BEGIN
    IF EXISTS (SELECT * FROM Goal_of_visit g, Visit v
			   WHERE g.name_goal = del_name_goal
			   AND d.id_goal = v.id_goal) THEN
        RAISE EXCEPTION 'The goal cannot be deleted because it has visits!';
    ELSE
        DELETE FROM Goal_of_visit WHERE name_goal = del_name_goal;
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE del_visit (
    del_num_ticket INTEGER)
LANGUAGE plpgsql
AS $$
BEGIN
    DELETE FROM Visit WHERE num_ticket = del_num_ticket;
END;
$$;

/*UPDATING*/
CREATE OR REPLACE PROCEDURE upd_med_area (
    u_id_area INTEGER,
    u_num_area INTEGER,
    u_village_area  VARCHAR(50), 
    u_street_area  VARCHAR(50),
    u_house_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
     UPDATE Med_area 
     SET 
        num_area = u_num_area,
        village_area = u_village_area,
        street_area = u_street_area,
        house_area = u_house_area
     WHERE id_area = u_id_area;
END;$$;

CREATE OR REPLACE PROCEDURE upd_patient (
    u_id_pat INTEGER,
    u_med_pol INTEGER,
    u_num_medbook INTEGER,
    u_firstname_pat VARCHAR(50),
	u_name_pat VARCHAR(50),
	u_fathername_pat VARCHAR(50),
	u_village_pat VARCHAR(50),
	u_street_pat VARCHAR(50),
	u_house_pat INTEGER,
	u_flat_pat INTEGER,
	u_datebirth_pat DATE,
	u_num_area INTEGER,
	u_id_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
        UPDATE Patient 
        SET 
            med_pol = u_med_pol, 
            num_medbook = u_num_medbook, 
            firstname_pat = u_firstname_pat, 
            name_pat = u_name_pat,
            fathername_pat = u_fathername_pat,
            village_pat = u_village_pat, 
            street_pat = u_street_pat, 
            house_pat = u_house_pat, 
            flat_pat = u_flat_pat,
            datebirth_pat = u_datebirth_pat,
            num_area = u_num_area, 
            id_area = u_id_area
        WHERE id_pat = u_id_pat;
END;$$;

CREATE OR REPLACE PROCEDURE upd_doc (
    u_id_doc INTEGER,
    u_serv_num INTEGER,
	u_firstname_doc VARCHAR(50),
	u_name_doc VARCHAR(50),
	u_father_name VARCHAR(50),
	u_specialization VARCHAR(50),
	u_category VARCHAR(50),
	u_salary REAL,
	u_num_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
        UPDATE Doctor 
        SET 
            serv_num = u_serv_num,
            firstname_doc = u_firstname_doc,
            name_doc = u_name_doc,
            father_name = u_father_name,
            specialization = u_specialization, 
            category = u_category, 
            salary = u_salary,
            num_area = u_num_area
        WHERE id_doc = u_id_doc;
END;$$;

CREATE OR REPLACE PROCEDURE upd_diag(
    u_id_diag INTEGER,
    u_name_diag VARCHAR(50) ,
	u_desc_diag VARCHAR(500)
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF  EXISTS(SELECT * FROM Diagnosis WHERE name_diag = a_name_diag) 
        THEN
        RAISE EXCEPTION 'A diagnosis with this name: % already exists!', u_name_diag;
    ELSE
        UPDATE Med_area 
        SET 
            name_diag = u_name_diag ,
	        desc_diag = u_desc_diag
        WHERE id_diag = u_id_diag;
    END IF;
END;$$;

CREATE OR REPLACE PROCEDURE upd_goal(
    u_id_goal INTEGER,
    u_name_goal VARCHAR(50) ,
	u_desc_goal VARCHAR(500)
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF  EXISTS(SELECT * FROM Goal_of_visit WHERE name_goal = u_name_goal) 
        THEN
        RAISE EXCEPTION 'A goal with this name: % already exists!', u_name_goal;
    ELSE
        UPDATE Med_area 
        SET 
            name_goal = u_name_goal ,
	        desc_goal = u_desc_goal
        WHERE id_goal = u_id_goal;
    END IF;
END;$$;

CREATE OR REPLACE PROCEDURE upd_visit(
    u_id_visit INTEGER,
    u_id_pat  INTEGER,
	u_id_doc  INTEGER,
	u_id_diag  INTEGER,
	u_id_goal  INTEGER,
	u_date_visit TIMESTAMP,
	u_status_visit VARCHAR,
	u_num_ticket INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
        UPDATE Visit
        SET 
            id_pat = u_id_pat,
            id_doc = u_id_doc,
            id_diag = u_id_diag,
            id_goal = u_id_goal,
            date_visit = u_date_visit, 
            status_visit = u_status_visit, 
            num_ticket = u_num_ticket
        WHERE id_visit = u_id_visit;
END;$$;

CALL add_med_area(894, 'п.Первомайский', 'Михайлова', 25);
CALL add_med_area(14, 'п.Аэропорт', 'Омулёвского', 10);
CALL add_med_area(7, 'п.Вольчека', 'Антоновская', 8);
CALL add_med_area(21, 'п.Загорск', 'Лесная', 17);
CALL add_med_area(10, 'п.Северный', 'Гоголя', 42);
CALL add_med_area(5, 'п.Южный', 'Лермонтова', 7);
CALL add_med_area(12, 'п.Верхний', 'Тургенева', 14);
CALL add_med_area(8, 'п.Победы', 'Сталина', 19);

-- Modified calls with unique values for parameters 1 and 2
CALL add_patient(111111111, 111111, 'Васильев', 'Михаил', 'Андреевич', 'п.Первомайский', 'Попова', 78, 10, '1980-10-01', 894, 1);
CALL add_patient(222222222, 222222, 'Илькова', 'Анна', 'Александровна', 'п.Победы', 'Мартынова', 56, 114, '2003-10-20', 8, 11);
CALL add_patient(333333333, 333333, 'Петров', 'Иван', 'Сергеевич', 'п.Верхний', 'Омулёвского', 10, 20, '1976-08-24', 12, 10);
CALL add_patient(444444444, 444444, 'Смирнова', 'Елена', 'Александровна', 'п.Северный', 'Мартынова', 56, 114, '1985-12-03', 10, 5);
CALL add_patient(555555555, 555555, 'Антонов', 'Олег', 'Сергеевич', 'п.Северный', 'Лесная', 17, 21, '1998-05-20', 10, 5);
CALL add_patient(666666666, 666666, 'Васнецова', 'Светлана', 'Александровна', 'п.Победу', 'Омулёвского', 10, 14, '1995-02-28', 8, 11);
CALL add_patient(777777777, 777777, 'Михайлов', 'Наталья', 'Игоревна', 'п.Северный', 'Гоголя', 42, 10, '1980-04-02', 10, 5);
CALL add_patient(888888888, 888888, 'Королев', 'Артем', 'Александрович', 'п.Первомайский', 'Михайлова', 25, 894, '1991-12-11', 894,1);
CALL add_patient(999999999, 999999, 'Иванов', 'Алексей', 'Михайлович', 'п.Первомайский', 'Попова', 78, 10, '1982-05-15', 894, 1);
CALL add_patient(101010101, 101010, 'Петрова', 'Ольга', 'Андреевна', 'п.Победы', 'Мартынова', 56, 114, '2004-07-08', 8, 11);
CALL add_patient(111111111, 111111, 'Сидоров', 'Дмитрий', 'Сергеевич', 'п.Верхний', 'Омулёвского', 10, 20, '1978-03-12', 12, 10);
CALL add_patient(121212121, 121212, 'Козлов', 'Мария', 'Александровна', 'п.Северный', 'Мартынова', 56, 114, '1989-09-30', 10, 5);
CALL add_patient(131313131, 131311, 'Григорьев', 'Валентин', 'Сергеевич', 'п.Северный', 'Лесная', 17, 21, '2001-11-18', 10, 5);
CALL add_patient(141414141, 141411, 'Иванова', 'Екатерина', 'Александровна', 'п.Победу', 'Омулёвского', 10, 14, '1996-08-05', 8, 11);
CALL add_patient(151515151, 151511, 'Федоров', 'Анастасия', 'Игоревна', 'п.Северный', 'Гоголя', 42, 10, '1982-12-30', 10, 5);
CALL add_patient(161616161, 161611, 'Кузнецов', 'Дарья', 'Михайловна', 'п.Первомайский', 'Попова', 78, 10, '1985-10-03', 894, 1);
CALL add_patient(181818181, 181811, 'Александров', 'Сергей', 'Андреевич', 'п.Победы', 'Мартынова', 56, 114, '2006-02-15', 8, 11);
CALL add_patient(191919191, 191991, 'Новиков', 'Оксана', 'Сергеевна', 'п.Верхний', 'Омулёвского', 10, 20, '1980-09-28', 12, 10);
CALL add_patient(202020202, 202002, 'Антонова', 'Анна', 'Александровна', 'п.Северный', 'Мартынова', 56, 114, '1992-04-14', 10, 5);
CALL add_patient(212121212, 212112, 'Морозов', 'Денис', 'Сергеевич', 'п.Северный', 'Лесная', 17, 21, '1997-07-22', 10, 5);
CALL add_patient(222222222, 222222, 'Карпова', 'София', 'Александровна', 'п.Победу', 'Омулёвского', 10, 14, '1994-01-09', 8, 11);
CALL add_patient(232323232, 323232, 'Беляев', 'Станислав', 'Игоревич', 'п.Северный', 'Гоголя', 42, 10, '1985-06-07', 10, 5);
CALL add_patient(242424242, 244242, 'Игнатьев', 'Андрей', 'Александрович', 'п.Первомайский', 'Михайлова', 25, 894, '1989-03-18', 894, 1);

CALL add_doctor(54547, 'Михайлов', 'Антон', 'Викторович', 'окулист', 'высшая', 25500.0, NULL);
CALL add_doctor(14578, 'Голубникова', 'Инна', 'Алекасндровна', 'травматолог', 'средняя', 25500, NULL);
CALL add_doctor(54547, 'Васильева', 'Маргарита', 'Сергеевна', 'рентгенолог', 'высшая', 75000, NULL);
CALL add_doctor(89741, 'Сидоров', 'Александр', 'Игоревич', 'окулист', 'высшая', 30000, NULL);
CALL add_doctor(22222, 'Игнатьева', 'Анна', 'Александровна', 'терапевт', 'высшая', 40000, 10);
CALL add_doctor(33333, 'Петров', 'Сергей', 'Сергеевич', 'хирург', 'высшая', 50000, NULL);
CALL add_doctor(44444, 'Антонова', 'Елена', 'Александровна', 'терапевт', 'средняя', 32000, 5);
CALL add_doctor(55555, 'Козлов', 'Игорь', 'Игоревич', 'гинеколог', 'высшая', 42000, NULL);
CALL add_doctor(66666, 'Мартынов', 'Олег', 'Васильевич', 'терапевт', 'высшая', 55000, 12);
CALL add_doctor(77777, 'Григорьева', 'Наталья', 'Дмитриевна', 'окулист', 'средняя', 28000, NULL);
CALL add_doctor(88888, 'Соколов', 'Андрей', 'Александрович', 'лор', 'высшая', 48000, NULL);
CALL add_doctor(99999, 'Ильков', 'Мария', 'Васильевна', 'педиатр', 'высшая', 65000, NULL);
CALL add_doctor(101010, 'Александров', 'Артем', 'Артемович', 'маммолог', 'высшая', 38000, NULL);
CALL add_doctor(111111, 'Голубцов', 'Иван', 'Иванович', 'хирург', 'средняя', 50000, NULL);
CALL add_doctor(121212, 'Смирнова', 'Екатерина', 'Сергеевна', 'терапевт', 'высшая', 60000, NULL, 894);
CALL add_doctor(131313, 'Петрова', 'Светлана', 'Андреевна', 'окулист', 'средняя', 32000, NULL);
CALL add_doctor(141414, 'Морозов', 'Денис', 'Олегович', 'онколог', 'высшая', 42000, NULL);
CALL add_doctor(151515, 'Васнецов', 'Евгения', 'Викторовна', 'хирург', 'высшая', 55000, NULL);
CALL add_doctor(161616, 'Карпов', 'Игнат', 'Олегович', 'уролог', 'средняя', 28000, NULL);
CALL add_doctor(171717, 'Иванов', 'Дмитрий', 'Артемович', 'педиатр', 'высшая', 65000,5);
CALL add_doctor(181818, 'Беляев', 'София', 'Станиславовна', 'психотерапевт', 'высшая', 38000, NULL);
CALL add_doctor(191919, 'Новиков', 'Анна', 'Денисовна', 'ортопед', 'средняя', 48000, NULL);
CALL add_doctor(117819, 'Ложбин', 'Артём', 'Викторович', 'терапеват', 'средняя', 48000, 894);
CALL add_diagnosis('Грипп', 'острое респираторное вирусное заболевание, характеризующееся лихорадкой, ознобом, болью в мышцах, головной болью, кашлем, насморком.');
CALL add_diagnosis('Пневмония', 'острое или хроническое воспаление легких, характеризующееся лихорадкой, кашлем с мокротой, одышкой.');
CALL add_diagnosis('Сердечный приступ', 'острое нарушение кровообращения в сердечной мышце, характеризующееся сильной болью в груди, одышкой, рвотой, холодным потом.');
CALL add_diagnosis('Инсульт', 'острое нарушение кровообращения в головном мозге, характеризующееся внезапным нарушением речи, зрения, движений, сознания.');
CALL add_diagnosis('Рак', 'злокачественное новообразование, которое может поражать любые органы и ткани организма.');
CALL add_diagnosis('Аллергия', 'ненормальная реакция иммунной системы на обычно безвредные вещества.');
CALL add_diagnosis('Диабет', 'эндокринное заболевание, характеризующееся нарушением обмена веществ.');
CALL add_diagnosis('Артрит', 'воспалительное заболевание суставов.');
CALL add_diagnosis('Остеохондроз', 'дегенеративно-дистрофическое заболевание позвоночника.');
CALL add_diagnosis('Гепатит', 'воспаление печени, которое может быть вызвано вирусами, бактериями, паразитами или лекарствами.');
CALL add_diagnosis('Цироз печени', 'заболевание печени, при котором происходит замещение нормальной ткани печени соединительной тканью.');
CALL add_diagnosis('Язва желудка', 'повреждение слизистой оболочки желудка, которое может быть вызвано бактериями, стрессом, нездоровым питанием.');
CALL add_diagnosis('Язва двенадцатиперстной кишки', 'повреждение слизистой оболочки двенадцатиперстной кишки, которое может быть вызвано бактериями, стрессом, нездоровым питанием.');
CALL add_diagnosis('Кишечник', 'воспалительное заболевание кишечника, которое может быть вызвано вирусами, бактериями, паразитами или аутоиммунными заболеваниями.');
CALL add_diagnosis('ОРЗ', 'острое респираторное заболевание, характеризующееся лихорадкой, ознобом, болью в горле, кашлем, насморком.');
CALL add_diagnosis('Гипертония', 'заболевание, при котором артериальное давление превышает 140/90 мм рт. ст.');
CALL add_diagnosis('Гастрит', 'воспаление слизистой оболочки желудка.');
CALL add_diagnosis('Холецистит', 'воспаление желчного пузыря.');
CALL add_diagnosis('Цистит', 'воспаление мочевого пузыря.');
CALL add_diagnosis('Простуда', 'неинфекционное заболевание, характеризующееся лихорадкой, ознобом, болью в горле, кашлем, насморком.');
CALL add_diagnosis('Аллергический ринит', 'воспаление слизистой оболочки носа, вызванное аллергической реакцией.');
CALL add_diagnosis('Аллергический дерматит', 'воспаление кожи, вызванное аллергической реакцией.');
CALL add_diagnosis('Остеохондроз поясничного отдела позвоночника', 'дегенеративно-дистрофическое заболевание позвоночника, локализующееся в поясничном отделе.');

CALL add_goal('Общий медосмотр', 'Выявление потенциальных болезней на ранних стадиях');
CALL add_goal('Профилактические прививки', 'Получение вакцин для предотвращения инфекционных заболеваний и поддержания общественного здоровья');
CALL add_goal('Лечение инфекций', 'Диагностика и лечение различных инфекций, включая респираторные, мочевыводящие и системные инфекции');
CALL add_goal('Управление хроническими заболеваниями', 'Контроль состояний, таких как диабет, артрит, гипертония, для предотвращения осложнений');
CALL add_goal('Составление плана снижения веса', 'Разработка программы по снижению веса с учетом индивидуальных особенностей и здоровья');
CALL add_goal('Гинекологический осмотр', 'Обследование и диагностика заболеваний женских репродуктивных органов, консультирование по беременности и контрацепции');
CALL add_goal('Психотерапия и консультации психиатра', 'Лечение психических расстройств, консультации по стрессу, депрессии и тревожности');
CALL add_goal('Аллергологическое обследование', 'Идентификация аллергических реакций, разработка плана лечения и предотвращение контакта с аллергенами');
CALL add_goal('Офтальмологический осмотр', 'Обследование зрения, выявление проблем, коррекция зрения с помощью очков или контактных линз');
CALL add_goal('Лечение хронических болей', 'Поиск эффективных методов лечения хронических болей для повышения качества жизни');
CALL add_goal('Жалобы на боли', 'Обращения пациента в связи с плохим самочувствием');

CREATE OR REPLACE PROCEDURE random_add_visit(
    a_status_visit VARCHAR(50),
    a_num_ticket_param INTEGER
)
LANGUAGE plpgsql
AS $$
DECLARE
    a_id_pat INTEGER;
    a_id_doc INTEGER;
    a_id_diag INTEGER;
    a_id_goal INTEGER;
BEGIN
    IF EXISTS(SELECT * FROM Visit WHERE num_ticket = a_num_ticket_param) THEN
        RAISE EXCEPTION 'Visiting with this ticket: % already exists!', a_num_ticket_param;
    ELSE
        SELECT id_pat INTO a_id_pat FROM Patient ORDER BY RANDOM() LIMIT 1;
        SELECT id_doc INTO a_id_doc FROM Doctor ORDER BY RANDOM() LIMIT 1;
        SELECT id_diag INTO a_id_diag FROM Diagnosis ORDER BY RANDOM() LIMIT 1;
        SELECT id_goal INTO a_id_goal FROM Goal_of_visit ORDER BY RANDOM() LIMIT 1;

        INSERT INTO Visit(
            id_pat, id_doc, id_diag, id_goal,
            date_visit, status_visit, num_ticket
        )
        VALUES(
            a_id_pat,
            a_id_doc,
            a_id_diag,
            a_id_goal,
            NOW() - (RANDOM() * INTERVAL '1 year'),
            a_status_visit,
            a_num_ticket_param 
        );
    END IF;
END;
$$;

DO $$ 
DECLARE 
    i INTEGER;
    random_number INTEGER;
BEGIN 
    FOR i IN 1..100 LOOP
        -- Генерация случайного шестизначного числа
        random_number := floor(random() * (999999 - 100000 + 1) + 100000);
        CALL random_add_visit('Первичный', random_number);
        
        -- Генерация еще одного случайного шестизначного числа
        random_number := floor(random() * (999999 - 100000 + 1) + 100000);
        CALL random_add_visit('Повторный', random_number);
    END LOOP;
END; $$;

CREATE OR REPLACE FUNCTION list_of_docs()
RETURNS TABLE(
    num_area INTEGER, 
    village_area VARCHAR(50),
    street_area  VARCHAR(50),
    house_area INTEGER,
    serv_num INTEGER, 
    name_doc VARCHAR(50), 
    firstname_doc VARCHAR(50),
    specialization VARCHAR(50)
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY 
    SELECT  ma.num_area, ma.village_area, ma.street_area, ma.house_area, d.serv_num, d.name_doc, d.firstname_doc, d.specialization
    FROM Med_area ma
    JOIN Doctor d ON ma.num_area = d.num_area;
END;
$$;

CREATE OR REPLACE FUNCTION pat_with_diag(
    f_name_diagnosis VARCHAR(50)
)
RETURNS TABLE(
    num_pat INTEGER,
    name_pat VARCHAR(50),
    firstname_pat VARCHAR(50),
    diagnosis VARCHAR(50),
    num_area INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT DISTINCT p.num_medbook, p.name_pat, p.firstname_pat, di.name_diag, p.num_area
    FROM Patient p
    JOIN Visit v ON p.id_pat = v.id_pat
    JOIN Diagnosis di ON v.id_diag = di.id_diag
    WHERE di.name_diag = f_name_diagnosis;
END;
$$;

CREATE OR REPLACE FUNCTION pat_to_date(
    f_serv_num INTEGER,
    startdate DATE,
    enddate DATE
)
RETURNS TABLE(
    sev_num INTEGER,
    name_doc VARCHAR(50), 
    firstname_doc VARCHAR(50),
    specialization VARCHAR(50),
    num_medbook INTEGER,
    name_pat VARCHAR(50),
    firstname_pat VARCHAR(50),
    diagnosis VARCHAR(50),
    date_visit TIMESTAMP
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT d.serv_num, d.name_doc, d.firstname_doc, d.specialization, p.num_medbook, p.name_pat, p.firstname_pat,di.name_diag, v.date_visit
    FROM Patient p
    JOIN Visit v ON p.id_pat = v.id_pat 
    JOIN Doctor d ON d.id_doc = v.id_doc
    JOIN Diagnosis di ON v.id_diag = di.id_diag
    WHERE v.date_visit>startdate AND v.date_visit<enddate AND d.serv_num = f_serv_num;
END;
$$;

CREATE OR REPLACE FUNCTION pat_to_date(
    f_num_ticket INTEGER,
)
RETURNS TABLE(
    num_ticket INTEGER,
    date_visit TIMESTAMP,
    visit_goal VARCHAR(50),
    status_visit VARCHAR(50)
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT d.serv_num, d.name_doc, d.firstname_doc, d.specialization, p.num_medbook, p.name_pat, p.firstname_pat,v.name_diag, v.date_visit
    FROM Patient p
    JOIN Visit v ON p.id_pat = v.id_pat 
    JOIN Doctor d ON d.id_doc = v.id_doc
    WHERE v.date_visit>startdate AND v.date_visit<enddate AND d.serv_num = f_serv_num;
END;
$$;

CREATE OR REPLACE FUNCTION statistic_ticket(
    f_num_ticket INTEGER
)
RETURNS TABLE(
    num_ticket INTEGER,
    date_visit TIMESTAMP,
    doc_service_number INTEGER,
    firstname_doc VARCHAR(50),
    name_doc VARCHAR(50),
    father_name_doc VARCHAR(50),
    specialization VARCHAR(50),
    category VARCHAR(50),
    med_book_num INTEGER,
    firstname_pat VARCHAR(50),
    name_pat VARCHAR(50),
    fathername_pat VARCHAR(50),
    visit_goal VARCHAR(50),
    diagnosis VARCHAR(50),
    desc_of_diag VARCHAR(200),
    status_visit VARCHAR(50)
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT v.num_ticket, v.date_visit, d.serv_num, d.firstname_doc, d.name_doc, d.father_name,
    d.specialization, d.category, p.num_medbook, p.firstname_pat, p.name_pat, p.fathername_pat,
    g.name_goal, di.name_diag, di.desc_diag, v.status_visit
    FROM Visit v
    JOIN Patient p ON p.id_pat = v.id_pat 
    JOIN Doctor d ON d.id_doc = v.id_doc
    JOIN Diagnosis di ON di.id_diag = v.id_diag
    JOIN Goal_of_visit g ON g.id_goal = v.id_goal
    WHERE v.num_ticket = f_num_ticket;
END;
$$;

