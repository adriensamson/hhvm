<?php
	$test_table_name = 'test_bug44897_table_1'; require_once("table.inc");

	if (!$link->query('DROP PROCEDURE IF EXISTS test_bug44897_procedure_1'))
		printf("[001] [%d] %s\n", $link->errno, $link->error);

	if (!$link->query('CREATE PROCEDURE test_bug44897_procedure_1(IN new_id INT, IN new_label CHAR(1)) BEGIN INSERT INTO test_bug44897_table_1(id, label) VALUES (new_id, new_label); SELECT new_label; END;'))
		printf("[002] [%d] %s\n", $link->errno, $link->error);

	$new_id = 100;
	$new_label = 'z';

	if (!$stmt = $link->prepare('CALL test_bug44897_procedure_1(?, ?)'))
		printf("[003] [%d] %s\n", $link->errno, $link->error);

	if (!$stmt->bind_param('is', $new_id, $new_label) || !$stmt->execute())
		printf("[004] [%d] %s\n", $stmt->errno, $stmt->error);

	$out_new_label = null;
	if (!$stmt->bind_result($out_new_label) || !$stmt->fetch())
		printf("[005] [%d] %s\n", $stmt->errno, $stmt->error);

	if ($out_new_label != $new_label)
		printf("[006] IN value and returned value differ. Expecting %s/%s got %s/%s\n",
			$new_label, gettype($new_label), $out_new_label, gettype($out_new_label));

	$stmt->close();

	$stmt2 = $link->prepare('SELECT label FROM test_bug44897_table_1 WHERE id = ?');
	if (!is_object($stmt2)) {

		printf("[007] Failed to create new statement object, [%d] %s\n",
			$link->errno, $link->error);

	} else {

		if (!$stmt2->bind_param("i", $new_id) || !$stmt2->execute())
			printf("[008] [%d] %s\n", $stmt2->errno, $stmt2->error);

		$out_new_label = null;
		if (!$stmt2->bind_result($out_new_label) || !$stmt2->fetch())
			printf("[009] [%d] %s\n", $stmt2->errno, $stmt2->error);

		if ($out_new_label != $new_label)
			printf("[010] IN value and returned value differ. Expecting %s/%s got %s/%s\n",
				$new_label, gettype($new_label), $out_new_label, gettype($out_new_label));

	}

	$link->close();

	print "done!";
?>
<?php
require_once("connect.inc");
if (!$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket))
   printf("[c001] [%d] %s\n", mysqli_connect_errno(), mysqli_connect_error());

if (!mysqli_query($link, "DROP TABLE IF EXISTS test_bug44897_table_1"))
	printf("[c002] Cannot drop table, [%d] %s\n", mysqli_errno($link), mysqli_error($link));

mysqli_query($link, "DROP PROCEDURE IF EXISTS test_bug44897_procedure_1");

mysqli_close($link);
?>