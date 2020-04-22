-- in this example we truncate also before each test
TRUNCATE TABLE `test`.`ephemeral_table`;

INSERT INTO `test`.`persistent_table` (`id`) VALUES (NULL);