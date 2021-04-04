CREATE DATABASE `adrow_time_record`;

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `fullname` longtext NOT NULL,
  `create_date` datetime NOT NULL
);

CREATE TABLE `time_records` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `record_date` datetime NOT NULL,
  `action` varchar(10) NOT NULL
);

ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `time_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `time_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `time_records`
  ADD CONSTRAINT `time_records_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;